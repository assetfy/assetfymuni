<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GruposRolesModel extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $table = 'act.grupos_roles';

    // Si la tabla no tiene una columna autoincremental única y usamos clave compuesta,
    // indicamos que no es autoincremental.
    public $incrementing = false;

    // No definimos primaryKey para evitar conflictos, o podrías definir un valor ficticio.
    // protected $primaryKey = null;

    // Asegurate de incluir ambos campos que vas a insertar:
    protected $fillable = ['id_rol', 'id_grupo'];
}
