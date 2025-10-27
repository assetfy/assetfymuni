<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActivosFotosModel extends Model
{
    use HasFactory;
    public $timestamps = false;
    const TABLE = 'act.activo_fotos';
    protected $table = self::TABLE;
    protected $fillable = [
                            'id_activo',
                            'id_tipo',
                            'id_categoria',
                            'id_subcategoria',
                            'ruta_imagen'];                         
    protected $primaryKey = 'id_foto';
}