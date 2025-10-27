<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContratoServicioMaterialModel extends Model
{
    use HasFactory;
    const TABLE = 'act.contrato_servicio_material';
    protected $table = self::TABLE;
    public $timestamps = false;
    protected $primaryKey = "id_contrato_servicio_material";

    protected $fillable = ['id_contrato_servicio', 'id_material'];
}
