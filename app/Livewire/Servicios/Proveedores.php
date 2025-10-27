<?php

namespace App\Livewire\Servicios;

use App\Models\EmpresasModel;
use App\Models\MisProveedoresModel;
use RamonRietdijk\LivewireTables\Livewire\LivewireTable;
use RamonRietdijk\LivewireTables\Columns\Column;
use RamonRietdijk\LivewireTables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Http;
use App\Helpers\IdHelper;

class Proveedores extends LivewireTable
{
    protected string $model = EmpresasModel::class;
    public $title = 'Proveedores'; // Nombre del encabezado
    public $createForm = ''; // Nombre del componente de creación predeterminado
    protected bool $useSelection = false;

    protected $listeners = ['guardarFav'];

    public $url, $places, $rating, $userRatingsTotal, $userId;

    public $filtroProvincia;
    public $filtroLocalidad;
    public $calificaciones = [];

    protected function query(): Builder
    {
        $this->userId = IdHelper::identificador();

        return EmpresasModel::query()
            ->where('tipo', '2')
            ->where('cuit', '!=', $this->userId) // Para no mostrar la empresa del mismo usuario para ser agregada a favoritos
            ->whereNotIn('cuit', function ($subQuery) {
                $subQuery->select('cuit')
                    ->from('act.mis_proveedores_favoritos')
                    ->where('id_usuario', auth()->id())
                    ->where('empresa', $this->userId);
            })
            ->when($this->filtroProvincia, function ($query) {
                $query->where('provincia', $this->filtroProvincia);
            })
            ->when($this->filtroLocalidad, function ($query) {
                $query->where('localidad', $this->filtroLocalidad);
            });
    }

    protected function columns(): array
    {
        return [
            Column::make(__('Acciones'), function (Model $model) {
                // Botón para agregar a favoritos – Verde pastel
                $botonFavorito = '
                <button 
                    wire:click="agregarFav(\'' . $model->cuit . '\')" 
                    style="background-color: #BBF7D0;" 
                    class="text-green-800 font-bold py-2 px-4 rounded flex items-center justify-center w-12 h-12"
                    title="Agregar a Favoritos"
                >
                    <i class="fa-solid fa-star text-base"></i>
                </button>
            ';

                // Botón para detalles del proveedor – Gris pastel con ícono
                $botonDetalles = '
                <button 
                    wire:click="$dispatch(\'PrestadoraDetalle\', { data: ' . $model->getKey() . ' })" 
                    title="Detalles del Proveedor"
                    style="background-color: #E5E7EB;"
                    class="text-gray-800 font-bold py-2 px-4 rounded flex items-center justify-center w-12 h-12"
                >
                    <i class="fa-solid fa-address-book text-base"></i>
                </button>
            ';

                return '
                <div class="flex space-x-2">
                    ' . $botonFavorito . '
                    ' . $botonDetalles . '
                </div>
            ';
            })->asHtml(),

            Column::make(__('Razón Social'), 'razon_social')
                ->sortable()
                ->searchable()
                ->displayUsing(function ($value, $row) {
                    // Obtener la calificación de Google
                    $calificacion = $this->getCalificacion($row);

                    if ($calificacion && isset($calificacion['rating'])) {
                        $rating = $calificacion['rating'];
                        $userRatingsTotal = $calificacion['user_ratings_total'] ?? 0;

                        $fullStars = floor($rating);  // Número de estrellas llenas
                        $hasHalfStar = ($rating - $fullStars) >= 0.5;
                        $totalStars = $fullStars + ($hasHalfStar ? 1 : 0);
                        $emptyStars = 5 - $totalStars; // Completar con estrellas grises

                        // Construcción de las estrellas en HTML
                        $estrellasHtml = '';

                        for ($i = 0; $i < $fullStars; $i++) {
                            $estrellasHtml .= '<i class="fas fa-star text-yellow-500"></i>'; // Estrella llena
                        }
                        if ($hasHalfStar) {
                            $estrellasHtml .= '<i class="fas fa-star-half-alt text-yellow-500"></i>'; // Media estrella
                        }
                        for ($j = 0; $j < $emptyStars; $j++) {
                            $estrellasHtml .= '<i class="far fa-star text-gray-400"></i>'; // Estrella vacía en gris
                        }

                        // Texto con la calificación numérica y cantidad de reseñas
                        $calificacionTexto = '<div class="flex items-center space-x-2">
                        <span class="text-blue-500 font-semibold">Asset-fy</span> 
                        <span class="text-gray-700"> / </span> 
                        <span class="flex">' . $estrellasHtml . '</span> 
                        <span class="ml-2 text-gray-700">' . number_format($rating, 1) . ' (' . $userRatingsTotal . ') 
                        <span class="text-red-500 font-semibold">Google</span></span>
                    </div>';
                    } else {
                        // Mostrar 5 estrellas grises cuando no hay calificación
                        $estrellasHtml = str_repeat('<i class="far fa-star text-gray-400"></i>', 5);
                        $calificacionTexto = '<div class="flex items-center space-x-2">
                        <span class="text-blue-500 font-semibold">Asset-fy</span> 
                        <span class="text-gray-700"> / </span> 
                        <span class="flex">' . $estrellasHtml . '</span> 
                        <span class="ml-2 text-gray-700">(Sin reseñas) 
                        <span class="text-red-500 font-semibold">Google</span></span>
                    </div>';
                    }

                    return $value . '<br>' . $calificacionTexto;
                })
                ->asHtml(),
            Column::make(__('CUIT'), 'cuit'),
            Column::make(__('Localidad'), 'localidad')
                ->sortable()
                ->searchable(),
            Column::make(__('Provincia'), 'provincia')
                ->sortable()
                ->searchable(),
            Column::make(__('Actividad'), function ($model) {
                return $model->actividades ? $model->actividades->nombre : 'Sin actividad';
            })
                ->sortable()
                ->searchable(),
        ];
    }

