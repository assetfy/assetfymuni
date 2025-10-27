<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GruposModel extends Model
{
    public $timestamps = false;
    use HasFactory;
    const TABLE = 'act.grupos';
    protected $table = self::TABLE;
    protected $primaryKey = "id_grupo";
    protected $fillable = ['cuit', 'nombre', 'descripcion'];
}
