<?php

namespace App\Livewire\Empresas\EmpresasUsuarios;

use App\Helpers\IdHelper;
use App\Models\ContratoInterPrestadoraModel;
use Illuminate\Support\Facades\Session;
use App\Models\UsuariosEmpresasModel;
use Illuminate\Support\Facades\Auth;
use App\Services\MiddlewareInvoker;
use Illuminate\Support\Facades\DB;
use App\Models\EmpresasModel;
use App\Models\OrganizacionUnidadesModel;
use App\Traits\SortableTrait;
use Livewire\Component;
use App\Models\User;
use Illuminate\Support\Facades\Cache;

class CreateUsuario extends Component
{
    use SortableTrait;
    public $open = false;
    public $cargo, $legajo, $cuit, $usuario, $tipo_inter_exter;
    public $nmro_contrato, $busqueda_prestadora, $datos_empresa_prestadora, $panel;
    public $empresa_buscada = false;
    public $buscando = false, $error_message = null, $mensajeError = '', $cuil = '';
    public $contratos = [];
    public $apellido;
    public $allNivelesPlano;
    public $nivelesPlano;
    public $padreId;
    public $padreNombre;
    public ?int $selectedLevel = null;
    public $empresaActual;
    public $supervisor, $searchSupervisor, $cuil_usuario, $tiene_supervisor;
    public $searchEmpleados = '';
    public $selectedEmpleados = null;          // texto mostrado en el botón
    public $empleadosLista;                    // colección para el dropdown
    public $supervisor_usuario = null;         // AQUÍ guardamos el ID del user elegido
    public $noEmpleadosEncontrados = false;    //Busqueda de los empleados para ser asignado

    // Nueva propiedad para determinar el modo de creación
    public $createNewUser = false;
    // Propiedades para el nuevo usuario
    public $name, $email, $password;

    protected $listeners = ['openModalCrearUsuario', 'setPadre', 'refreshUsuarios'];

    protected $rules =
    [
        'cuil' => 'required|max:50',
        'busqueda_prestadora' => 'nullable|max:200',
        'nmro_contrato' => 'nullable|max:50'
    ];

    // Reglas para la creación de un usuario nuevo
    protected $rulesNewUser = [
        'name'     => 'required|string|max:255',
        'email'    => 'required|email|max:255|unique:users,email',
        'password' => 'required|min:6',
        'cuil'     => 'required|numeric|digits:11|unique:users,cuil',
        'apellido' => 'required|string|max:255',
    ];

    public function openModalCrearUsuario()
    {
        $nombreClase = str_replace('App\\Livewire\\', '', debug_backtrace()[1]['class']);
        if (!MiddlewareInvoker::checkPermisosRoles()) {
            $this->dispatch('no-permission', ['message' => 'No tiene permiso para realizar esta acción.']);
            return;
        } else {
            $this->mount();
            $this->open = true;
        }
    }

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    public function mount()
    {
        $id = Session::get('cuitEmpresaSeleccionado');
        $user = Auth::user();
        if ($id == null) {
            $this->cuit = $user->entidad;
            $this->panel = $user->panel_actual;
        } else {
            $this->cuit = $id;
            $this->panel = $user->panel_actual;
        }
        $this->empresaActual  = IdHelper::empresaActual()->cuit;

        $this->searchEmpleados();
        $this->updatedSearchEmpleados();
        $this->cargarNivelesPlano();
    }

    public function save()
    {
        $this->validate();

        $usuario = UsuariosEmpresasModel::create([
            'id_usuario' => $this->usuario,
            'cuit' => $this->cuit,
            'tipo_inter_exter' => $this->tipo_inter_exter,
            'id_Nivel_Organizacion' => $this->padreId ?? null,
            'supervisor' => $this->supervisor,
            'supervisor_usuario' => $this->supervisor_usuario ?? null,
        ]);

        if ($this->tipo_inter_exter == 'Externo') {
            if (!empty($this->contratos)) {
                foreach ($this->contratos as $contrato) {
                    if (!empty($contrato['numero'])) { // Verificar que no esté vacío
                        ContratoInterPrestadoraModel::create([
                            'id_relacion' => $usuario->id_relacion,
                            'id_usuario' => $this->usuario,
                            'cuil_empresa' => $this->cuit,
                            'nmro_contrato' => $contrato['numero'],
                            'cuil_prestadora' => $this->datos_empresa_prestadora,
                        ]);
                    }
                }
            } else {
                ContratoInterPrestadoraModel::create([
                    'id_relacion' => $usuario->id_relacion,
                    'id_usuario' => $this->usuario,
                    'cuil_empresa' => $this->cuit,
                    'nmro_contrato' => null,
                    'cuil_prestadora' => $this->datos_empresa_prestadora,
                ]);
            }
        }
        $this->dispatch('refreshUsuarios');
        $this->dispatch('lucky');
        $this->close();
    }

