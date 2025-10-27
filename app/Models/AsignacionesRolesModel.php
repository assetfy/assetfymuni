<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class AsignacionesRolesModel extends Model
{
    use HasFactory;
    public $timestamps = false;
    const TABLE = 'act.asignaciones_roles';
    protected $table = self::TABLE;
    protected $primaryKey = "id_asignacion";
    protected $fillable = ['id_rol', 'cuit', 'usuario_empresa', 'id_relacion_empresa'];

    public function roles()
    {
        return $this->belongsTo(RolesModel::class, 'id_rol', 'id_rol');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'usuario_empresa', 'id');
    }

    public function empresa()
    {
        return $this->belongsTo(EmpresasModel::class, 'cuit', 'cuit');
    }

    public function usuarioEmpresa()
    {
        return $this->belongsTo(UsuariosEmpresasModel::class, 'id_relacion_empresa', 'id_relacion');
    }

    protected static function booted()
    {
        // Cuando se crea o actualiza (saved cubre ambos casos)
        static::saved(function ($asignacion) {
            // si usas cache normal:
            Cache::forget('asignaciones_roles_all');
            // o si usas cache con tags:
            // Cache::tags('asignaciones_roles')->flush();
        });

        // Cuando se elimina
        static::deleted(function ($asignacion) {
            Cache::forget('asignaciones_roles_all');
            // o con tags:
            // Cache::tags('asignaciones_roles')->flush();
        });
    }
}
