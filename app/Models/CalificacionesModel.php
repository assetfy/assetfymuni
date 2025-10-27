<?php

namespace App\Models;

use App\Livewire\Servicios\Activos\ServiciosActivos;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CalificacionesModel extends Model
{
    use HasFactory;
    public $timestamps = false;
    const TABLE = 'act.calificaciones';
    protected $table = self::TABLE;
    protected $fillable = [
        'cuit',
        'id_usuario',
        'calificacion',
        'general',
        'id_serviciosActivos',
        'diagnostico',
        'precio',
        'contratacion',
        'fecha_resenia'
    ];                          
    protected $primaryKey = 'id';

    // Relación con el modelo de ubicaciones para ubicación actual
    public function serviciosActivos()
    {
        return $this->belongsTo(ServiciosActivosModel::class, 'id_serviciosActivos', 'id_serviciosActivos');
    }
    // Relación con el modelo de ubicaciones para ubicación trasladada
    public function empresas()
    {
        return $this->belongsTo(EmpresasModel::class, 'cuit', 'cuit');
    }
    // Relación con el modelo de usuario
    public function usuario()
    {
        return $this->belongsTo(User::class, 'id_usuario', 'id');
    }
}
