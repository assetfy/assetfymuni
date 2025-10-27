<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PermisosModel extends Model
{
    use HasFactory;
    public $timestamps = false;
    const TABLE = 'act.permisos';
    protected $table = self::TABLE;
    protected $fillable = ['nombre', 'tipo_permiso', 'cuit_empresa'];
    protected $primaryKey = "id_permiso";
}
