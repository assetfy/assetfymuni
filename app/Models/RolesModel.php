<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RolesModel extends Model
{
    use HasFactory;
    public $timestamps = false;
    const TABLE = 'act.roles';
    protected $table = self::TABLE;
    protected $fillable = ['nombre', 'cuit', 'tipo_empresa'];
    protected $primaryKey = "id_rol";

    public function empresasparticulares()
    {
        return $this->belongsTo(EmpresasModel::class, 'cuit', 'cuit');
    }
}
