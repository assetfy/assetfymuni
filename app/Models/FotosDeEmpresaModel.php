<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FotosDeEmpresaModel extends Model
{
    use HasFactory;
    public $timestamps = false;
    const TABLE = 'act.fotos_empresa';
    protected $table = self::TABLE;
    protected $primaryKey = "id_foto";
    
    protected $fillable = ['cuit', 'foto'];
}
