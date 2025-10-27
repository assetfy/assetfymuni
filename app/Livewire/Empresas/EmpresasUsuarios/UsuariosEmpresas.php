<?php

namespace App\Livewire\Empresas\EmpresasUsuarios;

use RamonRietdijk\LivewireTables\Livewire\LivewireTable;
use RamonRietdijk\LivewireTables\Filters\SelectFilter;
use RamonRietdijk\LivewireTables\Columns\Column;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use App\Models\UsuariosEmpresasModel;
use App\Helpers\IdHelper;
use Carbon\Carbon;

class UsuariosEmpresas extends LivewireTable
{
    public $id, $usuarios, $user, $userId, $permisosUser, $cuit;
    protected string $model = UsuariosEmpresasModel::class;
    public $title = 'Usuarios'; // Nombre del encabezado
    public $createForm = 'crearUsuario';
    protected bool $useSelection = false;
    public $loadingEstados = []; // Maneja el estado de carga por usuario
    protected $listeners = ['updateEstadoUsuario', 'updateTipoUsuario', 'updateRepresentante', 'refreshUsuarios' => 'refreshTable', 'updateSupervisor'];
    // marca si ya lanzamos el modal como una bandera 
    protected bool $modalDispatched = false;

    private function asignar()
    {
        $this->cuit = IdHelper::idEmpresa();
    }

    public function hydrate(): void
    {
        if (! $this->modalDispatched) {
            $this->dispatch('openModal', ['empresas.EmpresasUsuarios.create-usuario', 'empresas.empresas-actividad-representante', 'permisosRoles.editar-permisos-roles', 'roles.create-asignaciones-roles', 'empresas.empresas-usuarios.editar-dependencia-modal']);
            $this->modalDispatched = true;
        }
    }

    protected function query(): Builder
    {
        $this->asignar();
        $cuit = $this->cuit;
        return $this->model()->query()
            ->where('cuit', $cuit)
            ->where('cargo', 'Empleado')
            ->with(['permisos' => function ($query) use ($cuit) {
                $query->where('cuit', $cuit);
            }]);
    }

