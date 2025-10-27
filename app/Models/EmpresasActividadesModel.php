<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmpresasActividadesModel extends Model
{   use HasFactory;
    public $timestamps = false;
    const TABLE = 'act.empresas_actividades';
    protected $table = self::TABLE;
    protected $primaryKey = "cod_actividad";
    protected $fillable = ['cuit', 
                            'cod_actividad',
                            'ultima_habilitacion',
                            'estado_autorizante',
                            'empresa_reguladora_autorizante',
                            'autorizacion_estado',
                            'autorizacion_empresa_reg',
                            'autoriza',
                            'estado',
                            'provincia',
                            'localidad'];

    // Relación con la tabla Empresas o particulares
    public function empresa()
    {
        return $this->belongsTo(EmpresasModel::class, 'cuit', 'cuit');
    }

    // Relación con la tabla ActividadesEconomicasModel
    public function actividadEconomica()
    {
        return $this->belongsTo(ActividadesEconomicasModel::class, 'cod_actividad', 'COD_ACTIVIDAD');
    }  

    //Maneja desde la vista la variante que al no tener datos cargados en ultima habilitacion, muestra un mensaje por defecto
    public function getUltimaHabilitacionAttribute($value)
    {
        return $value ? $value : 'Sin datos';
    }

    //Maneja desde la vista la variante que al no tener datos cargados en estado, muestra un mensaje por defecto
    public function getEstadoAttribute($value)
    {
        return $value ? $value : 'A confirmar';
    }
}