<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PermisoPorEmpresaModel extends Model
{
    use HasFactory;
    public $timestamps = false;
    const TABLE = 'act.permisos_por_tipo_empresa';
    protected $table = self::TABLE;
    protected $fillable = ['id_permiso', 'tipo_empresa', 'id_ruta', 'id_config_ruta', 'cuit_empresa', 'con_configuracion'];
    protected $primaryKey = "id_asignacion_por_empresa";


    public function permisos()
    {
        return $this->belongsTo(PermisosModel::class, 'id_permiso', 'id_permiso');
    }

    public function rutas()
    {
        return $this->belongsTo(RutasModel::class, 'id_ruta', 'id_ruta');
    }
}
