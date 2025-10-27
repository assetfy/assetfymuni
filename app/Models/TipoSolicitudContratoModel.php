<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipoSolicitudContratoModel extends Model
{
    use HasFactory;
    const TABLE = 'act.tipo_solicitud_contrato';
    protected $table = self::TABLE;
    protected $primaryKey = "id_tipo_sol_contrato";

    protected $fillable = ['id_contrato', 'id_tipo_solicitud'];
}
