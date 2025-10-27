<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UnidadModel extends Model
{
    use HasFactory;
    public $timestamps = false;
    const TABLE = 'act.unidad_medida';
    protected $table = self::TABLE;
    protected $primaryKey = "id_unidad_medida";
    

    protected $fillable = ['nombre'];
}
