<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AuditoriaUbicacionActivoModel extends Model
{
    use HasFactory;
    public $timestamps = false;
    const TABLE = 'act.auditoria_ubicacion_activo';
    protected $table = self::TABLE;
    protected $fillable = [
        'ubicacion_actual',
        'id_activo',
        'trasladado',
        'fecha',
        'id_usuario',
    ];                          
    protected $primaryKey = 'id';

    // Relación con el modelo de ubicaciones para ubicación actual
    public function ubicacionActual()
    {
        return $this->belongsTo(UbicacionesModel::class, 'ubicacion_actual', 'id_ubicacion');
    }

    // Relación con el modelo de ubicaciones para ubicación trasladada
    public function ubicacionTrasladada()
    {
        return $this->belongsTo(UbicacionesModel::class, 'trasladado', 'id_ubicacion');
    }
    // Relación con el modelo de usuario
    public function usuario()
    {
        return $this->belongsTo(User::class, 'id_usuario', 'cuil');
    }
}
