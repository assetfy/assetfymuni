<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EstadoActividadesEconomicasModel extends Model
{   use HasFactory;
    public $timestamps = false;
    const TABLE = 'act.estado_actividades_economicas';
    protected $table = self::TABLE;
    protected $primaryKey = "codigo_unico";
    protected $fillable = ['cuit', 
                            'cod_actividad',
                            'entidad_reguladora',
                            'renovacion_cada_x_dias'];

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
}
