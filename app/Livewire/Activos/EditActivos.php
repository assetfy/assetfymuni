<?php

namespace App\Livewire\Activos;

use App\Helpers\Funciones;
use App\Helpers\IdHelper;
use App\Models\ActivosAtributosModel;
use App\Models\ActivosFotosModel;
use App\Models\UbicacionesModel;
use Livewire\Component;
use App\Models\ActivosModel;
use App\Models\EstadosAltasModel;
use App\Models\EstadoGeneralModel;
use App\Models\ActivosAsignacionModel;
use App\Models\AtributosSubcategoriaModel;
use App\Models\MarcasModel;
use App\Models\ModelosModel;
use App\Models\OrganizacionUnidadesModel;
use App\Models\PisosModel;
use App\Models\CondicionModel;
use App\Models\ordenesBienesModel;
use App\Models\OrdenesModel;
use Livewire\WithFileUploads;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Crypt;
use App\Models\QrBienModel;
use Illuminate\Support\Facades\DB; //

class EditActivos extends Component
{
    use WithFileUploads;
    public ?string $asignadoEmail = null;
    public ?string $gestorEmail   = null;
    public $atributosDisponibles = [];
    public $updatedImagenes = [], $imagen;
    public $open = false;
    public $upetiqueta, $upnumero_serie, $uppropietario, $ultimaOrdenFecha, $id_padre,
        $upnombre, $id_estado_sit_alta, $upcomentarios_sit_alta,
        $upestado_inventario, $upmotivo_baja, $id_estado_sit_general,
        $id_tipo, $id_categoria, $id_subcategoria, $nombreActivo, $propietario, $fechaCompra, $garantiaVigente, $facturaCompra, $vencimientoGarantia,
        $user, $asignado, $id_activo, $cert_garantia, $up_cert_garantia, $up_vencimiento_garantia, $garantia_vigente, $qrUrl, $upNmroSerie, $pisoActual, $pisosDisponible;
    public $certificadoSubido = false;
    public $activo, $edicion, $piso, $pisoActualId, $pisoActualNombre, $padreId, $allNivelesPlano, $datosPrueba, $altas, $condicionBien;
    public $cat, $subcat, $factura_compra, $gestor;
    public $baja = false;
    public $ubicacionActual, $userId, $atributos, $atributosDatos, $selectedPiso, $pisoNombre, $nivelesPlano, $padreNombre, $searchPadre, $nivelActual, $nombre, $linkbien, $alta;
    public $id_ubicacion = -2;
    public $selectedUbicacionNombre;
    public $activeTab = 'detalles'; // Pestaña activa
    public $editMode = false; // Modo de edición
    public $imagenes = [];
    public $id_condicion, $fecha_asignacion;
    public array $tabsLoaded = [];
    public $ubicacionesDisponibles = []; // Ubicaciones disponibles para el usuario
    public $searchUbicacion = ''; // Campo de búsqueda
    public $ListaMarcas = [],  $selectedMarcaNombre, $searchMarca, $id_marca;
    public $ListaModelos = [], $selectedModeloNombre, $searchModelo, $id_modelo;

    // Escuchar eventos desde el componente UbicacionDropdown
    protected $listeners = ['editActivos', 'ubicacionSeleccionada', 'cerrarModal' => 'close', 'setPadre',];


    public function editActivos($data)
    {
        $this->reset(['atributos', 'atributosDisponibles', 'atributosDatos']);

        // Verificar si el parámetro es un array o un entero
        if (is_int($data)) {
            $this->edicion = ActivosModel::find($data);
        } elseif (is_array($data)) {
            $activoId = $data['activoId']['id_activo'];
            $this->edicion = ActivosModel::find($activoId);
        }
        $this->AsignacionActivo();
        if ($this->edicion) {
            $this->cargarDatosActivos($this->edicion);
            $this->atributos = $this->atributosActivos($this->edicion);
            $this->cargarNivelesPlano();
            $this->activeTab = 'detalles';
            $this->open = true;
        }
    }

    public function correo($data)
    {
        $nombreActivo = $this->edicion->nombre;
        $url =  $this->linkbien;
        $correo = $this->gestorEmail;
        $datos = [
            'email'        => $correo,
            'activoNombre' => $nombreActivo,
            'urlBien'      => $url,
            'etiqueta'     => $data,
        ];

        $this->dispatch('contactar', datos: $datos);
    }