    protected function getCalificacion($row)
    {
        // Llamada a la API de Google para obtener las calificaciones si existe dato en 'places`
        if (!empty($row->places)) {
            return $this->getReviews($row->places);
        }

        return null; // Si no tiene calificación, devolver null
    }

    public function agregarFav($cuit)
    {
        $empresa = EmpresasModel::find($cuit);
        if ($empresa) {
            // Despachamos un evento de navegador con los datos que usaremos para la creación.
            $this->dispatch('checkCargarContrato', [
                'cuit'         => $empresa->cuit,
                'razon_social' => $empresa->razon_social,
                'localidad'    => $empresa->localidad,
                'provincia'    => $empresa->provincia,
                'id_usuario'   => auth()->user()->id,
                'empresa'      => $this->userId, // o IdHelper::idEmpresa() si conviene
            ]);
        } else {
            $this->dispatch('notificacion', 'No se encontró la empresa.');
        }
    }

    public function guardarFav($data)
    {
        // Aquí $data vendrá del JS (pasado como objeto), asegúrate de que contenga los mismos keys.
        // Por ejemplo:
        $registro = MisProveedoresModel::create([
            'existe_en_la_plataforma' => 'Si',
            'cuit'         => $data['cuit'],
            'razon_social' => $data['razon_social'],
            'localidad'    => $data['localidad'],
            'provincia'    => $data['provincia'],
            'id_usuario'   => $data['id_usuario'],
            'empresa'      => $data['empresa'],
        ]);
        $this->dispatch('lucky', 'Proveedor agregado a favoritos.');
    }

    protected function filters(): array
    {
        return [
            SelectFilter::make(__('Provincia'), 'provincia')->options($this->getProvincias()),
            SelectFilter::make(__('Localidad'), 'localidad')->options($this->getLocalidades()),
        ];
    }

    protected function getProvincias(): array
    {
        return EmpresasModel::where('tipo', '2')
            ->whereNotIn('cuit', function ($subQuery) {
                $subQuery->select('cuit')
                    ->from('act.mis_proveedores_favoritos')
                    ->where('id_usuario', auth()->id())
                    ->where('empresa', $this->userId);
            })
            ->distinct()
            ->pluck('provincia', 'provincia')
            ->toArray();
    }

    protected function getLocalidades(): array
    {
        return EmpresasModel::where('tipo', '2')
            ->whereNotIn('cuit', function ($subQuery) {
                $subQuery->select('cuit')
                    ->from('act.mis_proveedores_favoritos')
                    ->where('id_usuario', auth()->id())
                    ->where('empresa', $this->userId);
            })
            ->distinct()
            ->pluck('localidad', 'localidad')
            ->toArray();
    }

    public function getReviews($placeId)
    {
        $apiKey = config('services.google_maps.api_key');
        $fields = 'rating,user_ratings_total';

        // Usamos withoutVerifying() para evitar la verificación SSL
        $response = Http::withoutVerifying()->get('https://maps.googleapis.com/maps/api/place/details/json', [
            'key'      => $apiKey,
            'place_id' => $placeId,
            'fields'   => $fields,
        ]);

        if ($response->successful()) {
            $data = $response->json();

            // Si no hay 'result' o está vacío, devolvemos valores por defecto
            if (!isset($data['result']) || empty($data['result'])) {
                return ['rating' => null, 'user_ratings_total' => 0];
            }

            $result = $data['result'];

            // Asegurarnos de que rating y user_ratings_total estén presentes
            $rating           = isset($result['rating']) && $result['rating'] !== '' ? $result['rating'] : null;
            $userRatingsTotal = isset($result['user_ratings_total']) && $result['user_ratings_total'] !== ''
                ? $result['user_ratings_total']
                : 0;

            return [
                'rating'             => $rating,
                'user_ratings_total' => $userRatingsTotal,
            ];
        }

        // En caso de error en la petición
        return ['rating' => null, 'user_ratings_total' => 0];
    }
}
