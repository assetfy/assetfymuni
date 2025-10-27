<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrdenesBienesAdjuntoModel extends Model
{
    protected $table = 'act.ordenes_Bienes_adjuntos';
    protected $primaryKey = 'id_adjunto';
    public $timestamps = false;

    protected $fillable = [
        'id_orden_bien',
        'nombre_archivo',
        'ruta_archivo',
        'tipo',
        'fecha_subida',
        'subido_por',
    ];
}
