<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContratoInterPrestadoraModel extends Model
{
    use HasFactory;
    public $timestamps = false;
    const TABLE = 'act.contrato_inter_prestadora';
    protected $table = self::TABLE;
    protected $fillable =
    [
        'id_contrato',
        'id_relacion',
        'id_usuario',
        'cuil_empresa',
        'nmro_contrato',
        'cuil_prestadora',
    ];
    protected $primaryKey = 'id_contrato';
}
