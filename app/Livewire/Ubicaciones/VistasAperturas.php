<?php

namespace App\Livewire\Ubicaciones;
use RamonRietdijk\LivewireTables\Livewire\LivewireTable;
use RamonRietdijk\LivewireTables\Columns\Column;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\Locked;
use Illuminate\Support\Facades\Session;
use App\Models\UbicacionesModel;
use App\Helpers\IdHelper;

class VistasAperturas extends LivewireTable
{
    protected string $model =UbicacionesModel::class;
    public $title = 'Aperturas'; // Nombre del emcabezado
    public $createForm = ''; // Nombre del componente de creación predeterminado
    public $editForm = ''; // Nombre del componente de edición predeterminado
    public $userId ;
    protected bool $useSelection = false;

   

    public function asignar(){
        $this->userId = IdHelper::identificador();
    }

    protected function query(): Builder
    {
        $this->asignar();
        $query = $this->model()->query()->where('cuit', '=', $this->userId);
        // Si no se encuentra ningún resultado usando cuit, busca por cuil
        if ($query->count() === 0) {
            $query = $this->model()->query()->where('cuil', '=', $this->userId);
            }

        return $query;
        
    }

    protected function columns(): array
    {
        return [
            Column::make(__('Ubicacion'), 'nombre')
            ->sortable()
            ->searchable(),
            Column::make(__('Apertura 1'), 'apertura1.nombre'),
            Column::make(__('Apertura 2'), 'apertura2.nombre'),
            Column::make(__('Apertura 3'), 'apertura3.nombre'),
            Column::make(__('Apertura 4'), 'apertura4.nombre'),
        ];  
    }

    public function isSelectable($row): bool
    {
        // Lógica específica de selección por fila
        return false;
    }
}
 