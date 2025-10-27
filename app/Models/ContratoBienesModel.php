<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContratoBienesModel extends Model
{
    use HasFactory;
    public $timestamps = false;
    const TABLE = 'act.contrato_bienes';
    protected $table = self::TABLE;
    protected $primaryKey = "id_contrato_bienes";

    protected $fillable = ['id_contrato', 'id_activo', 'id_tipo', 'id_categoria', 'id_subcategoria'];
}