    protected function columns(): array
    {
        return [
            Column::make(__('Nombre'), 'usuarios.name')
                ->searchable(),
            Column::make(__('Correo'), 'usuarios.email')
                ->sortable()
                ->searchable(),
            Column::make(__('Interno/Externo'), 'tipo_inter_exter')
                ->sortable()
                ->searchable(),
            Column::make(__('Cuit'), function (Model $model): string {
                return e(
                    $model->tipo_inter_exter === 'Interno' || is_null($model->tipo_inter_exter)
                        ? $model->cuit
                        : optional($model->contrato)->cuil_prestadora ?? 'Sin CUIT'
                );
            })->sortable()
                ->searchable(),
            Column::make(__('Tipo'), function (Model $model): string {
                $currentTipo = $model->tipo_user;
                $userId = $model->getKey();
                $tipos = [
                    1 => 'Comun',
                    2 => 'Administrador',
                ];
                $isLoading = in_array($userId, $this->loadingEstados);

                $currentEstado = $model->estado;
                $isDisabled = ($currentEstado == 'Deshabilitado');

                $options = '';
                foreach ($tipos as $tipo => $label) {
                    $selected = $tipo == $currentTipo ? 'selected' : '';
                    $disabledOption = $tipo == $currentTipo ? 'disabled' : '';
                    $options .= '<option value="' . $tipo . '" ' . $selected . ' ' . $disabledOption . '>' . $label . '</option>';
                }

                $selectClasses = 'block w-full mt-1 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm';
                if ($isLoading || $isDisabled) {
                    $selectClasses .= ' opacity-50 cursor-not-allowed';
                }

                $select = '
                    <select 
                        class="' . $selectClasses . '"
                        onchange="Livewire.dispatch(\'updateTipoUsuario\', { id: ' . $userId . ', tipo: this.value })"
                        ' . ($isLoading || $isDisabled ? 'disabled' : '') . '
                    >
                        ' . $options . '
                    </select>
                ';
                return $select;
            })
                ->clickable(false)
                ->asHtml(),
            Column::make(__('Supervisor'), function (Model $model): string {
                $currentSupervisor = $model->supervisor;
                $userId = $model->getKey();
                $supervisor = [
                    1 => 'Si',
                    0 => 'No',
                ];
                $isLoading = in_array($userId, $this->loadingEstados);

                $currentEstado = $model->estado;
                $isDisabled = ($currentEstado == 'Deshabilitado');

                $options = '';
                foreach ($supervisor as $super => $label) {
                    $selected = $super == $currentSupervisor ? 'selected' : '';
                    $disabledOption = $super == $currentSupervisor ? 'disabled' : '';
                    $options .= '<option value="' . $super . '" ' . $selected . ' ' . $disabledOption . '>' . $label . '</option>';
                }

                $selectClasses = 'block w-full mt-1 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm';
                if ($isLoading || $isDisabled) {
                    $selectClasses .= ' opacity-50 cursor-not-allowed';
                }

                $select = '
                    <select 
                        class="' . $selectClasses . '"
                        onchange="Livewire.dispatch(\'updateSupervisor\', { id: ' . $userId . ', supervisor: this.value })"
                        ' . ($isLoading || $isDisabled ? 'disabled' : '') . '
                    >
                        ' . $options . '
                    </select>
                ';
                return $select;
            })
                ->clickable(false)
                ->asHtml(),
            Column::make(__('Depende de'), function (Model $model): string {
                $nombre = $model->nivelOrganizacion->Nombre ?? 'Sin Dependencia';
                $userId = $model->getKey();

                $currentEstado = $model->estado;
                $isDisabled = ($currentEstado == 'Deshabilitado');

                $btn = 'bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded';
                if ($isDisabled) {
                    $btn .= ' opacity-50 cursor-not-allowed';
                }

                $btn = '                        
                <button
                            class="px-3 py-1 rounded-md text-white ' . $btn . ' focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500' . ($isDisabled ? ' opacity-50 cursor-not-allowed' : '') . '"
                            onclick="Livewire.dispatch(\'openModalEditarDependencia\', { payload: ' . $userId . ' })"
                        ' . ($isDisabled ? 'disabled' : '') . '>
                        <i class=\'fa fa-sitemap\'></i> Editar
                    </button>
                ';

                return '<div class="flex flex-col leading-tight">'
                    .     '<span class="text-gray-800 text-sm">' . e($nombre) . '</span>'
                    .      $btn
                    .  '</div>';
            })
                ->clickable(false)
                ->asHtml(),
            Column::make(__('Estado'), function (Model $model): string {
                $currentEstado = $model->estado;
                $userId = $model->getKey();

                $estados = [
                    'Aceptado' => 'Habilitado',
                    'Deshabilitado' => 'Deshabilitado',
                ];

                if (!array_key_exists($currentEstado, $estados)) {
                    $currentEstado = 'Aceptado';
                }

                $currentLabel = $estados[$currentEstado];

                $oppositeEstado = $currentEstado === 'Aceptado' ? 'Deshabilitado' : 'Aceptado';

                $isLoading = in_array($userId, $this->loadingEstados);

                $buttonClasses = $currentEstado === 'Aceptado'
                    ? 'bg-blue-500 hover:bg-blue-600'
                    : 'bg-red-500 hover:bg-red-600';

                $iconClass = $currentEstado === 'Aceptado' ? 'fa fa-check-circle' : 'fa fa-times-circle';

                $button = '
                        <button 
                            class="px-4 py-2 rounded-md text-white ' . $buttonClasses . ' focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                            onclick="Livewire.dispatch(\'updateEstadoUsuario\', { id: ' . $userId . ', estado: \'' . $oppositeEstado . '\' })"
                            ' . ($isLoading ? 'disabled' : '') . '
                        >
                            <i class="' . $iconClass . '"></i> ' . $currentLabel . '
                        </button>
                    ';
                return $button;
            })
                ->clickable(false)
                ->asHtml(),

            Column::make(__('Licencias'), function (Model $model): string {
                $currentEstado = $model->estado;
                $isDisabled = ($currentEstado == 'Deshabilitado');

                $buttonClasses = 'bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded';
                if ($isDisabled) {
                    $buttonClasses .= ' opacity-50 cursor-not-allowed';
                }

                if ($model->permisos) {
                    $button = '
                            <button 
                                onclick="Livewire.dispatch(\'openModalEditarPermisos\', { data: ' . $model->getKey() . ' })"
                                class="' . $buttonClasses . '"
                                ' . ($isDisabled ? 'disabled' : '') . '>
                                <i class="fa fa-edit"></i> Editar
                            </button>';
                } else {
                    $button = '
                            <button 
                                onclick="Livewire.dispatch(\'opernModalAsignarlRolUnicoUsuario\', { data: ' . $model->getKey() . ' })"
                                class="' . $buttonClasses . '"
                                ' . ($isDisabled ? 'disabled' : '') . '>
                               <i class="fa fa-plus"></i> Asignar
                            </button>';
                }
                return $button;
            })
                ->clickable(false)
                ->asHtml(),
            Column::make(__('Operador Ordenes'), function (Model $model): string {
                $currentEstado = $model->estado;
                $isDisabled = ($currentEstado == 'Deshabilitado');

                // Valor actual y el opuesto para el toggle
                $esRep = ($model->es_representante_tecnico === 'Si');
                $opposite = $esRep ? 'No' : 'Si';

                // Estilos del botón (pastel azul/verde)
                $buttonClasses = $esRep
                    ? 'bg-red-500 hover:bg-red-600'
                    : 'bg-blue-500 hover:bg-blue-600';

                $iconClass = $esRep; //? 'fa fa-user-check' : 'fa fa-user-plus';
                $labelBtn  = $esRep ? 'Desactivar' : 'Activar';

                $userId = $model->getKey();

                $button = '
                        <button
                            class="px-4 py-2 rounded-md text-white ' . $buttonClasses . ' focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500' . ($isDisabled ? ' opacity-50 cursor-not-allowed' : '') . '"
                            onclick="Livewire.dispatch(\'updateRepresentante\', { id: ' . $userId . ', representante: \'' . $opposite . '\' })"
                            ' . ($isDisabled ? 'disabled' : '') . '
                        >
                            <i class="' . $iconClass . '"></i> ' . $labelBtn . '
                        </button>
                    ';
                return $button;
            })
                ->clickable(false)
                ->asHtml(),

            Column::make(__('Email validado'), function (Model $model): string {
                $user = $model->usuarios; // Accede al usuario relacionado en UsuariosEmpresasModel
                return $user?->emailVerifiedDate() ?? '<span style="color:red;">No verificado</span>';
            })->clickable(false)
                ->asHtml(),
            Column::make(__('Fecha de creación'), function (Model $model): string {
                // Llama al método que retorna la fecha de creación
                return $model->usuarios?->createdAtDate(); // Llama al método que retorna la fecha de creación
            })->clickable(false)->asHtml(),
            Column::make(__('Ultima Conexión'), function (Model $model): string {
                $lastLogin = $model->usuarios?->lastLogin();
                return $lastLogin
                    ? Carbon::createFromTimestamp($lastLogin)->setTimezone('America/Argentina/Buenos_Aires')->format('Y-m-d H:i')
                    : 'No hay datos';
            })
                ->clickable(false)
                ->asHtml(),
        ];
    }

    public function updateTipoUsuario($id, $tipo)
    {
        $user = UsuariosEmpresasModel::find($id);
        if ($user) {
            $user->tipo_user = $tipo;
            $user->save();
        }
    }

    public function updateEstadoUsuario($id, $estado)
    {
        $empresaUsuario = UsuariosEmpresasModel::find($id);
        if (!$empresaUsuario) {
            return;
        }
        // Actualizar el estado en el modelo UsuariosEmpresasModel
        $empresaUsuario->update(['estado' => $estado]);

        // Usar la relación definida (usuarios) para actualizar el estado del usuario del sistema
        $sistemaUsuario = $empresaUsuario->usuarios;
        if ($sistemaUsuario) {
            $sistemaUsuario->update([
                'estado' => ($estado === 'Deshabilitado') ? 2 : 1
            ]);
        }
    }

    public function updateRepresentante($id, $representante)
    {
        $user = UsuariosEmpresasModel::find($id);
        if ($user) {
            $user->es_representante_tecnico = $representante;
            $user->save();
        }
    }

    public function updateSupervisor($id, $supervisor)
    {
        $user = UsuariosEmpresasModel::find($id);
        if ($user) {
            $user->supervisor = $supervisor;
            $user->save();
        }
    }

    protected function filters(): array
    {
        return [
            SelectFilter::make(__('Tipo de Usuario'), 'tipo_user')
                ->options([
                    '1' => __('Comun'),
                    '2' => __('Administrador'),
                ]),

            SelectFilter::make(__('Estado'), 'estado')
                ->options([
                    'Aceptado' => __('Habilitado'),
                    'Deshabilitado' => __('Deshabilitado'),
                ]),

            SelectFilter::make(__('Representante Técnico'), 'es_representante_tecnico')
                ->options([
                    'Si' => __('Si'),
                    'No' => __('No'),
                ]),
        ];
    }

    public function crearUsuario()
    {
        $this->dispatch('openModalCrearUsuario')->to('empresas.EmpresasUsuarios.create-usuario');
    }

    public function refreshTable()
    {
        $this->dispatch('refreshLivewireTable');
    }
}
