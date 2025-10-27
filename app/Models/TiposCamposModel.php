<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TiposCamposModel extends Model
{
    use HasFactory;
    public $timestamps = false;
    const TABLE = 'act.tipos_campo';
    protected $table = self::TABLE;
    protected $fillable = ['nombre'];
    protected $primaryKey = "id_tipo_campo";
}