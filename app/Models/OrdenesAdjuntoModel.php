<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrdenesAdjuntoModel extends Model
{
    use HasFactory;
    public $timestamps = false;
    const TABLE = 'act.ordenes_adjuntos';
    protected $table = self::TABLE;
    protected $fillable =
    [
        'id_orden',
        'id_adjunto',
        'nombre_archivo',
        'ruta_archivo',
        'fecha_subida',
        'tipo',
    ];
    protected $primaryKey = 'id_adjunto';
}
