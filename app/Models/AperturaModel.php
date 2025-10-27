<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class AperturaModel extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $table = 'act.aperturas_4';
    protected $primaryKey = 'id_apertura_4';
    protected $fillable = ['nombre','id_ubicacion','id_apertura_1','id_apertura_2','id_apertura_3']; 
}