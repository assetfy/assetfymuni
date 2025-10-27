<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrdenesPorgramacionModel extends Model
{
    use HasFactory;
    public $timestamps = false;
    const TABLE = 'act.ordenes_programaciones';
    protected $table = self::TABLE;
    protected $fillable =
    [
        'id_orden',
        'fecha_inicio',
        'fecha_fin',
        'periodicidad',
        'fechas_periodicidad',
    ];
    
    protected $primaryKey = 'id_programacion';
}
