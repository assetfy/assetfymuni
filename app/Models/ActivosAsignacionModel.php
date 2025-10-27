<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActivosAsignacionModel extends Model
{
    public $timestamps = false;
    use HasFactory;
    const TABLE = 'act.activos_asignaciones';
    protected $table = self::TABLE;
    protected $primaryKey = "id_activos_asignaciones";
    public $incrementing = false;

    protected $fillable = [
        'id_activo',
        'id_tipo',
        'id_categoria',
        'asignado_a',
        'id_subcategoria',
        'gestionado_por',
        'id_subcategoria',
        'fecha_asignacion',
        'fecha_fin_asignacion',
        'responsable',
        'empresa_empleados',
        'estado_asignacion'
    ];

    public function activoAsignado()
    {
        return $this->hasMany(User::class, 'asignado_a', 'id');
    }

    public function asignado()
    {
        return $this->belongsTo(User::class, 'asignado_a', 'id');
    }

    /** El usuario que gestiona */
    public function gestor()
    {
        return $this->belongsTo(User::class, 'gestionado_por', 'id');
    }

    public function responsable()
    {
        return $this->belongsTo(User::class, 'responsable', 'id');
    }

    public function activos()
    {
        return $this->hasMany(ActivosModel::class, 'id_activo', 'id_activo');
    }
}
