<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TiposUsuariosModel extends Model
{
    use HasFactory;
    public $timestamps = false;
    const TABLE = 'act.tipos_usuarios';
    protected $table = self::TABLE;
    protected $primaryKey = "id_tipo_usuarios";
    protected $fillable = ['nombre','descripcion'];
}
