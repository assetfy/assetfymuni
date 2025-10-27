<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class TiposModel extends Model
{
    public $timestamps = false;
    use HasFactory;
    const TABLE = 'act.tipos';
    protected $table = self::TABLE;
    protected $primaryKey = "id_tipo";

    protected $fillable = [
        'sigla',
        'nombre',
        'descripcion',
        'imagen'
    ];

    public function getObfuscatedIdAttribute()
    {
        return encrypt($this->id_tipo);
    }

    // Obtener el ID original desofuscado
    public function getDecryptedIdAttribute()
    {
        return $this->id_tipo;
    }

    public function subcategorias()
    {
        return $this->hasMany(SubcategoriaModel::class, 'id_tipo', 'id_tipo');
    }
}
