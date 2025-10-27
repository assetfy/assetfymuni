<?php

namespace App\Livewire;

use App\Models\ActivosAsignacionModel;
use Livewire\Component;
// C:\Users\38137513\Documents\GitHub\laradev\app\Http\ActivosDatos.php
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;
use App\Models\ActivosModel;
use App\Models\TiposModel;
use App\Models\EstadoGeneralModel;
use App\Models\EstadosAltasModel;
use App\Models\SubcategoriaModel;
use App\Models\CategoriaModel;
use App\Models\EmpresasModel;
use App\Models\UbicacionesModel;
use App\Models\ServiciosActivosModel;
use App\Models\ServiciosModel;
use App\Models\User;
use Carbon\Carbon;
use Livewire\WithFileUploads;

class ActivosDatos extends Component
{
    use WithFileUploads;
    public string $loginNextUrl; // <-- nueva
    public ?string $asignadoEmail = null;
    public ?string $gestorEmail   = null;
    public $id_activo;
    public $activo;
    public $tipos;
    public $altas;
    public $general;
    public $subcategorias;
    public $categorias;
    public $ubicaciones;
    public $servicios, $tipoServicios;
    public $empresas;
    public $usuarios, $datosUsuario;
    public $ultimaFechaServicio;
    public $asignado, $gestor, $fecha_asignacion;

    public function mount($id)
    {
        try {
            $this->id_activo = Crypt::decrypt($id);
            $this->datos($this->id_activo);

            $this->AsignacionActivo();

            // guarda el id para usarlo después del login (sin exponerlo en la URL)
            session()->put('open_activo_id', $this->id_activo);

            $this->loginNextUrl = route('login', ['next' => route('bienes-empresa')]);
        } catch (DecryptException $e) {
            abort(403, 'ID inválido o modificado.');
        }
    }

    public function render()
    {
        return view('livewire.activos-datos', [
            'activo' => $this->activo,
            'tipos' => $this->tipos,
            'altas' => $this->altas,
            'general' => $this->general,
            'subcategorias' => $this->subcategorias,
            'categorias' => $this->categorias,
            'ubicaciones' => $this->ubicaciones,
            'servicios' => $this->servicios,
            'tipoServicios' => $this->tipoServicios,
            'empresas' => $this->empresas,
            'usuarios' => $this->usuarios,
            'datosUsuario' => $this->datosUsuario,
            'ultimaFechaServicio' => $this->ultimaFechaServicio
        ])
            ->title('Detalles del Bien')
            ->layout('layouts.app');
    }

    protected function datos($id_activo)
    {
        $this->activo = ActivosModel::find($id_activo);
        $this->tipos = TiposModel::all();
        $this->altas = EstadosAltasModel::all();
        $this->general = EstadoGeneralModel::all();
        $this->subcategorias = SubcategoriaModel::all();
        $this->categorias = CategoriaModel::all();
        $this->ubicaciones = UbicacionesModel::all();
        $this->servicios = ServiciosActivosModel::where('id_activo', $this->id_activo)
            ->latest('fecha')
            ->first();
        // Formatear la fecha
        $this->ultimaFechaServicio = $this->servicios ? Carbon::parse($this->servicios->fecha)->format('Y-m-d H:i') : '';
        $this->tipoServicios = ServiciosModel::all();
        $this->empresas = EmpresasModel::all();
        $this->usuarios = User::all();
    }

    public function correo(string $aQuien) // 'gestor' | 'usuario'
    {
        $destino = $aQuien === 'gestor' ? $this->gestorEmail : $this->asignadoEmail;

        // URL del bien con el ID encriptado (ajustá la ruta si usás nombre de ruta)
        $idEnc   = Crypt::encrypt($this->activo->id_activo);
        $urlBien = url('/datos-activos/' . $idEnc);

        $datos = [
            'email'        => $destino,
            'activoId'     => $this->activo->id_activo,
            'activoNombre' => $this->activo->nombre ?? '',
            'urlBien'      => $urlBien,     // <— AHORA ES UN LINK REAL AL BIEN
            'etiqueta'     => $aQuien,
        ];

        $this->dispatch('contactar', datos: $datos)
            ->to(\App\Livewire\Activos\Correos::class);
    }

    private function AsignacionActivo()
    {
        $asig = ActivosAsignacionModel::with(['asignado', 'gestor'])
            ->where('id_activo', $this->activo->id_activo)
            ->first();

        $this->asignadoEmail = $asig?->asignado?->email;
        $this->gestorEmail   = $asig?->gestor?->email;

        // Extraigo el nombre o 'Sin asignado' si no existe
        $this->asignado = $asig->asignado?->name ?? 'Sin asignado';
        $this->gestor   = $asig->gestor?->name   ?? 'Sin asignado';
        $this->fecha_asignacion =   $asig->fecha_asignacion ?? '-';
    }
}
