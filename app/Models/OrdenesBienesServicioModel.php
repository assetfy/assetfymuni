<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrdenesBienesServicioModel extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $table = 'act.ordenes_bienes_servicios';
    protected $primaryKey = 'id_ob_servicio';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'id_orden_bien',
        'id_servicio',
        'descripcion_servicio',
        'horas',
        'precio_unitario',
        'moneda',
        'origen ',
        'id_contrato',
        'id_contrato_servicio ',
        'fecha_carga',
        'cargado_por ',
    ];
}
