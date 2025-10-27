<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CategoriaModel extends Model
{
    use HasFactory;
    public $timestamps = false;
    const TABLE = 'act.categorias';
    protected $table = self::TABLE;
    protected $fillable = [
        'sigla',
        'id_tipo',
        'nombre',
        'descripcion',
        'imagen'
    ];
    protected $primaryKey = "id_categoria";
    protected $keyType = 'string';

    public function data()
    {
        return $this->belongsTo(TiposModel::class, 'id_tipo', 'id_tipo');
    }

    public function subcategorias()
    {
        // está usando id_tipo en lugar de id_categoria
        return $this->hasMany(SubcategoriaModel::class, 'id_tipo', 'id_tipo');
    }
}
