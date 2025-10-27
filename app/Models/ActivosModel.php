<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Traits\AuditableEmpresa;

class ActivosModel extends Model
{
    use HasFactory, AuditableEmpresa;

    public $timestamps = false;

    const TABLE = 'act.activos';
    protected $table = self::TABLE;
    protected $primaryKey = 'id_activo';


    protected $fillable = [
        'etiqueta',
        'numero_serie',
        'propietario',
        'id_subcategoria',
        'id_categoria',
        'id_tipo',
        'nombre',
        'id_estado_sit_alta',
        'comentarios_sit_alta',
        'estado_inventario',
        'motivo_baja',
        'id_estado_sit_general',
        'usuario_titular',
        'empresa_titular',
        'id_ubicacion',
        'imagen',
        'fecha_compra',
        'factura_compra',
        'garantia_vigente',
        'vencimiento_garantia',
        'id_externo',
        'cert_garantia',
        'id_modelo',
        'id_piso',
        'fecha_creacion',
        'id_Nivel_Organizacion',
        'id_condicion',
        'prestado'
    ];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        // === Configuracin de auditoria ===
        $this->claseAuditoria = 'bien';
        // $this->camposIncluirAuditoria = ['id_estado_sit_general','usuario_titular','empresa_titular','id_ubicacion','prestado','garantia_vigente'];
        $this->camposExcluirAuditoria = ['fecha_creacion', 'imagen', 'factura_compra', 'cert_garantia'];