    private function padreActivo()
    {
        $this->padreNombre = OrganizacionUnidadesModel::where('id', $this->id_padre)->value('Nombre');
    }
    // 1) Si sólo te interesa la fecha:
    private function ultimaOrden()
    {
        // IDs de órdenes asociadas al activo
        $ids = ordenesBienesModel::where('id_activo', $this->id_activo)
            ->pluck('id_orden');

        if ($ids->isEmpty()) {
            $this->ultimaOrdenFecha = 'Sin órdenes';
            return;
        }

        // Última fecha de carga entre esas órdenes
        $fecha = OrdenesModel::whereIn('id_orden', $ids)
            ->max('fecha');

        $this->ultimaOrdenFecha = $fecha
            ? \Illuminate\Support\Carbon::parse($fecha)->format('d/m/Y H:i')
            : 'Sin órdenes';
    }
    /**
     * Se lanza automáticamente cada vez que cambie $activeTab
     */
    public function updatedActiveTab(string $tab)
    {
        // Evitamos recargar dos veces la misma pestaña
        if (! empty($this->tabsLoaded[$tab])) {
            return;
        }
        // Marcamos como ya cargada 
        $this->tabsLoaded[$tab] = true;
        switch ($tab) {
            case 'atributos':
                // Método que ya tenías para levantar atributos
                $this->atributos = $this->atributosActivos($this->edicion);
                $this->edicion->actualizarGarantias();
                $this->altas = EstadosAltasModel::whereIn('nombre', ['Nuevo', 'Usado'])->get();
                $this->marcas();
                $this->modelos();
                break;
            case 'garantia':
                // Tu lógica de garantía
                $this->garantiaActivo($this->edicion);
                break;
            case 'Unidad_Responsable':
                $this->padreActivo();
                break;
        }
    }

    private function AsignacionActivo()
    {
        $asig = ActivosAsignacionModel::with(['asignado', 'gestor'])
            ->where('id_activo', $this->edicion->id_activo)
            ->first();
        $this->asignadoEmail = $asig?->asignado?->email;
        $this->gestorEmail   = $asig?->gestor?->email;
        // Extraigo el nombre o 'Sin asignado' si no existe
        $this->asignado = $asig->asignado?->name ?? 'Sin asignado';
        $this->gestor   = $asig->gestor?->name   ?? 'Sin asignado';
        $this->fecha_asignacion =   $asig->fecha_asignacion ?? '-';
    }

    private function cargarDatosActivos($activo)
    {
        $this->activo = $activo;
        $this->nombreActivo =    $this->activo->nombre;
        $this->editMode = false;
        // Cargar información del activo
        $this->id_activo = $activo->id_activo;
        $this->upetiqueta = $activo->etiqueta;
        $this->upnombre = $activo->nombre;
        $this->id_estado_sit_alta = $activo->id_estado_sit_alta;
        $this->upcomentarios_sit_alta = $activo->comentarios_sit_alta;
        $this->upmotivo_baja = $activo->motivo_baja;
        $this->id_estado_sit_general = $activo->id_estado_sit_general;
        $this->id_condicion = $activo->id_condicion;
        $this->id_tipo = $activo->id_tipo;
        $this->id_categoria = $activo->id_categoria;
        $this->id_subcategoria = $activo->id_subcategoria;
        $this->upestado_inventario = $activo->estado_inventario;
        $this->fechaCompra = $activo->fecha_compra;
        $this->facturaCompra = $activo->factura_compra;
        $this->up_cert_garantia = $this->activo->cert_garantia;
        $this->propietario =  $activo->propietario;
        $this->up_vencimiento_garantia = $activo->vencimiento_garantia;
        $this->garantia_vigente = $activo->garantia_vigente;
        $this->upNmroSerie = $activo->numero_serie;
        $this->id_modelo = $activo->id_modelo;
        $this->id_padre = $activo->id_Nivel_Organizacion;

        // dd($activo->id_condicion);
        if ($activo->id_modelo) {
            $this->id_marca = ModelosModel::find($activo->id_modelo)->id_marca;
        }

        if ($activo->cert_garantia != null) {
            $this->certificadoSubido = true;
        } else {
            $this->certificadoSubido = false;
        }

        $fotos = ActivosFotosModel::where('id_activo', $activo->id_activo)
            ->pluck('ruta_imagen')
            ->toArray();

        $this->imagenes = array_map(function ($ruta) {
            return Storage::disk('s3')
                ->temporaryUrl($ruta, now()->addMinutes(10));
        }, $fotos);

        $this->userId = IdHelper::usuarioActual()->entidad;

        // Configurar la ubicación actual y las disponibles
        if ($activo->id_ubicacion) {
            // Asignar la ubicación actual del activo
            $this->ubicacionActual = UbicacionesModel::find($activo->id_ubicacion);
            // Obtener las ubicaciones disponibles excluyendo la actual
            $this->ubicacionesDisponibles = $this->ubicacionesSegunReglas($this->ubicacionActual->id_ubicacion);
            $this->selectedUbicacionNombre = $this->ubicacionActual->nombre;
            if ($this->ubicacionActual->multipisos == 1) {
                $this->PisosActivo($activo);
            }
        } else {
            // Si el activo no tiene ubicación asignada
            $this->ubicacionActual = null; // Indicar que no tiene ubicación actual
            $this->ubicacionesDisponibles = $this->ubicacionesSegunReglas();
            $this->selectedUbicacionNombre = 'Sin Ubicación';
        }

        // Si hay una búsqueda previa, filtrar las ubicaciones
        if ($this->searchUbicacion) {
            $this->updatedSearchUbicacion();
        }
        $this->UbicacionActivo();
        $this->activosQr();
        $this->condicionActivo();
        $this->ultimaOrden();
        $this->situacionAlta();
    }

