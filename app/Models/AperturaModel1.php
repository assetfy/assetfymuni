<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class AperturaModel1 extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $table = 'act.aperturas_1';
    protected $primaryKey = 'id_apertura_1';
    protected $fillable = ['nombre', 'nombre_apertura_2', 'id_ubicacion']; 
}

