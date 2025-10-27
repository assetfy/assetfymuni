<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiciosActividadesEconomicasModel extends Model
{
    public $timestamps = false;
    use HasFactory;
    const TABLE = 'act.servicios_actividades_economicas';
    protected $table = self::TABLE;
    protected $primaryKey = "id_servicios_actividades_economicas";

    protected $fillable = ['cod_actividad',
                            'id_servicio', 
                            'cuit_municipio', 
                            'localidad', 
                            'tiene_vencimiento', 
                            'mensual_o_x_dias', 
                            'cantidad_dias_o_meses',
                            'es_regulada'];

    public function servicios()
    {
        return $this->belongsTo(ServiciosModel::class, 'id_servicio', 'id_servicio');
    }

    public function actividadesEconomicas()
    {
        return $this->belongsTo(ActividadesEconomicasModel::class, 'cod_actividad', 'COD_ACTIVIDAD');
    }

    public function empresas()
    {
        return $this->belongsTo(EmpresasModel::class, 'cuit_municipio', 'cuit');
    }

    public function getLocalidadAttribute($value)
    {
        return $value ? $value : 'Sin datos';
    }

    public function getTieneVencimientoAttribute($value)
    {
        return $value ? $value : 'Sin datos';
    }

    public function getMensualOXDiasAttribute($value)
    {
        return $value ? $value : 'Sin datos';
    }

    public function getCantidadDiasOMesesAttribute($value)
    {
        return $value ? $value : 'Sin datos';
    }

    public function getEsReguladaAttribute($value)
    {
        return $value ? $value : 'Sin datos';
    }
}
