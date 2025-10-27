<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContratosTiposModel extends Model
{
    use HasFactory;
    public $timestamps = false;
    const TABLE = 'act.tipo_contrato';
    protected $table = self::TABLE;
    protected $primaryKey = "id_tipo_contrato";

    protected $fillable = ['nombre'];
}
