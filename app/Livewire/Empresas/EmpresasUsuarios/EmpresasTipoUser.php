<?php

namespace App\Livewire\Empresas\EmpresasUsuarios;

use App\Models\EmpresasModel;
use App\Models\FotosDeEmpresaModel;
use App\Models\SolicitudesServiciosModel;
use App\Models\User;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use App\Traits\SortableTrait;
use App\Models\UsuariosEmpresasModel;
use App\Models\AsignacionesRolesModel;
use Illuminate\Support\Facades\Session;
use Carbon\Carbon;

class EmpresasTipoUser extends Component
{
    use SortableTrait, WithPagination, WithFileUploads;

    protected $listeners = ['render' => 'render'];

    public $search = "";
    public $sort = 'cuit';
    public $direction = 'desc';
    public $id, $users, $empresa;
    public $servicios, $serviciosExpirados;
    public $serviceDates = [];
    public $currentYear;
    public $currentMonth;
    public $photo, $open;
    public $usuarios;  // Añadimos la variable $usuarios

    public function render()
{
    $usuariosPaginados = $this->cargaUsuariosEmpresa(); // Obtenemos la colección paginada

    $this->usuarios = $usuariosPaginados->items(); // Extraemos los items a un array simple

    $fotosEmpresa = $this->fotosEmpresa($this->id);

    // Obtener las fotos de la empresa y completar con "Sin imagen" si es necesario
    $imagenes = $fotosEmpresa->pluck('foto')->toArray();
    $missingImagesCount = 4 - count($imagenes);
    for ($i = 0; $i < $missingImagesCount; $i++) {
        $imagenes[] = null; // Representa una imagen faltante
    }

    return view('livewire.empresas.EmpresasUsuarios.empresas-tipo-user', [
        'usuarios' => $this->usuarios, // Pasamos la variable $usuarios a la vista
        'imagenes' => $imagenes,
        'usuariosPaginados' => $usuariosPaginados // Pasamos la paginación completa si es necesario
    ]);
}


    public function mount()
    {
        $this->id = Session::get('cuitEmpresaSeleccionado');
        $this->cargarUsuario();
        $this->empresa = $this->datosEmpresa($this->id);
        $this->servicios = $this->serviciosCalendario($this->id);
        $this->serviciosExpirados = $this->serviciosExpiradosCalendario($this->id);
        $this->serviceDates = $this->fetchServiceDates();
        $this->currentYear = Carbon::now()->year;
        $this->currentMonth = Carbon::now()->month;
    }

    private function cargaUsuariosEmpresa()
    {
        return UsuariosEmpresasModel::where('id_usuario', 'like', '%' . $this->search . '%')
            ->where('cuit', $this->id)
            ->where('estado', 'Aceptado')
            ->orderBy($this->sort, $this->direction)
            ->paginate(4);
    }

    private function datosEmpresa($id)
    {
        return EmpresasModel::with('actividades')->where('cuit', $id)->first();
    }

    private function cargarUsuario()
    {
        $this->users = User::select('id', 'name')->get();
    }

    public function updateRepresentanteTecnico($usuarioId, $opcion)
    {
        $usuario = UsuariosEmpresasModel::where('id_usuario', $usuarioId)
            ->where('cuit', $this->id)->first();

        if ($usuario) {
            $usuario->es_representante_tecnico = $opcion;
            $usuario->save();
        }
    }

    private function serviciosCalendario($id)
    {
        return SolicitudesServiciosModel::select('fechaHora', 'fecha_modificada', 'descripcion')
            ->where('empresa_prestadora', $id)
            ->where('estado_presupuesto', 'Confirmado por Cliente y esperando visita')
            ->where('fechaHora', '>=', Carbon::now())
            ->orderBy('fechaHora', 'asc')
            ->get();
    }

    private function serviciosExpiradosCalendario($id)
    {
        return SolicitudesServiciosModel::select('fechaHora', 'fecha_modificada', 'descripcion')
            ->where('empresa_prestadora', $id)
            ->where('estado_presupuesto', 'Confirmado por Cliente y esperando visita')
            ->where('fechaHora', '<', Carbon::now())
            ->orderBy('fechaHora', 'desc')
            ->get();
    }

    private function fetchServiceDates()
    {
        $allServices = $this->servicios->concat($this->serviciosExpirados);
        return $allServices->map(function ($servicio) {
            $date = $servicio->fecha_modificada ? Carbon::parse($servicio->fecha_modificada) : Carbon::parse($servicio->fechaHora);
            return $date->format('Y-m-d');
        })->toArray();
    }

    public function savePhoto()
    {
        $this->validate([
            'photo' => 'image|max:1024', // 1MB Max
        ]);

        $path = $this->photo->store('photos', 'public');

        FotosDeEmpresaModel::create([
            'cuit' => $this->id,
            'foto' => $path,
        ]);

        $this->dispatch('lucky');
        $this->dispatch('render');
    }

    public function updatePermisos($usuario)
    {
        $this->dispatch('opendModal', ['usuario' => $usuario])->to('permisosRoles.editar-permisos-roles');
    }

    public function updateRepresentate($usuarioId)
    {
        $this->dispatch('openModal', ['usuario' => $usuarioId])->to('empresas.empresas-actividad-representante');
    }

    private function fotosEmpresa($id)
    {
        return FotosDeEmpresaModel::where('cuit', $id)->select('foto')->get();
    }

    public function usuarioTieneRoles($usuarioId)
    {
        foreach ($this->usuarios as $usuario) {
            if ($usuario['id_usuario'] == $usuarioId) {
                return true;
            }
        }
        return false;
    }

}
