<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TiposSolicitudModel extends Model
{
    public $timestamps = false;
    use HasFactory;
    const TABLE = 'act.tipo_solicitud';
    protected $table = self::TABLE;
    protected $primaryKey = "id_tipo_solicitud";

    protected $fillable = ['nombre'];
}
