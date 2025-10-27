<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use App\Traits\AuditableEmpresa;

class UsuariosEmpresasModel extends Model
{
    use HasFactory, AuditableEmpresa;
    public $timestamps = false;
    const TABLE = 'act.usuarios_empresas';
    protected $table = self::TABLE;
    protected $fillable =
    [
        'id_relacion',
        'id_usuario',
        'cuit',
        'cargo',
        'estado',
        'legajo',
        'tipo_user',
        'tipo_inter_exter',
        'es_representante_tecnico',
        'id_Nivel_Organizacion',
        'supervisor',
        'supervisor_usuario',
    ];
    protected $primaryKey = 'id_relacion';

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        // === Configuracin de auditoria ===
        $this->claseAuditoria = 'Usuarios Empresas';
        // $this->camposIncluirAuditoria = ['id_estado_sit_general','usuario_titular','empresa_titular','id_ubicacion','prestado','garantia_vigente'];
        $this->camposExcluirAuditoria = ['id_relacion', 'tipo_inter_exter', 'legajo'];
        // Comportamiento:
        $this->creacionCompacta = true;        // 1 registro en alta (campo='CREACION', evento='Creacion')
        $this->actualizacionCompacta = false;  // si lo pones true => 1 registro por update con JSON
    }

    public function usuarios()
    {
        return $this->belongsTo(User::class, 'id_usuario', 'id');
    }

    public function permisos()
    {
        return $this->hasOne(AsignacionesRolesModel::class, 'id_relacion_empresa', 'id_relacion');
    }

    public function contrato()
    {
        return $this->belongsTo(ContratoInterPrestadoraModel::class, 'id_relacion', 'id_relacion');
    }

    public function nivelOrganizacion()
    {
        return $this->belongsTo(OrganizacionUnidadesModel::class, 'id_Nivel_Organizacion', 'Id');
    }

    /**
     * Booted hook: cada vez que se actualice
     * una asignación de rol, invalidamos la caché de “Admin Empresa” asi evitamos que quede por 15 minutos con su rol antiguo .
     */
    protected static function booted()
    {
        // Se dispara únicamente cuando un registro existente se actualiza
        static::updated(function (UsuariosEmpresasModel $registro) {
            $usuario = $registro->id_usuario;
            $empresa = $registro->cuit;
            Cache::forget("ctx_apoderado_{$usuario}_{$empresa}");
        });
    }
}
