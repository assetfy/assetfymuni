<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FotosServicioModel extends Model
{
    use HasFactory;
    public $timestamps = false;
    const TABLE = 'act.fotos_servicios';
    protected $table = self::TABLE;
    protected $fillable = ['id_solicitud', 'fotos'];
    protected $primaryKey = "id_foto";
}