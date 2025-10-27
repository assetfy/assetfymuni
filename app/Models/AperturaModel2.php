<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class AperturaModel2 extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $table = 'act.aperturas_2';
    protected $primaryKey = 'id_apertura_2';
    protected $fillable = ['nombre', 'nombre_apertura_3', 'id_ubicacion','id_apertura_1']; 
}