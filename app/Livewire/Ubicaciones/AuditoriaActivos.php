<?php

namespace App\Livewire\Ubicaciones;

use RamonRietdijk\LivewireTables\Livewire\LivewireTable;
use RamonRietdijk\LivewireTables\Columns\DateColumn;
use RamonRietdijk\LivewireTables\Columns\Column;
use App\Models\AuditoriaUbicacionActivoModel;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Session;
class AuditoriaActivos extends LivewireTable
{
    protected string $model = AuditoriaUbicacionActivoModel::class;
    public $title = 'Movimientos Del Activo'; // Nombre del encabezado
    public $createForm = ''; // Nombre del componente de creación predeterminado
    public $editForm = ''; // Nombre del componente de edición predeterminado
    protected $cuit, $empresa, $valor, $identificadores;
    public $previousUrl;
    protected bool $useSelection = false;

    public function mount(): void
    {
        parent::mount();
        $this->initializeActivo();
        $this->previousUrl = Session::get('previous_url', url()->previous());
    }

    protected function initializeActivo(): void
    {
        $id_activo = request()->route('id_activo'); // O donde obtengas este ID
        if ($id_activo) {
            Session::put('activo', $id_activo);
        }
    }

    protected function query(): Builder
    {
        $activo = Session::get('activo');
        $query = $this->model()->query()->where('id_activo', '=', $activo);
        return $query;
    }

    protected function columns(): array
    {
        return [
            Column::make(__('Ubicación Actual'), 'ubicacionActual.nombre')
                ->sortable()
                ->searchable(),
            Column::make(__('Activo'), 'id_activo')
                ->sortable()
                ->searchable(),
            Column::make(__('Trasladado'), 'ubicacionTrasladada.nombre')
                ->sortable()
                ->searchable(),
            DateColumn::make(__('Fecha'), 'fecha')
                ->format('Y-m-d H:i'),
            Column::make(__('Usuario'), 'usuario.name')
                ->sortable()
                ->searchable(),
        ];
    } 
}