    private function situacionAlta()
    {
        $this->alta = EstadosAltasModel::where('id_estado_sit_alta', $this->id_estado_sit_alta)->value('nombre');
    }

    private function condicionActivo()
    {
        $this->condicionBien = CondicionModel::where('id_condicion', $this->id_condicion)
            ->value('nombre')
            ?? 'Sin datos';
    }

    private function UbicacionActivo()
    {
        if ($this->ubicacionActual && $this->ubicacionActual->lat && $this->ubicacionActual->long) {
            $this->dispatch('showActivoMap', [
                'lat'   => $this->ubicacionActual->lat,
                'lng'   => $this->ubicacionActual->long,
                'mapId' => 'mapActivo',
            ]);
            $this->id_ubicacion = $this->ubicacionActual->id_ubicacion;
            $this->selectedUbicacionNombre = $this->ubicacionActual->nombre;
        } else {
            $this->id_ubicacion = -1;
        }
    }

    private function datos($edicion)
    {
        // 1 sola consulta “lógica” a la BD
        $this->nivelActual = OrganizacionUnidadesModel::with('padre')
            ->find($edicion->id_Nivel_Organizacion);

        if (! $this->nivelActual) {
            return;
        }
        $this->nombre      = $this->nivelActual->Nombre;
        // si viene cargado, usamos la relación; si no, null
        $this->padreNombre =  $this->nivelActual->Nombre;
    }

    public function cargarNivelesPlano()
    {
        $empresaActual  = IdHelper::empresaActual()->cuit;
        $todos = OrganizacionUnidadesModel::where('CuitEmpresa',  $empresaActual)
            ->orderBy('PadreId')
            ->get();

        $this->allNivelesPlano = $todos->map(fn($item) => (object)[
            'Id' => $item->Id,
            'Nombre' => $item->Nombre,
        ])->toArray();

        $this->nivelesPlano = $this->allNivelesPlano;

        $datosPrueba = collect($todos) // $todos ya es una Collection de Eloquent
            ->map(fn($item) => [
                // el ID como string
                'id'     => (string) $item->Id,
                // si tiene PadreId lo casteamos a string, si no dejamos null
                'padre'  => $item->PadreId ? (string) $item->PadreId : null,
                // el nombre tal cual
                'nombre' => $item->Nombre,
            ])
            ->values()   // reindexa numéricamente
            ->toArray(); // convierte a array puro
        $this->dispatch('init-jstree-edit', ['data' => $datosPrueba]);
    }


    private function PisosActivo($activo)
    {
        $piso = PisosModel::find($activo->id_piso);
        if ($piso) {
            // Propiedad con el id
            $this->pisoActualId     = $piso->id_piso;
            // Propiedad con el nombre
            $this->pisoActualNombre = $piso->nombre;
        } else {
            $this->pisoActualId     = null;
            $this->pisoActualNombre = null;
        }
    }

