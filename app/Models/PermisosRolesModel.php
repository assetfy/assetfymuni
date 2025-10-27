<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PermisosRolesModel extends Model
{
    use HasFactory;
    public $timestamps = false;
    const TABLE = 'act.permisos_roles';
    protected $table = self::TABLE;
    protected $fillable = ['id_rol', 'id_permiso','cuit_empresa'];
    protected $primaryKey = "id_unico";


    public function roles()
    {
        return $this->belongsTo(RolesModel::class, 'id_rol', 'id_rol');
    }

    public function permisos()
    {
        return $this->belongsTo(PermisosModel::class, 'id_permiso', 'id_permiso');
    }
}
