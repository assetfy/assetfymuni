<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrdenesBienesMaterialModel extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $table = 'act.ordenes_bienes_materiales ';
    protected $primaryKey = 'id_ob_material';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'id_orden',
        'id_material',
        'nombre_material',
        'cantidad',
        'precio_unitario',
        'moneda',
        'origen',
        'id_contrato',
        'id_contrato_servicio_material ',
        'fecha_carga',
        'cargado_por ',
    ];
}