    private function garantiaActivo($activo)
    {
        $this->garantiaVigente = $activo->garantia_vigente;
        if ($this->garantiaVigente == 'Si') {
            $this->vencimientoGarantia = $activo->vencimiento_garantia;
        }
    }

    private function atributosActivos($activo)
    {
        return ActivosAtributosModel::where('id_activo', $activo->id_activo)
            ->with([
                'atributo.tiposCampos',
                'atributo.unidadMedida',
            ])
            ->get();
    }

    protected $rules = [
        'upnombre' => 'required',
        'upmotivo_baja' => 'nullable',
    ];

    public function estadoId($value)
    {
        $estado = EstadoGeneralModel::find($value);
        $this->baja = $estado->nombre === 'Baja';
    }

    public function toggleEditMode()
    {
        if ($this->isResponsable()) {
            $this->editMode = !$this->editMode;
        }
    }

    // private function obtenerTodasUbicaciones($userId)
    // {
    //     return UbicacionesModel::where(function ($query) use ($userId) {
    //         $query->where('cuit', $userId)
    //             ->orWhere('cuil', $userId);
    //     })->get();
    // }

    // private function obtenerUbicacionesDisponibles($ubicacionActual, $userId)
    // {
    //     return UbicacionesModel::where(function ($query) use ($userId) {
    //         $query->where('cuit', $userId)
    //             ->orWhere('cuil', $userId);
    //     })
    //         ->when($ubicacionActual, function ($query) use ($ubicacionActual) {
    //             $query->where('id_ubicacion', '!=', $ubicacionActual->id_ubicacion);
    //         })
    //         ->get();
    // }

    public function actualizar()
    {
        $this->validate([
            'upnombre' => 'required',
            'upmotivo_baja' => 'nullable',
            'updatedImagenes.*' => 'nullable|image|max:2048', // Validación de la nueva imagen
        ]);


        // Si hay archivo, se guarda
        if ($this->up_cert_garantia instanceof UploadedFile) {
            $certificadoPath = $this->up_cert_garantia->store('StorageMvp/facturas', 's3');
            $this->activo->cert_garantia = $certificadoPath;
            $this->activo->garantia_vigente = 'Si';
            $this->certificadoSubido = true;
        } else {
            $this->activo->cert_garantia = null;
        }

        if ($this->up_vencimiento_garantia) {
            $this->activo->vencimiento_garantia = $this->up_vencimiento_garantia;
            $this->activo->garantia_vigente = 'Si';
        } else {
            $this->activo->vencimiento_garantia = null;
        }

        if ($this->factura_compra) {
            $facturaPath = $this->factura_compra->store('StorageMvp/facturas', 's3');
        }

        $estado_inventario = $this->id_estado_sit_general == Funciones::activoBaja()
            ? 'Baja'
            : 'Activo';

        // Asigna los valores editados al activo actual
        $this->activo->estado_inventario = $estado_inventario;
        $this->activo->nombre = $this->upnombre;
        $this->activo->motivo_baja = $this->upmotivo_baja;
        $this->activo->id_estado_sit_general = $this->id_estado_sit_general;
        $this->activo->id_condicion = $this->id_condicion;
        $this->activo->id_estado_sit_alta = $this->id_estado_sit_alta;
        $this->activo->id_modelo = $this->id_modelo;
        $this->activo->numero_serie = $this->upNmroSerie;
        $this->activo->factura_compra = $facturaPath ?? null;
        $this->activo->fecha_compra = $this->fechaCompra  ?? null;
        $this->activo->id_Nivel_Organizacion = $this->padreId ?? null;
        if ($this->id_ubicacion != -2) {
            if ($this->id_ubicacion == -1) {
                $this->activo->id_ubicacion = null;
            } else {
                $this->activo->id_ubicacion = $this->id_ubicacion;
                $this->CargarPiso();
                $this->activo->id_piso =  $this->pisoActualId ?? null;
            }
        }
        // Manejo de la imagen
        if ($this->updatedImagenes) {
            $this->manejoImagen();
        }
        // Guarda los cambios del activo
        $this->activo->save();
        // Dispara eventos para la actualización de la interfaz
        $this->dispatch('lucky');
        $this->dispatch('refreshBienes');
        $this->dispatch('refreshBienesAceptados');
        $this->close();
    }

