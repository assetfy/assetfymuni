<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiciosModel extends Model
{
    use HasFactory;
    public $timestamps = false;
    const TABLE = 'act.servicios';
    protected $table = self::TABLE;
    protected $fillable = ['nombre', 'descripcion'];
    protected $primaryKey = "id_servicio";
}