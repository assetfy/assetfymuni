<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AtributosSubcategoriaModel extends Model
{
    use HasFactory;
    public $timestamps = false;
    const TABLE = 'act.atributos_subcategorias';
    protected $table = self::TABLE;
    protected $fillable = ['id_atributo', 'id_subcategoria', 'id_categoria', 'id_tipo', 'obligatorio_carga_ini', 'unico'];
    protected $primaryKey = "id_atributo";


    public function atributo()
    {
        return $this->belongsTo(AtributosModel::class, 'id_atributo', 'id_atributo');
    }

    public function subcategoria()
    {
        return $this->belongsTo(SubcategoriaModel::class, 'id_subcategoria', 'id_subcategoria');
    }

    public function valores()
    {
        return $this->hasMany(AtributosValoresModel::class, 'id_atributo', 'id_atributo');
    }
}