    private function manejoImagen()
    {
        if (!$this->updatedImagenes || count($this->updatedImagenes) === 0) return;

        foreach ($this->updatedImagenes as $imagen) {
            // 1) Guardar la imagen
            $filename = $imagen->store('StorageMvp/fotos', 's3');

            // 2) Crear el registro en la base de datos
            ActivosFotosModel::create([
                'id_activo' => $this->activo->id_activo,
                'id_tipo' => $this->activo->id_tipo,
                'id_categoria' => $this->activo->id_categoria,
                'id_subcategoria' => $this->activo->id_subcategoria,
                'ruta_imagen' => $filename,
            ]);
        }

        $imagenesPrevias = ActivosFotosModel::where('id_activo', $this->activo->id_activo)->get();

        // // 1) Guardamos la ruta anterior para borrarla más tarde
        // $fotoprevia = $foto ? $foto->ruta_imagen : null;

        // // 2) Subimos la nueva imagen y guardamos la ruta en la base de datos
        // $filename = $this->updatedImagen->store('fotos', 'public');

        // // Asignar los datos y guardar
        // $foto->fill([
        //     'id_tipo' => $this->activo->id_tipo,
        //     'id_categoria' => $this->activo->id_categoria,
        //     'id_subcategoria' => $this->activo->id_subcategoria,
        //     'ruta_imagen' => $filename,
        // ])->save();

        // foreach ($imagenesPrevias as $foto) {
        //     if (Storage::exists('public/' . $foto->ruta_imagen)) {
        //         Storage::delete('public/' . $foto->ruta_imagen);
        //     }
        //     $foto->delete();
        // }
    }

    private function cuitMiEmpresa(): string
    {
        return (string) IdHelper::empresaActual()->cuit;
    }

    private function ubicacionesSegunReglas(?int $excluirId = null)
    {
        $q = UbicacionesModel::query();
        $cuitEmpresa = $this->cuitMiEmpresa();

        if (strtolower((string)$this->propietario) === 'cliente') {
            // Vistas de una empresa PROVEEDORA para su CLIENTE
            $q->where('propiedad', 'Cliente')
                ->where('cuit_empresa', $cuitEmpresa);

            // // Si sabemos quién es el titular, lo filtramos
            // if ($cuitTitular) {
            //     $q->where('cuit', $cuitTitular);
            // }
        } else {
            // PROPIO: ubicaciones de MI empresa
            $q->where('propiedad', 'Propio')
                ->whereNull('cuit_empresa')
                ->where('cuit', $cuitEmpresa);
        }

        if ($excluirId) {
            $q->where('id_ubicacion', '!=', $excluirId);
        }

        return $q->orderBy('nombre')->get();
    }


    public function updatedUpdatedImagenes($nuevas)
    {
        if (!$this->updatedImagenes) {
            $this->updatedImagenes = [];
        }
        // Para evitar duplicados, verificar cada nuevo archivo con los ya existentes
        foreach ($nuevas as $file) {
            $exists = false;
            foreach ($this->updatedImagenes as $existingFile) {
                // Comparar por nombre original o alguna propiedad única
                if ($existingFile->getClientOriginalName() === $file->getClientOriginalName()) {
                    $exists = true;
                    break;
                }
            }
            if (!$exists) {
                $this->updatedImagenes[] = $file;
            }
        }
    }

    public function eliminarImagenTemp($index)
    {
        if (isset($this->updatedImagenes[$index])) {
            unset($this->updatedImagenes[$index]);
            $this->updatedImagenes = array_values($this->updatedImagenes); // reindexar
        }
    }

    // Si el usuario es el responsable del bien
    public function isResponsable(): bool
    {
        $userId = IdHelper::usuarioEmpresaActual()->id_usuario;

        return ActivosAsignacionModel::query()
            ->where('id_activo',            $this->id_activo)
            ->where('estado_asignacion',    'Aceptado')
            ->whereNull('fecha_fin_asignacion')
            ->where(function ($q) use ($userId) {
                $q->where('responsable',     $userId)
                    ->orWhere('gestionado_por', $userId);
            })
            ->exists();
    }

