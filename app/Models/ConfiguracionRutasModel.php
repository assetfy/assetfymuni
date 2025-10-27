<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConfiguracionRutasModel extends Model
{
    use HasFactory;
    public $timestamps = false;
    const TABLE = 'act.config_rutas';
    protected $table = self::TABLE;
    protected $fillable = [
        'id_ruta',
        'nombre_config',
        'atributos',
        'cuit_empresa',
    ];
    protected $primaryKey = "id_config_ruta";
    protected $keyType = 'string';
}
