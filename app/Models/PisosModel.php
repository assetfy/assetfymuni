<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PisosModel extends Model
{
    use HasFactory;
    public $timestamps = false;
    const TABLE = 'act.pisos';
    protected $table = self::TABLE;
    protected $fillable = ['id_ubicacion', 'nombre'];
    protected $primaryKey = "id_piso";
}
