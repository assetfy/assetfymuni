<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UsuarioActividadesRepresentadasModel extends Model
{
    use HasFactory;
    public $timestamps = false;
    const TABLE = 'act.usuarios_actividadesRepresentadas';
    protected $table = self::TABLE;
    protected $fillable = 
                        ['id_usuario',
                         'id_relacion_usuario',
                         'cuit_usuario',
                         'cod_actividad',
                         'habilitado',
                         'entidad_habilitadora'];
    protected $primaryKey = 'id_relacion_usuario';
}