    public function createUser()
    {
        // Valida los datos del nuevo usuario
        $fullName = trim("{$this->apellido} {$this->name}");
        $this->validate($this->rulesNewUser);
        try {
            DB::beginTransaction();
            // Crea el usuario en la tabla 'users'
            $newUser = User::create([
                'name'     => $fullName,
                'email'        => $this->email,
                'password'     => bcrypt($this->password),
                'cuil'       => $this->cuil,
                'panel_actual' => $this->panel,
                'entidad' => $this->cuit,
            ]);
            // Crea la asociación en la tabla de usuarios de la empresa
            $usuario = UsuariosEmpresasModel::create([
                'id_usuario'       => $newUser->id,
                'cuit'             => $this->cuit,
                'tipo_inter_exter' => $this->tipo_inter_exter,
                'id_Nivel_Organizacion' => $this->padreId ?? null,
                'supervisor' => $this->supervisor,
                'supervisor_usuario' => $this->supervisor_usuario ?? null,
            ]);
            // Si el usuario es Externo y se ingresaron contratos, guarda los contratos asociados
            if ($this->tipo_inter_exter == 'Externo') {
                if (!empty($this->contratos)) {
                    foreach ($this->contratos as $contrato) {
                        if (!empty($contrato['numero'])) {
                            ContratoInterPrestadoraModel::create([
                                'id_relacion'       => $usuario->id_relacion,
                                'id_usuario'        => $newUser->id,
                                'cuil'              => $this->cuit,
                                'nmro_contrato'     => $contrato['numero'],
                                'cuil_prestadora'   => $this->datos_empresa_prestadora,
                            ]);
                        }
                    }
                } else {
                    ContratoInterPrestadoraModel::create([
                        'id_relacion'       => $usuario->id_relacion,
                        'id_usuario'        => $newUser->id,
                        'cuil'              => $this->cuit,
                        'nmro_contrato'     => null,
                        'cuil_prestadora'   => $this->datos_empresa_prestadora,
                    ]);
                }
            }
            DB::commit();
            // Envía notificación de verificación de email
            //$newUser->setShouldSendEmailVerification(true);
            // $newUser->sendEmailVerificationNotification();
            $this->dispatch('lucky');
            // Dispara evento de éxito para el frontend
            $this->dispatch('Exito', [
                'title'   => 'Éxito',
                'message' => 'El usuario se creó correctamente.'
            ]);
            $this->dispatch('refreshUsuarios');

            $this->close();
        } catch (\Exception $e) {
            DB::rollBack();
            // Dispara evento de error para el frontend
            $this->dispatch('errorInfo', [
                'title'   => 'Error',
                'message' => $e->getMessage()
            ]);
        }
    }


    public function buscarUsuario()
    {
        $this->reset(['mensajeError', 'usuario']); // Resetear mensajes y usuario previo

        $this->buscando = true; // Iniciar búsqueda

        // Realizar la búsqueda del usuario según el CUIL
        $id_usuario = User::where('cuil', $this->cuil)->first();
        $id = Session::get('cuitEmpresaSeleccionado');

        if ($id_usuario) {
            $existUser = UsuariosEmpresasModel::where('id_usuario', $id_usuario->id)
                ->where('cuit', $id)
                ->first();
            if ($existUser) {
                // Si el usuario ya está asociado a la empresa, mostrar un mensaje de error
                $this->mensajeError = "El usuario ya está registrado en la empresa.";
            } else {
                // El usuario aun no esta asociado a la empresa
                $this->usuario = $id_usuario->id;
            }
        } else {
            // Si no se encontró un usuario, mostrar mensaje de error
            $this->mensajeError = "Usuario no encontrado.";
        }

        // Finalizar la búsqueda
        $this->buscando = false;
    }

    public function resetErrorMessage()
    {
        $this->mensajeError = '';
    }

    public function buscarEmpresa()
    {
        // Limpiar el mensaje de error antes de realizar la búsqueda
        $this->empresa_buscada = true;

        $this->error_message = null;

        // Determinar si el usuario ingresó un CUIT o nombre de empresa
        if (is_numeric($this->busqueda_prestadora)) {
            $empresa = EmpresasModel::where('cuit', $this->busqueda_prestadora)
                ->where('tipo', '=', '2')
                ->first()->cuit ?? null;
        } else {
            $empresa = EmpresasModel::where('razon_social', $this->busqueda_prestadora)
                ->where('tipo', '=', '2')->first()->cuit ?? null;
        }

        // Si la empresa no existe, asigna el mensaje de error
        if (!$empresa) {
            $this->error_message = true;
            $this->datos_empresa_prestadora = null;
        } else {
            // Si la empresa es encontrada, asigna su CUIT
            $this->datos_empresa_prestadora = $empresa;
            $this->error_message = null;
        }
    }

    public function agregarContrato()
    {
        // Agregar un nuevo contrato al array
        $this->contratos[] = ['numero' => ''];
    }

