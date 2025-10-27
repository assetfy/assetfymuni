<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrdenCierreModel extends Model
{
    use HasFactory;
    public $timestamps = false;
    const TABLE = 'act.ordenes_cierre';
    protected $table = self::TABLE;
    protected $fillable =
    [
        'id_orden',
        'incluye_materiales',
        'mano_de_obra',
        'hora_llegada',
        'hora_retiro',
        'comentarios',
        'fecha_expiracion_garantia_servicio',
        'fecha_expiracion_garantia_repuestos'

    ];
    protected $primaryKey = 'id_cierre';
    protected $casts = [
        // la columna es DATE, guardamos solo fecha
        'fecha_expiracion_garantia_servicio'  => 'date:Y-m-d',
        'fecha_expiracion_garantia_repuestos' => 'date:Y-m-d',
        // asumimos columnas TIME o DATETIME, pero solo HH:MM
        'hora_llegada' => 'datetime:H:i',
        'hora_retiro'  => 'datetime:H:i',
    ];
}
