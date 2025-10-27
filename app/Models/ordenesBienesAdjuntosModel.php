<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ordenesBienesAdjuntosModel extends Model
{
    use HasFactory;
    public $timestamps = false;
    const TABLE = 'act.ordenes_Bienes_adjuntos';
    protected $table = self::TABLE;
    protected $fillable =
    [
        'id_orden_bien',
        'nombre_archivo',
        'ruta_archivo',
        'fecha_subido',
        'subido_por',
    ];
    protected $primaryKey = 'id_adjunto';
}
