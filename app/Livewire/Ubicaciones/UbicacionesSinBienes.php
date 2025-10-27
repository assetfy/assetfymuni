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
use App\Models\EmpresasModel;

class UbicacionesSinBienes extends LivewireTable
{
    protected string $model = UbicacionesModel::class;
    public $title = ''; // Nombre del encabezado
    public $createForm = 'crearUbicaciones'; //  Nombre de la funcion que llama al evento
    public $origen = 'ubicaciones_delegadas'; // Identificador de la vista    
    public $editForm = ''; // Nombre del componente de edición predeterminado
    #[Locked]
    public $userId;
    public $usuarios_empresas, $atributos, $tipoEmpresa;

    public function asignar()
    {
        $this->userId = IdHelper::identificador();
        $this->tipoEmpresa = $this->empresa();
        $this->title = $this->tipoEmpresa == 2 ? 'Ubicaciones Clientes' : 'Mis Ubicaciones';
    }

    protected function query(): Builder
    {
        $this->abrirModal();
        $this->asignar();

        $user = auth()->user();
        if ($user->panel_actual == 'Usuario') {
            $query = $this->model()::query()
                ->where('cuil', '=', $this->userId)
                ->where('propiedad', '=', 'Propio');
        } else {
            $this->atributos = RouteAttributesHelper::getRouteAttributes();

            if ($this->tipoEmpresa == 2) {
                $query = $this->model()::query()
                    ->where('cuit_empresa', '=', $this->userId)
                    ->whereNotNull('cuit')
                    ->where('propiedad', '=', 'Cliente')
                    ->whereDoesntHave('activos');
            } else {
                $query = $this->model()::query()
                    ->where('cuit', '=', $this->userId)
                    ->whereDoesntHave('activos');
            }
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
        $columns = [];

        // Columnas comunes a todos los tipos de empresa
        $columns[] = Column::make(__('Nombre'), 'nombre')->sortable()->searchable();
        $columns[] = Column::make(__('País'), 'pais')->sortable()->searchable();
        $columns[] = Column::make(__('Provincia'), 'provincia')->sortable()->searchable();
        $columns[] = Column::make(__('Ciudad'), 'ciudad')->sortable()->searchable();
        $columns[] = Column::make(__('Código Postal'), 'codigo_postal')->sortable()->searchable();
        $columns[] = Column::make(__('Calle'), 'calle');
        $columns[] = Column::make(__('Altura'), 'altura');
        $columns[] = Column::make(__('Piso'), 'piso');
        $columns[] = Column::make(__('Dpto'), 'depto');

        // Si es tipoEmpresa == 1, agregar columna de acciones al final
        if ($this->tipoEmpresa == 1) {
            $columns[] = Column::make(__('Acciones'), function (Model $model): string {
                return '<button wire:click="$dispatch(\'abrirModalEditar\', { data: ' . $model->getKey() . ' })" 
                        class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                        Editar
                    </button>';
            })
                ->clickable(false)
                ->asHtml();
        }

        return $columns;
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


    private function empresa()
    {
        return EmpresasModel::where('cuit', $this->userId)
            ->pluck('tipo')
            ->first();
    }

    public function crearUbicaciones()
    {
        $this->dispatch('crearUbicacion', origen: $this->origen)->to('ubicaciones.crear-ubicaciones');
    }
}
