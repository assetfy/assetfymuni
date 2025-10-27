<?php

namespace App\Livewire\Servicios\Prestadora;

use RamonRietdijk\LivewireTables\Livewire\LivewireTable;
use RamonRietdijk\LivewireTables\Columns\Column;
use Illuminate\Database\Eloquent\Builder;
use App\Models\SolicitudesServiciosModel;
use Illuminate\Database\Eloquent\Model;
use App\Helpers\IdHelper;

class ServiciosActivosPendientes extends LivewireTable
{
    protected string $model = SolicitudesServiciosModel::class;
    public $title = 'Servicios pendientes para activos'; // Nombre del encabezado
    public $createForm = ''; // Nombre del componente de creación predeterminado
    public $editForm = ''; // Nombre del componente de edición predeterminado
    public $empresa, $datos;


    protected function query(): Builder
    {
        $this->asignar();

        return SolicitudesServiciosModel::query()
            ->where('empresa_prestadora', $this->empresa)
            ->where('estado_presupuesto', 'Confirmado por Cliente y esperando visita');
    }

    protected function asignar()
    {
        $this->empresa = IdHelper::idEmpresa();
    }

    protected function columns(): array
    {
        return [
            Column::make(__('Servicio'), 'servicios.nombre')->searchable(),
            Column::make(__('Activo'), 'activos.nombre'),
            Column::make(__('Tipo'), 'tipos.nombre'),
            Column::make(__('Categoria'), 'categorias.nombre'),
            Column::make(__('Subcategoria'), 'subcategorias.nombre'),
            Column::make(__('Empresa Prestadora'), 'empresasPrestadora.razon_social'),
            Column::make(__('Empresa Solicitante'), 'empresasSolicitantes.razon_social'),
            Column::make(__('Solicitante'), 'users.name'),
            Column::make(__('Descripcion de solicitud'), 'descripcion'),
            Column::make(__('Estado'), 'estado'),
            Column::make(__('Precio'), 'precio'),
            Column::make(__('Actions'), function (Model $model): string {
                return '<a class="underline" href="' . $this->action($model) . '"title="Realizar Servicios"><i class="fa-solid fa-circle-down"></i></a>';
            })
                ->clickable(false)
                ->asHtml(),
        ];
    }

    public function action( $model)
    {
        return route('servicios-realizar-servicios',['servicio' => $model]);
    }
}