    public function close()
    {
        $this->open = false;

        $this->up_cert_garantia = null;
        $this->up_vencimiento_garantia = null;
        $this->certificadoSubido = false;
        $this->cert_garantia = null;
        $this->garantia_vigente = null;

        $this->nombre       = '';
        $this->padreId      = null;
        $this->padreNombre  = null;
        $this->searchPadre  = '';
        $this->nivelesPlano = $this->allNivelesPlano;

        $this->reset([
            'id_tipo',
            'id_ubicacion',
            'ubicacionActual',
            'selectedUbicacionNombre',
            'searchUbicacion',
            'imagenes',
            'id_ubicacion',
            'updatedImagenes',
            'pisoActual',
            'factura_compra',
            'atributosDisponibles',
            'atributosDatos',
            'openModalAtributos'
        ]);
        // Recargar las ubicaciones disponibles al cerrar
        // if ($this->activo->id_ubicacion) {
        //     $this->ubicacionesDisponibles = $this->obtenerUbicacionesDisponibles($this->ubicacionActual, $this->userId);
        // } else {
        //     $this->ubicacionesDisponibles = $this->obtenerTodasUbicaciones($this->userId);
        // }
    }

    private function isInmueble(): bool
    {
        return \App\Helpers\Funciones::isInmueble($this->id_tipo ?? '');
    }

    public function render()
    {
        if (!$this->qrUrl && $this->activo) {
            $this->activosQr(); // tu método que arma quickchart con el link encriptado
        }
        $condiciones = CondicionModel::all();
        $general = EstadoGeneralModel::all();
        $qr = QrBienModel::where('cuit', IdHelper::idEmpresa())->first();

        return view('livewire.activos.edit-activos', [
            'activo' => $this->activo,
            'general' => $general,
            'responsable' => $this->isResponsable(),
            'qrUrl' => $this->qrUrl,
            'condiciones' => $condiciones,
            'qr' => $qr,
            'inmueble' => $this->isInmueble(),
        ]);
    }

    public function eliminarImagenBD(string $rutaUrl)
    {
        $path = parse_url($rutaUrl, PHP_URL_PATH);
        $key = ltrim($path, '/');

        if (Storage::disk('s3')->exists($key)) {
            Storage::disk('s3')->delete($key);
        }
        ActivosFotosModel::where('id_activo', $this->activo->id_activo)
            ->where('ruta_imagen', $key)
            ->delete();

        $fotos = ActivosFotosModel::where('id_activo', $this->activo->id_activo)
            ->pluck('ruta_imagen')
            ->toArray();

        $this->imagenes = array_map(function ($ruta) {
            return Storage::disk('s3')->temporaryUrl($ruta, now()->addMinutes(10));
        }, $fotos);
    }

    public function updatedIdUbicacion($value)
    {
        // Si eligieron -1 => "Sin ubicación"
        if ($value == -1) {
            // Mapa default (coords de Corrientes, por ejemplo)
            $this->dispatch('showActivoMap', [
                'lat'   => -27.4799,
                'lng'   => -58.8361,
                'mapId' => 'mapActivo', // ID del <div> donde pintas el mapa
            ]);
            return;
        }
        // Si eligieron otra ubicación y NO es -2 (que usas como “ninguna seleccionada”)
        if ($value != -2) {
            $ubicacion = UbicacionesModel::find($value);
            if ($ubicacion && $ubicacion->lat && $ubicacion->long) {
                $this->dispatch('showActivoMap', [
                    'lat'   => floatval($ubicacion->lat),
                    'lng'   => floatval($ubicacion->long),
                    'mapId' => 'mapActivo',
                ]);
                $this->pisosDisponibles($ubicacion);
            } else {
                // Si la ubicación no tiene lat/lng, podrías mostrar coords por defecto
                $this->dispatch('showActivoMap', [
                    'lat'   => -27.4799,
                    'lng'   => -58.8361,
                    'mapId' => 'mapActivo',
                ]);
            }
        }
    }

    private function cargarPiso()
    {
        if (! $this->selectedPiso) {
            return;
        }
        // 1) Si ya había un piso guardado, lo borramos
        if ($this->pisoActualId) {
            PisosModel::destroy($this->pisoActualId);
            $this->pisoActualId = null;
        }
        // 2) Creamos el nuevo
        $nuevo = PisosModel::create([
            'id_ubicacion' => $this->id_ubicacion,
            'nombre'       => $this->selectedPiso,
        ]);

        // 3) Guardamos su id en la propiedad
        $this->pisoActualId = $nuevo->id_piso;
    }

