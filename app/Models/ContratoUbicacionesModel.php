<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContratoUbicacionesModel extends Model
{
    use HasFactory;
    public $timestamps = false;
    const TABLE = 'act.contrato_ubicaciones';
    protected $table = self::TABLE;
    protected $primaryKey = "id_contrato_ubicaciones";

    protected $fillable = ['id_contrato', 'id_ubicacion'];
}
