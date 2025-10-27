<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActivosControlesModel extends Model
{
    use HasFactory;
    public $timestamps = false;
    const TABLE = 'act.activos_control';
    protected $table = self::TABLE;
    protected $fillable = ['id_control',
                            'id_activo',
                            'id_subcategoria_activo',
                            'id_categoria_activo',
                            'id_tipo_activo',
                            'fecha_inicio',
                            'fecha_fin',
                            'foto1',
                            'foto2',
                            'foto3',
                            'foto4',
                            'foto5'];
                         
    protected $primaryKey = 'id_controlesactivos';

    public function activo()
    {
        return $this->belongsTo(ActivosModel::class, 'id_activo', 'id_activo');
    }

    public function control()
    {
        return $this->belongsTo(ControlesModel::class, 'id_control', 'id_control');
    }
}