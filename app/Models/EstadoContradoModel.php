<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EstadoContradoModel extends Model
{
    use HasFactory;
    const TABLE = 'act.estado_contrato';
    protected $table = self::TABLE;
    protected $primaryKey = "id_estado_contrato";

    protected $fillable = ['nombre_estado'];
}
