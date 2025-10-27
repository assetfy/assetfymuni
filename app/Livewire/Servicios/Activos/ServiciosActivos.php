<?php

namespace App\Livewire\Servicios\Activos;

use RamonRietdijk\LivewireTables\Livewire\LivewireTable;
use RamonRietdijk\LivewireTables\Filters\SelectFilter;
use RamonRietdijk\LivewireTables\Columns\ImageColumn;
use RamonRietdijk\LivewireTables\Columns\DateColumn;
use RamonRietdijk\LivewireTables\Filters\DateFilter;
use RamonRietdijk\LivewireTables\Columns\Column;
use App\Models\SolicitudesServiciosModel;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;
use App\Models\EmpresasModel;
use App\Helpers\IdHelper;

class ServiciosActivos extends LivewireTable
{
    protected string $model = SolicitudesServiciosModel::class;
    public $title = 'SERVICIOS SOLICITADOS PARA EL ACTIVO'; // Nombre del emcabezado
    public $createForm = ''; // Nombre del componente de creación predeterminado
    public $editForm = ''; // Nombre del componente de edición predeterminado
    protected $cuit, $empresa, $valor, $identificadores;
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

    public function asignar()
    {
        $identificadores = IdHelper::identificadoresCompletos();
        $this->cuit = $identificadores['cuit'];

        if($this->cuit == null){
            $this->valor = Auth::user()->cuil;
        } else {
            $this->valor = $this->cuit;
        }

        $this->empresa = EmpresasModel::where('cuit', $this->valor)->first();
    }

    protected function columns(): array
    {
        $this->asignar();

        return $this->empresa && isset($this->empresa->cuit) ? $this->columnsEmpresa() : $this->columnsUsuarios();
    }

    protected function commonColumns(): array
    {
        return [
            ImageColumn::make(__('Foto'), 'foto')
                ->size(75, 75),
            Column::make(__('Servicio'), 'servicios.nombre')
                ->searchable(),
            Column::make(__('Tipo'), 'tipos.nombre'),
            Column::make(__('Categoria'), 'categorias.nombre'),
            Column::make(__('Subcategoria'), 'subcategorias.nombre'),
            DateColumn::make(__('Fecha'), 'fechaHora')
                ->format('Y-m-d'),
            Column::make(__('Descripcion'), 'descripcion'),
            Column::make(__('Estado'), 'estado'),
            Column::make(__('Empresa Prestadora'), 'empresasPrestadora.razon_social'),
        ];
    }
    
    protected function columnsEmpresa(): array
    {
        $columns = $this->commonColumns();
        $columns[] = Column::make(__('Empresa Solicitante'), 'empresasSolicitantes.razon_social');
        return $columns;
    }
    
    protected function columnsUsuarios(): array
    {
        $columns = $this->commonColumns();
        $columns[] = Column::make(__('Solicitante'), 'users.name');
        return $columns;
    }    

    protected function filters(): array
    {
        return [
           SelectFilter::make(__('Servicios'), 'id_servicio')
               ->options($this->getServicios()),
           DateFilter::make(__('Fecha'), 'fechaHora'),
        ];
    }

    protected function getServicios()
    {
        $solicitudes = SolicitudesServiciosModel::has('servicios')->get();

        $options = $solicitudes->pluck('servicios.nombre', 'servicios.id_servicio')->toArray();

        return $options;
    }

    public function isSelectable($row): bool
    {
        // Lógica específica de selección por fila
        return false;
    }
}