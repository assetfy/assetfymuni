<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActividadesEconomicasModel extends Model
{
    public $timestamps = false;
    use HasFactory;
    const TABLE = 'act.Actividades_Economicas';
    protected $table = self::TABLE;
    protected $primaryKey = "COD_ACTIVIDAD";

    protected $fillable = ['nombre', 'descripcion', 'estado', 'logo'];

    // Definir la relación con servicios asociados a la actividad
    public function serviciosActividades()
    {
        return $this->hasMany(ServiciosActividadesEconomicasModel::class, 'cod_actividad', 'COD_ACTIVIDAD');
    }
}
