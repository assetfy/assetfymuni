<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RutasModel extends Model
{
    use HasFactory;
    public $timestamps = false;
    const TABLE = 'act.rutas';
    protected $table = self::TABLE;
    protected $fillable = ['nombre', 'ruta', 'configurable'];
    protected $primaryKey = "id_ruta";
}