        // Comportamiento:
        $this->creacionCompacta = true;        // 1 registro en alta (campo='CREACION', evento='Creacion')
        $this->actualizacionCompacta = false;  // si lo pones true => 1 registro por update con JSON
    }


    // Relaciones

    public function fotoPortada()
    {
        $t = (new ActivosFotosModel)->getTable(); // "act.activo_fotos"

        return $this->hasOne(ActivosFotosModel::class, 'id_activo', 'id_activo')
            ->ofMany('id_foto', 'min') // ó 'max' si querés la más nueva
            // Seleccioná desde la tabla hija, calificado y (opcional) alias:
            ->select([
                "{$t}.id_activo as id_activo", // <- evita ambigüedad
                "{$t}.id_foto",
                "{$t}.ruta_imagen",
            ]);
    }


    // Relación con SubcategoriaModel
    public function subcategoria()
    {
        return $this->belongsTo(SubcategoriaModel::class, 'id_subcategoria', 'id_subcategoria');
    }

    // Relación con TiposModel
    public function tipo()
    {
        return $this->belongsTo(TiposModel::class, 'id_tipo', 'id_tipo');
    }

    // Relación con CategoriaModel
    public function categoria()
    {
        return $this->belongsTo(CategoriaModel::class, 'id_categoria', 'id_categoria');
    }

    // Relación con EstadoGeneralModel
    public function estadoGeneral()
    {
        return $this->belongsTo(EstadoGeneralModel::class, 'id_estado_sit_general', 'id_estado_sit_general');
    }

    // Relación con EstadosAltasModel
    public function estadoAlta()
    {
        return $this->belongsTo(EstadosAltasModel::class, 'id_estado_sit_alta', 'id_estado_sit_alta');
    }

    // Relación con UbicacionesModel
    public function ubicacion()
    {
        return $this->belongsTo(UbicacionesModel::class, 'id_ubicacion', 'id_ubicacion');
    }

    // Relación con ActivosControlesModel
    public function activosControles()
    {
        return $this->hasMany(ActivosControlesModel::class, 'id_activo', 'id_activo');
    }

    // Relación con ActivosAtributosModel
    public function activosAtributos()
    {
        return $this->hasMany(ActivosAtributosModel::class, 'id_activo', 'id_activo');
    }

    // Relación con User (usuario titular)
    public function usuarioTitular()
    {
        return $this->belongsTo(User::class, 'usuario_titular', 'cuil');
    }

    // Relación con EmpresasModel (empresa titular)
    public function empresaTitular()
    {
        return $this->belongsTo(EmpresasModel::class, 'empresa_titular', 'cuit');
    }

    // Relación con ServiciosSubcategoriasModel
    public function serviciosSubcategoria()
    {
        return $this->belongsTo(ServiciosSubcategoriasModel::class, 'id_subcategoria', 'id_subcategoria');
    }

    // Relación con ActivosAsignacionModel
    public function asignaciones()
    {
        return $this->hasMany(ActivosAsignacionModel::class, 'id_activo', 'id_activo');
    }

    // Relación con Fotos
    public function fotos()
    {
        return $this->hasMany(ActivosFotosModel::class, 'id_activo', 'id_activo');
    }

    // Relacion Usuarios Empresas
    public function tipo_user()
    {
        return $this->hasMany(UsuariosEmpresasModel::class, 'cuit', 'empresa_titular');
    }

    // Relacion Activos Compartidos
    public function compartidos()
    {
        return $this->hasMany(ActivosCompartidosModel::class, 'id_activo', 'id_activo');
    }

    // Métodos para obtener datos de las vistas

    public static function getListaActivosNormal($userId)
    {
        return DB::table('lista_activos_normal')
            ->Where('empresa_titular', $userId)
            ->get();
    }

    public static function getListaActivosBaja($userId)
    {
        return DB::table('lista_activos_baja')
            ->Where('empresa_titular', $userId)
            ->get();
    }

    public static function getListaActivos($userId)
    {
        return DB::table('lista_activos')
            ->Where('empresa_titular', $userId)
            ->get();
    }

    public static function getCotizaciones($userId)
    {
        return DB::table('lista_cotizaciones_solicitadas')
            ->where('usuario_titular', $userId)
            ->get();
    }

    public static function getServicios($userId)
    {
        return DB::table('lista_servicios_efectuados')
            ->where('usuario_titular', $userId)
            ->orWhere('empresa_titular', $userId)
            ->get();
    }

    public static function getListaUbicaciones($userId)
    {
        return DB::table('lista_ubicaciones')
            ->where('cuil', $userId)
            ->orWhere('cuit', $userId)
            ->get();
    }

    public static function getCalificaciones($userId)
    {
        return DB::table('vista_servicios_resenia')
            ->where('usuario_titular', $userId)
            ->orWhere('empresa_titular', $userId)
            ->get();
    }

    public static function getCalificacionesFaltante($userId)
    {
        return DB::table('vista_servicios_sin_resenia')
            ->where('usuario_titular', $userId)
            ->orWhere('empresa_titular', $userId)
            ->get();
    }

    public function actualizarGarantias()
    {
        DB::table(self::TABLE)
            ->whereNotNull('vencimiento_garantia') // Solo registros con vencimiento definido
            ->update([
                // Actualizar 'garantia_vigente' y 'vencimiento_garantia' dependiendo del cálculo
                'garantia_vigente' => DB::raw("
                CASE 
                    WHEN DATEDIFF(DAY, fecha_compra, GETDATE()) >= DATEDIFF(DAY, fecha_compra, vencimiento_garantia) THEN 'No'
                    ELSE 'Si'
                END
            "),
                'vencimiento_garantia' => DB::raw("
                CASE 
                    WHEN DATEDIFF(DAY, fecha_compra, GETDATE()) >= DATEDIFF(DAY, fecha_compra, vencimiento_garantia) THEN NULL
                    ELSE vencimiento_garantia
                END
            ")
            ]);
    }

    // Para obtener el responsable asignado 
    public function responsableAsignado()
    {
        return $this->hasOneThrough(
            User::class,
            ActivosAsignacionModel::class,
            'id_activo', // Foreign key on asignacion
            'id',        // Foreign key on users
            'id_activo',        // Local key on activos
            'responsable' // Local key on asignacion
        )
            ->where('estado_asignacion', 'Aceptado')
            ->whereNull('fecha_fin_asignacion');
    }

    public function nivelOrganizacion()
    {
        return $this->belongsTo(
            OrganizacionUnidadesModel::class,
            'id_Nivel_Organizacion',  // FK en activos
            'Id'                      // PK en OrganizacionUnidades
        );
    }
    // Para obtener el usuario asignado
    public function usuarioAsignado()
    {
        return $this->hasOneThrough(
            User::class,
            ActivosAsignacionModel::class,
            'id_activo', // Foreign key on asignacion
            'id',        // Foreign key on users
            'id_activo',        // Local key on activos
            'asignado_a' // Local key on asignacion
        )
            ->where('estado_asignacion', 'Aceptado')
            ->whereNull('fecha_fin_asignacion');
    }
}
