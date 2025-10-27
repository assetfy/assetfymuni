<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActivosAtributosModel extends Model
{
    use HasFactory;
    public $timestamps = false;
    const TABLE = 'act.activos_atributos';
    protected $table = self::TABLE;
    protected $fillable = [
        'id_atributo',
        'id_activo',
        'id_subcategoria_activo',
        'id_categoria_activo',
        'id_tipo_activo',
        'campo',
        'campo_numerico',
        'fecha',
        'campo_enum',
        'campo_enum_list',
        'campo_enum_id',
    ];

    protected $primaryKey = 'id_activo_atributo';

    public function activo()
    {
        return $this->belongsTo(ActivosModel::class, 'id_activo', 'id_activo');
    }

    public function atributo()
    {
        return $this->belongsTo(AtributosModel::class, 'id_atributo', 'id_atributo');
    }

    public function subcategoria()
    {
        return $this->belongsTo(SubcategoriaModel::class, 'id_subcategoria_activo', 'id_subcategoria');
    }

    public function categoria()
    {
        return $this->belongsTo(CategoriaModel::class, 'id_categoria_activo', 'id_categoria');
    }

    public function tipo()
    {
        return $this->belongsTo(TiposModel::class, 'id_tipo_activo', 'id_tipo');
    }
}
