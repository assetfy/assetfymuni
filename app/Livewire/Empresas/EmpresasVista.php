<?php

namespace App\Livewire\Empresas;

use RamonRietdijk\LivewireTables\Livewire\LivewireTable;
use RamonRietdijk\LivewireTables\Columns\Column;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use App\Models\UsuariosEmpresasModel;
use App\Models\EmpresasModel;
use App\Models\User;


class EmpresasVista extends LivewireTable
{
    protected string $model = EmpresasModel::class;
    public $title = 'Solicitudes de alta'; // Nombre del encabezado
    public $createForm; // Nombre de la función que llama al evento
    protected bool $useSelection = false;
    protected $listeners = ['updateEstado'];

    // Propiedad para manejar el estado de carga por empresa
    public $loadingEstados = [];

    protected function query(): Builder
    {
        return $this->model::where('autoriza', 'admin')->with('usuariosApoderado.usuarios');
    }

    protected function columns(): array
    {
        return [
            Column::make(__('Nombre'), 'usuariosApoderado.usuarios.name')
                ->searchable(),
            Column::make(__('Empresa'), 'razon_social')
                ->sortable()
                ->searchable(),
            Column::make(__('Cuit'), 'cuit')
                ->sortable()
                ->searchable(),
            // Columna de Acciones con botón o select según el estado
            Column::make(__('Estado'), function (Model $model): string {
                // Obtener el estado actual y el ID de la empresa
                $currentEstado = e($model->estado);
                $empresaId = $model->getKey();

                if ($currentEstado === 'En Revision') {
                    // Verificar si está en carga
                    $isLoading = in_array($empresaId, $this->loadingEstados);
                    // Construir el select con deshabilitación si está en carga
                    return '<select 
                    aria-label="Cambiar estado de la empresa" 
                    class="w-32 h-10 text-center bg-gray-100 border border-gray-300 text-black font-medium text-sm rounded-lg cursor-pointer appearance-none focus:outline-none focus:ring focus:ring-indigo-500 focus:border-indigo-500" 
                    onchange="Livewire.dispatch(\'updateEstado\', { id: ' . $empresaId . ', estado: this.value })" ' . 
                    ($isLoading ? 'disabled' : '') . '>
                        <option value="" hidden selected>Revisión</option>
                        <option value="Aceptado">Aceptado</option>
                        <option value="Rechazado">Rechazado</option>
                </select>';
        
                } else {
                    // Mostrar un botón azul no interactivo indicando el estado actual
                    return '<button class="w-32 h-10 bg-blue-500 text-white font-bold py-2 px-4 rounded cursor-default">' . $currentEstado . '</button>';
                }
            })
                ->clickable(false)
                ->asHtml(),
            Column::make(__('Acciones'), function (Model $model): string {
                return '<a class="w-32 h-10 bg-blue-500 text-white font-bold py-2 px-4 rounded inline-block" href="' . asset(str_replace('public/', '', $model->constancia_afip)) . '" target="_blank">Constancia</a>';
            })
                ->clickable(false)
                ->asHtml(),
        ];
    }


    public function updateEstado($estado, $id)
    {
        $empresa = EmpresasModel::find($id);
        if ($empresa) {
            $this->panel($id, $estado);
            $empresa->estado = $estado;
            $empresa->save();
        }
        if ($estado == 'Aceptado') {
            $this->actualizarEstadoUsuarioEmpresa($estado, $id);
        }
        $this->dispatch('refreshLivewireTable');
    }


    private function panel($cuit, $estado)
    {
        if ($estado == 'Rechazado') {
            $usuarios = UsuariosEmpresasModel::where('cuit', $cuit)->pluck('id_usuario');
            $users = User::whereIn('id', $usuarios)->get();
            foreach ($users as $user) {
                $user->panel_actual = 'Usuario';
                $user->save();
            }
        }
    }

    private function actualizarEstadoUsuarioEmpresa($nuevoEstado, $cuit)
    {
        $user = auth()->user();
        $usuarioEmpresa = UsuariosEmpresasModel::where('id_usuario', $user->id)
            ->where('cuit', $cuit)
            ->where('cargo', 'Apoderado')
            ->first();
        if ($usuarioEmpresa) {
            $usuarioEmpresa->estado = $nuevoEstado;
            $usuarioEmpresa->save();
        }
    }
}