    private function pisosDisponibles($ubicacion)
    {
        if (! $ubicacion->multipisos) {
            $this->pisosDisponible  = [];
            return;
        }
        $max = 1 + (int) $ubicacion->piso;  // p. ej. 4 si hay PB + 3 pisos
        $options = [];
        // Generamos manualmente cada valor
        for ($i = 0; $i < $max; $i++) {
            if ($i === 0) {
                $options[] = 'Planta Baja';      // Planta baja
            } else {
                $options[] = 'Piso ' . $i;   // "1", "2", "3", ...
            }
        }
        $this->pisosDisponible = $options;
    }

    private function activosQr()
    {
        $baseUrl = config('app.url');

        $encryptedId = Crypt::encrypt($this->activo->id_activo);

        $encodedId = urlencode($encryptedId);

        $url = $baseUrl . '/datos-activos/' . $encodedId;

        $this->linkbien = $url;

        $this->qrUrl = 'https://quickchart.io/qr?text=' . urlencode($url) . '&size=400';
    }

    private function modelos()
    {
        // Query con los 3 filtros obligatorios
        $query = ModelosModel::where('id_subcategoria', $this->id_subcategoria)
            ->where('id_categoria',   $this->id_categoria)
            ->where('id_tipo',        $this->id_tipo);

        if ($this->id_marca) {
            $query->where('id_marca', $this->id_marca);
        }

        $this->ListaModelos = $query->get();
    }

    private function marcas()
    {
        $this->ListaMarcas = MarcasModel::whereHas('modelos', function ($query) {
            $query->where('id_categoria', $this->id_categoria)
                ->where('id_subcategoria', $this->id_subcategoria)
                ->where('id_tipo', $this->id_tipo);
        })->get();
    }

    public function actualizarModelos()
    {
        $this->modelos();
    }

    public function setPadre(int $id)
    {
        $this->padreId = $id;
    }

    public $openModalAtributos = false;

    public function abrirModalAtributos()
    {
        $this->cargarAtributosDisponibles();
        $this->openModalAtributos = true;
    }

    public function cerrarModalAtributos()
    {
        $this->openModalAtributos = false;
    }

