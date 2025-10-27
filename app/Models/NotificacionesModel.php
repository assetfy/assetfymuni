<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NotificacionesModel extends Model
{
    use HasFactory;
    public $timestamps = false;
    const TABLE = 'act.notificaciones';
    protected $table = self::TABLE;
    protected $fillable = ['emisora','cuit_empresa', 'id_usuario','cod_actividad','tipo_empresa','descripcion','es_representante_tecnico'];
    protected $primaryKey = "id_notificacion";
}