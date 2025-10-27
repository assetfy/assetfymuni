<?php

namespace App\Livewire\Empresas;

use App\Exports\AuditoriaExport;
use RamonRietdijk\LivewireTables\Livewire\LivewireTable;
use RamonRietdijk\LivewireTables\Filters\SelectFilter;
use RamonRietdijk\LivewireTables\Columns\Column;
use RamonRietdijk\LivewireTables\Actions\Action;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Enumerable;
use Maatwebsite\Excel\Facades\Excel;

use App\Helpers\IdHelper;
use App\Models\AuditoriaEmpresaModel;

class Auditoria extends LivewireTable
{
    protected string $model = AuditoriaEmpresaModel::class;

    public string $title = 'Auditorias';
    public string $createForm = '';
    protected bool $useSelection = true;

    protected function query(): Builder
    {
        $q = AuditoriaEmpresaModel::query();

        $rel = IdHelper::usuarioEmpresaActual();

        // Si es tipo 3 => ver todo
        if ($rel && (string) $rel->tipo_user == '3') {

            return $q;
        }

        // Caso contrario => filtrar por CUIT de la empresa actual
        $empresa = IdHelper::empresaActual(); // \App\Models\EmpresasModel|null
        if ($empresa && !empty($empresa->cuit)) {
            $q->where('cuit_empresa', $empresa->cuit);
        }

        return $q;
    }

    protected function columns(): array
    {
        return [
            Column::make(__('Tabla'), 'clase_tabla')->sortable()->searchable(),
            //Column::make(__('Nombre de la Pk'), 'pk_nombre')->sortable()->searchable(),
            //Column::make(__('ID de la tabla'), 'id_tabla')->sortable()->searchable(),
            Column::make(__('Campo'), 'campo')->sortable()->searchable(), // <-- FIX
            Column::make(__('Valor previo'), 'valor_previo')->sortable(),
            Column::make(__('Valor actual'), 'valor_actual')->sortable(),
            Column::make(__('Evento'), 'evento')->sortable()->searchable(),
            Column::make(__('Fecha y hora del cambio'), 'fechahora_cambio')->sortable(),
            Column::make(__('Autor'), 'autor')->sortable()->searchable(),
            Column::make(__('Empresa del autor'), 'autor_empresa')->sortable()->searchable(),
            Column::make(__('IP'), 'ip')->sortable()->searchable(),
        ];
    }

    /** 3) Filtro por clase de tabla (opcional pero útil) */
    protected function filters(): array
    {
        return [
            SelectFilter::make(__('Tabla'), 'clase_tabla')
                ->options($this->getClasesTablaOptions()),
        ];
    }

    protected function getClasesTablaOptions(): array
    {
        $opciones = AuditoriaEmpresaModel::query()
            ->select('clase_tabla')
            ->distinct()
            ->orderBy('clase_tabla')
            ->pluck('clase_tabla', 'clase_tabla')
            ->toArray();

        return ['' => __('Todas')] + $opciones;
    }

    /** 4) Acción de exportar (igual que tenías) */
    protected function actions(): array
    {
        return [
            Action::make(
                __('Exportar'),
                'export',
                fn(Enumerable $models) =>
                Excel::download(new AuditoriaExport($models), 'auditorias.xlsx')
            ),
        ];
    }
}
