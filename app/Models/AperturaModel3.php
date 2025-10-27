<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class AperturaModel3 extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $table = 'act.aperturas_3';
    protected $primaryKey = 'id_apertura_3';
    protected $fillable = ['nombre', 'nombre_apertura_4', 'id_ubicacion','id_apertura_1','id_apertura_2']; 
}