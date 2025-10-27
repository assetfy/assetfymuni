<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MaterialesModel extends Model
{
    use HasFactory;
    public $timestamps = false;
    const TABLE = 'act.materiales';
    protected $table = self::TABLE;
    protected $fillable = ['codigo_interno', 'nombre', 'unidad', 'descripcion', 'estado'];
    protected $primaryKey = "id_material";
}
