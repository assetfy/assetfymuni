<?php

namespace App\Livewire\Empresas;

use RamonRietdijk\LivewireTables\Livewire\LivewireTable;
use RamonRietdijk\LivewireTables\Columns\DateColumn;
use RamonRietdijk\LivewireTables\Columns\Column;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Livewire\Attributes\Locked;
use App\Models\AuditoriasModel;

class Estado extends LivewireTable
{
    protected string $model = AuditoriasModel::class;
    #[Locked]
    public int $userId;
    public $title = 'Auditoria'; // Nombre del emcabezado
    public $createForm = ''; // Nombre del componente de creación predeterminado
    public $editForm = 'empresas.detalle-estado'; // Nombre del componente de edición predeterminado
    protected bool $useSelection = false;

    public function asignar(){
        $this->userId= auth()->user()->id;
    }

     /** @return Builder<Model> */
     protected function query(): Builder
     {
        $this->asignar();
        return $this->model()->where('id_usuario', '=', $this->userId);
     }

     protected function columns(): array
     {
         return [
             Column::make(__('Cuit'), 'cuit')
             ->sortable()
             ->searchable(),
             Column::make(__('Razon Social'), 'razon_social')
             ->sortable()
             ->searchable(),
             Column::make(__('Estado'), 'estado'),
             DateColumn::make(__('Fecha'), 'fecha_creacion')
            ->format('d m Y'),
         ];  
     }
}