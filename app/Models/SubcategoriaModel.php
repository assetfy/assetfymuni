<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubcategoriaModel extends Model
{
    use HasFactory;
    public $timestamps = false;
    const TABLE = 'act.subcategorias';
    protected $table = self::TABLE;
    protected $fillable = ['id_categoria',
                           'id_tipo',
                           'sigla',
                           'nombre',
                           'movil_o_fijo',
                           'se_relaciona',
                           'descripcion',
                           'imagen'];
    protected $primaryKey = "id_subcategoria";

    public function tipos()
    {
        return $this->belongsTo(TiposModel::class, 'id_tipo', 'id_tipo');
    }

    public function categoria()
    {
        return $this->belongsTo(CategoriaModel::class, 'id_categoria', 'id_categoria');
    }
}