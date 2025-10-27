<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContratoServicioModel extends Model
{
    use HasFactory;
    public $timestamps = false;
    const TABLE = 'act.contrato_servicio';
    protected $table = self::TABLE;
    protected $primaryKey = "id_contrato_servicio";

    protected $fillable = ['id_contrato', 'id_servicio', 'precio_unitario', 'moneda', 'estado'];
}
