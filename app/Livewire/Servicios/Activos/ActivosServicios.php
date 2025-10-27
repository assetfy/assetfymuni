<?php

namespace App\Livewire\Servicios\Activos;

use RamonRietdijk\LivewireTables\Livewire\LivewireTable;
use RamonRietdijk\LivewireTables\Columns\ImageColumn;
use RamonRietdijk\LivewireTables\Columns\DateColumn;
use RamonRietdijk\LivewireTables\Columns\Column;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Session;
use App\Models\ServiciosActivosModel;
use Illuminate\Support\Facades\Auth;
use App\Models\EmpresasModel;
use App\Helpers\IdHelper;

class ActivosServicios extends LivewireTable
{
    protected string $model = ServiciosActivosModel::class;
    public $title = 'Servicios realizados al activo';
    public $createForm = '';
    public $editForm = '';
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
        $query = $this->model()->query()->where('act.servicios_activos.id_activo', '=', $activo);
        return $query;
    }

    public function asignar()
    {
        $identificadores = IdHelper::identificadoresCompletos();
        $this->cuit = $identificadores['cuit'];

        if ($this->cuit == null) {
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
            Column::make(__('Foto'), function (Model $model): string {
                return  '<a href="' . asset(str_replace('public/', '',  $model->foto)) . '" target="_blank" 
                     style="color: blue; text-decoration: underline;">Ver Imagen</a>';
            })->clickable(false)
                ->asHtml(),
            Column::make(__('Servicio'), 'servicios.nombre')
                ->searchable(),
            Column::make(__('Empresa Prestadora'), 'empresas.razon_social'),
            Column::make(__('Tipo'), 'tipos.nombre'),
            Column::make(__('Categoria'), 'categorias.nombre'),
            Column::make(__('Subcategoria'), 'subcategorias.nombre'),
            Column::make(__('Activo'), 'activos.nombre'),
            Column::make(__('Descripcion'), 'comentarios'),
            Column::make(__('Estado'), 'estado'),
            DateColumn::make(__('Fecha'), 'fecha')
                ->format('Y-m-d - H:i'),
        ];
    }

    protected function columnsEmpresa(): array
    {
        $columns = $this->commonColumns();
        $columns[] = Column::make(__('Empresa Solicitante'), 'empresas.razon_social');
        return $columns;
    }

    protected function columnsUsuarios(): array
    {
        $columns = $this->commonColumns();
        $columns[] = Column::make(__('Solicitante'), 'users.name');
        return $columns;
    }
}
