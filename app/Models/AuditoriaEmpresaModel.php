<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuditoriaEmpresaModel extends Model
{
    public $timestamps = false;
    protected $table = 'act.auditorias_empresa';
    protected $primaryKey = 'id_auditoria';

    protected $fillable = [
        'clase_tabla',
        'pk_nombre',
        'id_tabla',
        'campo',
        'valor_previo',
        'valor_actual',
        'evento',
        'fechahora_cambio',
        'autor',
        'autor_empresa',
        'ip',
        'cuit_empresa'
    ];

    protected $casts = [
        'fechahora_cambio' => 'datetime',
    ];
}
