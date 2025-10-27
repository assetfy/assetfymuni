<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContratoModel extends Model
{
    use HasFactory;
    const TABLE = 'act.contrato';
    public $timestamps = false;
    protected $table = self::TABLE;
    protected $primaryKey = "id_contrato";

    protected $fillable = ['nro_contrato', 'nombre', 'cuit_cliente', 'prestadora', 'fecha_inicio', 'fecha_fin', 'id_estado_contrato', 'fecha_creacion', 'monto', 'moneda', 'contrato_file', 'id_tipo_contrato'];

    public function tiposContratos()
    {
        return $this->belongsTo(ContratosTiposModel::class, 'id_tipo_contrato', 'id_tipo_contrato');
    }

    public function prestadoras()
    {
        return $this->belongsTo(EmpresasModel::class, 'prestadora', 'cuit');
    }

    public function estadoContrato()
    {
        return  $this->belongsTo(EstadoContradoModel::class, 'id_estado_contrato', 'id_estado_contrato');
    }
}
