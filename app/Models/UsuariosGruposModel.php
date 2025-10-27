<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UsuariosGruposModel extends Model
{
    public $timestamps = false;
    use HasFactory;
    const TABLE = 'act.usuarios_grupos';
    protected $table = self::TABLE;
    protected $fillable = ['id_usuario', 'id_relacion', 'cuit', 'id_grupo'];
}
