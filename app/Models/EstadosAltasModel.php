<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EstadosAltasModel extends Model
{
    use HasFactory;
    public $timestamps = false;
    const TABLE = 'act.estados_altas';
    protected $table = self::TABLE;
    protected $primaryKey = "id_estado_sit_alta";
    
    protected $fillable = ['nombre', 'descripcion'];
}
