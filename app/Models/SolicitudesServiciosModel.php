<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class SolicitudesServiciosModel extends Model
{
    use HasFactory;
    public $timestamps = false;
    const TABLE = 'act.solicitudes_servicios';
    protected $table = self::TABLE;
    protected $fillable = [
        'id_servicio',
        'id_activo',
        'id_categoria',
        'id_subcategoria',
        'id_tipo',
        'empresa_prestadora',
        'empresa_solicitante',
        'id_solicitante',
        'fechaHora',
        'fecha_modificada',
        'descripcion',
        'estado',
        'presupuesto',
        'estado_presupuesto',
        'precio',
        'motivo_cancelacion',
        'garantia',
        'dias_garantia',
        'fecha_modificada',
        'fecha_finalizacion',
        'Nombre_solicitud',
        'id_tipo_solicitud',
        ' tecnico_id'
    ];

    protected $appends = [
        'cotizacion',        // para precio
        'garantia_display',  // para garantía
    ];

    protected $primaryKey = "id_solicitud";

    public function servicios()
    {
        return $this->belongsTo(ServiciosModel::class, 'id_servicio', 'id_servicio');
    }

    public function tipos()
    {
        return $this->belongsTo(TiposModel::class, 'id_tipo', 'id_tipo');
    }

    public function categorias()
    {
        return $this->belongsTo(CategoriaModel::class, 'id_categoria', 'id_categoria');
    }

    public function subcategorias()
    {
        return $this->belongsTo(SubcategoriaModel::class, 'id_subcategoria', 'id_subcategoria');
    }

    public function activos()
    {
        return $this->belongsTo(ActivosModel::class, 'id_activo', 'id_activo');
    }

    public function empresasSolicitantes()
    {
        return $this->belongsTo(EmpresasModel::class, 'empresa_solicitante', 'cuit');
    }

    public function empresasPrestadora()
    {
        return $this->belongsTo(EmpresasModel::class, 'empresa_prestadora', 'cuit');
    }

    public function tipoServicio()
    {
        return $this->belongsTo(TiposSolicitudModel::class, 'id_tipo_solicitud', 'id_tipo_solicitud');
    }
    public function serviciosFoto()
    {
        return $this->belongsTo(FotosServicioModel::class, 'id_solicitud', 'id_solicitud');
    }

    public function users()
    {
        return $this->belongsTo(User::class, 'id_solicitante', 'id');
    }

    public function canBeSelected(): bool
    {
        return $this->estado_presupuesto !== 'Aceptado' && $this->estado_presupuesto !== 'Rechazado';
    }

    // public function getEstadoPesupuestoAttribute($value)
    // {
    //     if ($value === 'Cerrado') {
    //         return 'aasfda';
    //     }

    //     // if ($value === 'No') {
    //     //     return 'Rechazado';
    //     // }

    //     // return $value ? $value : 'Esperando confirmación';
    // }

    public function getCotizacionAttribute(): string
    {
        if (empty($this->precio) || $this->precio == 0) {
            return 'Sin datos';
        }

        return '$' . number_format($this->precio, 2) . '';
    }

    public function getGarantiaDisplayAttribute(): string
    {
        // Solo mostramos garantía si está marcada y tiene días
        if ($this->garantia == 'Si' && ! empty($this->dias_garantia)) {
            return $this->dias_garantia . ' días';
        }

        return 'Sin Garantia';
    }

    public function actualizarGarantias()
    {
        DB::table(self::TABLE)
            ->where('garantia', 'Si') // Garantías activas
            ->whereNotNull('dias_garantia') // Días de garantía definidos
            ->update([
                'dias_garantia' => DB::raw("
                CASE 
                    WHEN GETDATE() < ISNULL(fecha_modificada, fechaHora) THEN dias_garantia
                    WHEN DATEDIFF(DAY, ISNULL(fecha_modificada, fechaHora), GETDATE()) > dias_garantia THEN 0
                    ELSE dias_garantia - DATEDIFF(DAY, ISNULL(fecha_modificada, fechaHora), GETDATE())
                END
            "),
                'garantia' => DB::raw("
                CASE 
                    WHEN GETDATE() < ISNULL(fecha_modificada, fechaHora) THEN garantia
                    WHEN DATEDIFF(DAY, ISNULL(fecha_modificada, fechaHora), GETDATE()) >= dias_garantia THEN 'No'
                    ELSE garantia
                END
            ")
            ]);
    }

    public function getTipoClienteAttribute()
    {
        $existeIndividual = $this->id_solicitante
            ? \App\Models\ServiciosActivosModel::where('id_usuario', $this->id_solicitante)
            ->where('proveedor', $this->empresa_prestadora)
            ->exists()
            : false;

        // Obtener todos los usuarios asociados a la empresa solicitante
        $usuarios = UsuariosEmpresasModel::where('cuit', $this->empresa_solicitante)->get();

        // Extraer los id_relacion de la colección
        $idsRelacion = $usuarios->pluck('id_relacion')->toArray();

        $existeEmpresa = $this->empresa_solicitante && !empty($idsRelacion)
            ? \App\Models\ServiciosActivosModel::whereIn('id_relacion_usuario', $idsRelacion)->exists()
            : false;

        return ($existeIndividual || $existeEmpresa) ? 'Cliente' : 'Nuevo';
    }

    public function tecnico()
    {
        return $this->belongsTo(User::class, 'tecnico_id', 'id');
    }

    // app/Models/SolicitudesServiciosModel.php

    // al final de la clase
    public function getEncargadoAttribute(): string
    {
        return optional($this->tecnico)->name ?? 'Sin encargado';
    }
}
