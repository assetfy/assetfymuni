<?php

namespace App\Livewire\Atributos\ActivosAtributos;

use RamonRietdijk\LivewireTables\Livewire\LivewireTable;
use RamonRietdijk\LivewireTables\Filters\SelectFilter;
use RamonRietdijk\LivewireTables\Columns\Column;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Session;
use App\Models\ActivosAtributosModel;


class ActivosAtributos extends LivewireTable
{
    protected string $model = ActivosAtributosModel::class;
    public $title = 'ATRIBUTOS'; // Nombre del emcabezado
    public $createForm = ''; // Nombre del componente de creación predeterminado
    public $editForm = ''; // Nombre del componente de edición predeterminado
    public $previousUrl;
    protected bool $useSelection = false;

    // Método mount compatible con la clase padre
    public function mount(): void
    {
        parent::mount(); // Llama al método mount de la clase padre si es necesario
        $this->initializeActivo();

        $this->previousUrl = Session::get('previous_url', url()->previous());
    }

    // Método adicional para la lógica de inicialización personalizada
    protected function initializeActivo(): void
    {
        // Aquí puedes obtener y establecer cualquier valor necesario
        // Por ejemplo, establecer el activo en la sesión
        $id_activo = request()->route('id_activo'); // O donde obtengas este ID
        // $model = SolicitudesServiciosModel::find($id_activo);
        if ($id_activo) {
            Session::put('activo', $id_activo);
        }
    }
    
    protected function query(): Builder
    {
        $activo = Session::get('activo');
        $query = $this->model()->query()->where('id_activo', '=', $activo);
        // Si no se encuentra ningún resultado usando cuit, busca por cuil
        return $query;
    }

    protected function columns(): array
     {
         return [
             Column::make(__('Atributo'), 'atributo.nombre')
             ->searchable(),
             Column::make(__('Subcategoria'), 'subcategoria.nombre'),
             Column::make(__('Categoria'), 'categoria.nombre'),
             Column::make(__('Tipo'), 'tipo.nombre'),
             Column::make(__('Campo'), 'campo'),
             Column::make(__('Campo Numerico'), 'campo_numerico'),
             
         ];  
     }

     protected function filters(): array
     {
         return [
            SelectFilter::make(__('Categoria'), 'id_categoria')
                ->options($this->getCategorias()),
            SelectFilter::make(__('Subcategoria'), 'id_subcategoria')
                ->options($this->getSubcategoria())
         ];
     }
 
     protected function getTipos()
     {
         $tiposConCategorias = ActivosAtributosModel::has('tipo')->get();
 
         $options = $tiposConCategorias->pluck('tipo.nombre', 'tipo.id_tipo')->toArray();
 
         return $options;
     }
 
     protected function getCategorias()
     {
         $categorias = ActivosAtributosModel::has('categoria')->get();
 
         $values = $categorias->pluck('categoria.nombre', 'categoria.id_tipo')->toArray();
 
         return $values;
     }

     protected function getSubcategoria()
     {
         $categorias = ActivosAtributosModel::has('subcategoria')->get();
 
         $values = $categorias->pluck('subcategoria.nombre', 'subcategoria.id_subcategoria')->toArray();
 
         return $values;
     }

     public function isSelectable($row): bool
     {
         // Lógica específica de selección por fila
         return false;
     }
}