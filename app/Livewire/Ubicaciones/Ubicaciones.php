<?php

namespace App\Livewire\Ubicaciones;

use RamonRietdijk\LivewireTables\Livewire\LivewireTable;
use RamonRietdijk\LivewireTables\Filters\SelectFilter;
use RamonRietdijk\LivewireTables\Actions\Action;
use RamonRietdijk\LivewireTables\Columns\Column;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Enumerable;
use App\Exports\UbicacionesExport;
use App\Models\UbicacionesModel;
use Livewire\Attributes\Locked;
use App\Helpers\IdHelper;
use App\Helpers\RouteAttributesHelper;

class Ubicaciones extends LivewireTable
{
    protected string $model = UbicacionesModel::class;
    public $title = 'Mis Ubicaciones'; // Nombre del encabezado
    public $createForm = 'crearubicacion'; //  Nombre de la funcion que llama al evento
    public $editForm = ''; // Nombre del componente de edición predeterminado
    #[Locked]
    public $userId;
    public $usuarios_empresas, $atributos, $modalDispatched;

    public function hydrate(): void
    {
        if (! $this->modalDispatched) {
            $this->dispatch('openModal', ['ubicaciones.crear-ubicaciones', 'ubicaciones.editar-ubicaciones']);
            $this->modalDispatched = true;
        }
    }

    protected function query(): Builder
    {
        $user = auth()->user();
        if ($user->panel_actual == 'Usuario') {
            $query = $this->model()::query()
                ->where('cuil', '=', IdHelper::identificador())
                ->where('propiedad', '=', 'Propio');
        } else {
            $this->atributos = RouteAttributesHelper::getRouteAttributes();

            $query = $this->model()::query()
                ->where('cuit', '=',  IdHelper::empresaActual()->cuit)
                ->whereHas('activos');
            // Si $this->atributos no es null ni está vacía, se agrega el filtro para id_tipo
            if (!empty($this->atributos) && collect($this->atributos)->isNotEmpty()) {
                $valores = collect($this->atributos)->filter()->all();
                // Si se esperan múltiples valores, usa whereIn
                $query->whereIn('tipo', $valores);
            }
        }

        return $query;
    }

    public function abrirModal()
    {
        $this->dispatch('reinicializarMapa');
    }

    protected function columns(): array
    {
        return [
            Column::make(__('Acciones'), function (Model $model): string {
                return sprintf(
                    '<button 
                        wire:click="$dispatch(\'abrirModalEditar\', { data: %d })" 
                        style="background-color: #C7D2FE;"
                        class="text-indigo-800 font-bold py-2 px-4 rounded flex items-center justify-center w-12 h-12 transition"
                        title="Editar">
                        <i class="fa-solid fa-pen-to-square text-base"></i>
                    </button>',
                    $model->getKey()
                );
            })
                ->clickable(false)
                ->asHtml(),
            Column::make(__('Nombre'), 'nombre')
                ->sortable()
                ->searchable(),
            Column::make(__('País'), 'pais')
                ->sortable()
                ->searchable(),
            Column::make(__('Provincia'), 'provincia')
                ->sortable()
                ->searchable(),
            Column::make(__('Ciudad'), 'ciudad')
                ->sortable()
                ->searchable(),
            Column::make(__('Código Postal'), 'codigo_postal')
                ->sortable()
                ->searchable(),
            Column::make(__('Calle'), 'calle'),
            Column::make(__('Altura'), 'altura'),
            Column::make(__('Piso'), 'piso'),
            Column::make(__('Dpto'), 'depto'),
        ];
    }

    protected function filters(): array
    {
        return [
            SelectFilter::make(__('Tipo'), 'tipo')
                ->options($this->getTipos()),
        ];
    }

    protected function getTipos()
    {
        $tipos = UbicacionesModel::has('tiposUbicaciones')->get();

        $options = $tipos->pluck('tiposUbicaciones.nombre', 'tiposUbicaciones.id_tipo')->toArray();

        return $options;
    }

    protected function actions(): array
    {
        return [
            Action::make(__('Exportar'), 'export', function (Enumerable $models): mixed {
                return Excel::download(
                    new UbicacionesExport($models),
                    'Ubicaciones.xlsx'
                );
            }),
        ];
    }

    public function crearubicacion()
    {
        $this->dispatch('crearUbicacion')->to('ubicaciones.crear-ubicaciones');
    }
}
