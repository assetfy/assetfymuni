<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EstadoGeneralModel extends Model
{
    use HasFactory;
    public $timestamps = false;
    const TABLE = 'act.estados_general';
    protected $table = self::TABLE;
    protected $primaryKey = "id_estado_sit_general";
    
    protected $fillable = ['nombre', 'descripcion'];
}
