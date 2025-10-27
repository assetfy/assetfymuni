<?php

namespace App\Livewire\Ubicaciones;

use App\Exports\UbicacionesExport;
use App\Helpers\IdHelper;
use App\Models\UbicacionesModel;
use RamonRietdijk\LivewireTables\Livewire\LivewireTable;
use RamonRietdijk\LivewireTables\Actions\Action;
use RamonRietdijk\LivewireTables\Columns\Column;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Enumerable;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Locked;
use Maatwebsite\Excel\Facades\Excel;

class UbicacionesDelegadas extends LivewireTable
{
    protected string $model = UbicacionesModel::class;
    public $title = 'Ubicaciones Clientes'; // Nombre del encabezado

    public $createForm = null; // Inicialmente vacío
    public $origen = 'ubicaciones_delegadas'; // Identificador de la vista    

    public $editForm = ''; // Nombre del componente de edición predeterminado
    #[Locked]
    public $userId;
    public $usuarios_empresas;

    private function asignar()
    {
        $this->userId = IdHelper::idEmpresa();
    }

    public function hydrate()
    {
        // Obtener el ID del usuario autenticado
        $usuarioId = auth()->id();

        // 🔍 Verificar si el usuario es Apoderado
        $esApoderado = \App\Models\UsuariosEmpresasModel::where('id_usuario', $usuarioId)
            ->where('cuit', $this->userId)
            ->where('cargo', 'Apoderado')
            ->exists();

        $idsRol = \App\Models\RolesModel::where('nombre', 'Admin Empresa Prestadora')
            ->pluck('id_rol');

        $admin = \App\Models\AsignacionesRolesModel::where('usuario_empresa', Auth::user()->id)
            ->whereIn('id_rol', $idsRol)
            ->where('cuit', IdHelper::idEmpresa())
            ->exists();

        // Solo permitir el botón si el usuario es Apoderado
        if ($esApoderado || $admin) {
            $this->createForm = 'crearUbicaciones';
        } else {
            $this->createForm = null;
        }
    }

    protected function query(): Builder
    {
        $this->asignar();
        $this->dispatch('openModal', ['ubicaciones.crear-ubicaciones']);
        $query = $this->model()->query()
            ->where('cuit_empresa', '=', $this->userId)
            ->whereHas('activos');
        return $query;
    }

    protected function columns(): array
    {
        return [
            Column::make(__('Empresa'), 'empresas.razon_social',)
                ->sortable()
                ->searchable(),
            Column::make(__('CUIT'), 'empresas.cuit',)
                ->sortable()
                ->searchable(),
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

    public function crearUbicaciones()
    {
        $this->dispatch('crearUbicacion', origen: $this->origen)->to('ubicaciones.crear-ubicaciones');
    }
}