    public function eliminarContrato($index)
    {
        // Eliminar el contrato de la lista
        array_splice($this->contratos, $index, 1);
    }

    public function render()
    {
        $usuarios = User::all();
        $usuariosempresas = UsuariosEmpresasModel::all();
        $empresas = EmpresasModel::all();
        return view('livewire.empresas.EmpresasUsuarios.create-usuario', compact(['usuariosempresas', 'empresas', 'usuarios']));
    }

    public function close()
    {
        $this->reset(['cuil', 'tipo_inter_exter', 'busqueda_prestadora', 'usuario', 'datos_empresa_prestadora', 'mensajeError', 'email', 'name', 'password', 'supervisor', 'supervisor_usuario', 'tiene_supervisor']);
        $this->open = false;
    }

    public function prepararPaso6()
    {
        $this->cargarNivelesPlano();
        $this->searchEmpleados();
    }

    public function cargarNivelesPlano()
    {
        $cacheKey = 'niveles_plano_' . $this->empresaActual;

        // Guarda en cache por 60 minutos
        $todos = Cache::remember($cacheKey, now()->addMinutes(60), function () {
            return OrganizacionUnidadesModel::where('CuitEmpresa', $this->empresaActual)
                ->orderBy('PadreId')
                ->get();
        });

        $this->allNivelesPlano = $todos->map(fn($item) => (object)[
            'Id' => $item->Id,
            'Nombre' => $item->Nombre,
        ])->toArray();

        $this->nivelesPlano = $this->allNivelesPlano;

        $datosPrueba = collect($todos)
            ->map(fn($item) => [
                'id'     => (string) $item->Id,
                'padre'  => $item->PadreId ? (string) $item->PadreId : null,
                'nombre' => $item->Nombre,
            ])
            ->values()
            ->toArray();

        $this->dispatch('init-jstree', ['data' => $datosPrueba]);
    }

    public function setPadre(int $id)
    {
        $this->padreId = $id;
        $u = OrganizacionUnidadesModel::find($id);
        $this->padreNombre = $u?->Nombre;
    }

    public function searchEmpleados()
    {
        // Reiniciar la variable antes de cada búsqueda
        $this->noEmpleadosEncontrados = false;

        // Busca los Id de los empleados en UsuariosEmpresasModel
        $usuariosIds = UsuariosEmpresasModel::where('cuit', $this->cuit)
            ->where('estado', 'Aceptado')
            ->whereNot('estado', 'Deshabilitado')
            ->where('supervisor', 1)
            ->whereNotNull('supervisor')
            ->pluck('id_usuario');

        // Validar si hay IDs, si no, no hace la consulta en User
        if ($usuariosIds->isEmpty()) {
            $this->empleadosLista = collect(); // Colección vacía
            $this->noEmpleadosEncontrados = true;
            $this->cuil_usuario = null;
            return;
        }

        $nombreBuscado = $this->searchEmpleados ?: '';

        // Buscar en User nombre que coincida con los existentes
        $this->empleadosLista = User::whereIn('id', $usuariosIds)
            ->where('name', 'like', "%{$nombreBuscado}%")
            ->orWhere('cuil', 'like', "%{$nombreBuscado}%")
            ->whereNotNull('cuil')
            ->get();

        // Verificar si hay resultados
        $this->noEmpleadosEncontrados = $this->empleadosLista->isEmpty();
    }

    public function updatedSearchEmpleados()   // se dispara al escribir en el input
    {
        $this->buscarEmpleados();
    }

    private function buscarEmpleados()
    {
        $this->noEmpleadosEncontrados = false;

        $empleadosIds = UsuariosEmpresasModel::where('cuit', IdHelper::empresaActual()->cuit)
            ->where('estado', 'Aceptado')
            ->whereNot('estado', 'Deshabilitado')
            ->where('supervisor', 1)
            ->whereNotNull('supervisor')
            ->pluck('id_usuario')
            ->filter();

        if ($empleadosIds->isEmpty()) {
            $this->empleadosLista = collect();
            $this->noEmpleadosEncontrados = true;
            return;
        }

        $term = trim($this->searchEmpleados ?? '');

        $this->empleadosLista = User::whereIn('id', $empleadosIds)
            ->when($term !== '', function ($q) use ($term) {
                $q->where('name', 'like', "%{$term}%");
                if (is_numeric($term)) {
                    $q->orWhere('cuil', $term);
                }
            })
            ->orderBy('name')
            ->get();
    }

    // Seleccionar supervisor (recibe ID)
    public function setEmpleados(int $userId)
    {
        $user = User::find($userId);
        $this->selectedEmpleados   = $user->name;
        $this->supervisor_usuario  = $user->id;
        $this->searchEmpleados     = '';
        $this->dispatch('closeDropdown', ['dropdown' => 'empleados']);
    }
}