    private function cargarAtributosDisponibles()
    {
        // 1) Construir definiciones
        $defs = AtributosSubcategoriaModel::where([
            ['id_tipo', $this->id_tipo],
            ['id_categoria', $this->id_categoria],
            ['id_subcategoria', $this->id_subcategoria],
        ])->with(['atributo.tiposCampos', 'atributo.unidadMedida', 'atributo.valores'])->get();

        $resultado = [];
        foreach ($defs as $d) {
            $attr    = $d->atributo;
            $tipoRaw = strtolower($attr->tiposCampos->nombre ?? '');
            $tipo    = in_array($tipoRaw, ['numerico', 'número', 'numero']) ? 'Numerico'
                : (in_array($tipoRaw, ['fecha', 'date', 'datetime'])   ? 'Fecha' : 'Texto');

            $resultado[(int)$d->id_atributo] = [
                'nombre'      => $attr->nombre,
                'tipo'        => $tipo,
                'predefinido' => strtolower((string)$attr->predefinido) === 'si',
                'multiple'    => strtolower((string)$attr->SelectM)    === 'si',
                'valores'     => $attr->valores?->pluck('valor')->toArray()     ?? [],
                'ids'         => $attr->valores?->pluck('id_valor')->toArray()  ?? [],
            ];
        }

        // Exponer en el componente (esto faltaba)
        $this->atributosDisponibles = $resultado;

        // 2) Precargar valores actuales
        $rows = ActivosAtributosModel::where('id_activo', $this->id_activo)->get();
        $this->atributosDatos = [];

        foreach ($rows as $row) {
            $idA = (int)$row->id_atributo;
            $conf = $resultado[$idA] ?? null;
            if (!$conf) continue;

            if ($conf['predefinido']) {
                if ($conf['multiple']) {
                    if (!empty($row->campo_enum_id)) {
                        $ids = array_filter(array_map('trim', explode(',', $row->campo_enum_id)), fn($x) => $x !== '');
                        $this->atributosDatos[$idA] = array_map('strval', $ids);
                    } else {
                        $map = array_combine($conf['valores'], $conf['ids']);      // texto -> id
                        $ids = [];
                        foreach (array_map('trim', explode(',', (string)$row->campo_enum_list)) as $txt) {
                            if ($txt === '') continue;
                            $id = $map[$txt] ?? null;
                            if ($id) $ids[] = (string)$id;                         // ← strings
                        }
                        $this->atributosDatos[$idA] = array_values(array_unique($ids));
                    }
                } else {
                    $this->atributosDatos[$idA] = $row->campo_enum_id ? (int)$row->campo_enum_id : null; // ID único
                }
            } else {
                switch ($conf['tipo']) {
                    case 'Numerico':
                        $this->atributosDatos[$idA] = $row->campo_numerico;
                        break;
                    case 'Fecha':
                        $this->atributosDatos[$idA] = $row->fecha;
                        break;
                    default:
                        $this->atributosDatos[$idA] = $row->campo;
                        break;
                }
            }
        }
    }
    public $registro;
    public function guardarAtributos()
    {
        foreach ($this->atributosDisponibles as $idAtributo => $config) {
            $valor = $this->atributosDatos[$idAtributo] ?? null;

            // Claves de la fila
            $attrs = [
                'id_activo'              => $this->id_activo,
                'id_atributo'            => $idAtributo,
                'id_tipo_activo'         => $this->id_tipo,
                'id_categoria_activo'    => $this->id_categoria,
                'id_subcategoria_activo' => $this->id_subcategoria,
            ];

            // Inicializo todos los campos en null (evita “basura” de ediciones previas)
            $data = [
                'campo'           => null,
                'campo_numerico'  => null,
                'fecha'           => null,
                'campo_enum'      => null,
                'campo_enum_list' => null,
                'campo_enum_id'   => null,
            ];

            // --- PREDEFINIDOS---
            if (!empty($config['predefinido'])) {
                // Mapeos para convertir id <-> texto
                $ids     = array_map('strval', $config['ids']     ?? []);
                $valores = array_map('strval', $config['valores'] ?? []);
                $idToTxt = array_combine($ids, $valores);
                $txtToId = array_combine($valores, $ids);

                if (!empty($config['multiple'])) {
                    $idsSel = is_array($valor)
                        ? array_map('strval', $valor)
                        : ($valor === null || $valor === '' ? [] : array_map('trim', explode(',', (string)$valor)));

                    // normalizo y elimino vacíos/duplicados
                    $idsSel  = array_values(array_unique(array_filter($idsSel, fn($x) => $x !== '')));
                    $txtSel  = array_values(array_filter(array_map(fn($id) => $idToTxt[(string)$id] ?? null, $idsSel)));

                    $data['campo_enum_id']   = $idsSel ? implode(',', $idsSel) : null;   // "34,37"
                    $data['campo_enum_list'] = $txtSel ? implode(',', $txtSel) : null;   // "WiFi,Automatico"
                } else {
                    // único: $valor puede llegar como ID o como texto
                    $id  = null;
                    $txt = null;

                    if (is_array($valor)) {
                        $id = $valor[0] ?? null;
                    } else {
                        // si viene numérico/string que es ID, usamos ese; si es texto, lo mapeamos
                        $maybe = (string)$valor;
                        if ($maybe === '') {
                            $id = null;
                        } elseif (isset($idToTxt[$maybe])) {
                            $id = $maybe;
                        } elseif (isset($txtToId[$maybe])) {
                            $id = $txtToId[$maybe];
                        }
                    }

                    $id  = $id === '' ? null : ($id !== null ? (string)$id : null);
                    $txt = $id !== null ? ($idToTxt[$id] ?? null) : null;

                    $data['campo_enum_id'] = $id;   // "34"
                    $data['campo_enum']    = $txt;  // "WiFi"
                }
            } else {
                $tipo = $config['tipo'] ?? 'Texto';
                switch ($tipo) {
                    case 'Numerico':
                        $data['campo_numerico'] = ($valor === '' ? null : $valor);
                        break;
                    case 'Fecha':
                        $data['fecha'] = ($valor === '' ? null : $valor);
                        break;
                    default: // Texto
                        $data['campo'] = ($valor === '' ? null : $valor);
                        break;
                }
            }

            // Guardar (usa el WHERE compuesto: no toca otros activos)
            DB::table('act.activos_atributos')->updateOrInsert($attrs, $data);
        }

        $this->cerrarModalAtributos();
        $this->atributos = $this->atributosActivos($this->activo);
    }
}
