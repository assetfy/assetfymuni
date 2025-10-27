<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TiposEmpresaModel extends Model
{
    use HasFactory;
    public $timestamps = false;
    const TABLE = 'act.tipos_empresas';
    protected $table = self::TABLE;
    protected $fillable = ['tipo_empresa'];
    protected $primaryKey = "id_tipo_empresa";
}