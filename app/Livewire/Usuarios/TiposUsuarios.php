<?php

namespace App\Livewire\Usuarios;

use RamonRietdijk\LivewireTables\Livewire\LivewireTable;
use RamonRietdijk\LivewireTables\Columns\Column;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class TiposUsuarios extends LivewireTable
{
    protected string $model = User::class;
    public $title = 'Tipos de Usuarios'; // Nombre del encabezado
    public $createForm;
    protected bool $useSelection = false;
    protected $listeners = ['updateEstadoUsuario', 'updateTipoUsuario'];
    public $loadingEstados = []; // Maneja el estado de carga por usuario

    protected function columns(): array
    {
        return [
            Column::make(__('Nombre'), 'name')
                ->searchable(),
            Column::make(__('Correo'), 'email')
                ->sortable()
                ->searchable(),
            // Columna de Tipo con select para cambiar tipo
            Column::make(__('Tipo'), function (Model $model): string {
                $currentTipo = $model->tipo; // 1: Administrador, 2: Comun 
                $userId = $model->getKey();
                $tipos = [
                    1 => 'Administrador',
                    2 => 'Comun',
                ];
                // Verificar si el usuario está en estado de carga
                $isLoading = in_array($userId, $this->loadingEstados);
                // Construir el select con todas las opciones recorriendo tipos, deshabilitando la opción actual
                $options = '';
                foreach ($tipos as $tipo => $label) {
                    $selected = $tipo == $currentTipo ? 'selected' : '';
                    $disabled = $tipo == $currentTipo ? 'disabled' : '';
                    $options .= '<option value="' . $tipo . '" ' . $selected . ' ' . $disabled . '>' . $label . '</option>';
                }
                $select = '
                    <select 
                        class="block w-full mt-1 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                        onchange="Livewire.dispatch(\'updateTipoUsuario\', { id: ' . $userId . ', tipo: this.value })"
                        ' . ($isLoading ? 'disabled' : '') . '
                    >
                        ' . $options . '
                    </select>
                ';
                return $select;
            })
                ->clickable(false)
                ->asHtml(),
            Column::make(__('Estado'), function (Model $model): string {
                $currentEstado = (int) $model->estado; // 1: Activo/Habilitado, 2: Deshabilitado
                $userId = $model->getKey();
                // Definir los estados
                $estados = [
                    1 => 'Habilitado',
                    2 => 'Deshabilitado',
                ];
                // Determinar el estado opuesto
                $oppositeEstado = $currentEstado === 1 ? 2 : 1;
                $oppositeLabel = $estados[$oppositeEstado];
                $currentLabel = $estados[$currentEstado];
                // Verificar si el usuario está en estado de carga
                $isLoading = in_array($userId, $this->loadingEstados);
                $buttonClasses = $currentEstado === 1
                    ? 'bg-blue-500 hover:bg-blue-600'
                    : 'bg-red-500 hover:bg-red-600';
                $button = '
                    <button 
                        class="w-32 h-10 px-4 py-2 rounded-md text-white ' . $buttonClasses . ' focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                        onclick="Livewire.dispatch(\'updateEstadoUsuario\', { id: ' . $userId . ', estado: ' . $oppositeEstado . ' })"
                        ' . ($isLoading ? 'disabled' : '') . '
                    >
                        ' . $currentLabel . '
                    </button>
                ';
                return $button;
            })
                ->clickable(false)
                ->asHtml(),
        ];
    }

    public function updateTipoUsuario($id, $tipo)
    {
        $user = User::find($id);
        if ($user) {
            $user->tipo = $tipo;
            $user->save();
        }
    }

    public function updateEstadoUsuario($id, $estado)
    {
        $user = User::find($id);

        if ($user) {
            $user->estado = $estado;
            $user->save();
        }
    }
}
