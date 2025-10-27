<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AtributosModel extends Model
{
    use HasFactory;
    public $timestamps = false;
    const TABLE = 'act.atributos';
    protected $table = self::TABLE;
    protected $fillable = ['tipo_campo', 'unidad_medida', 'nombre', 'descripcion', 'SelectM', 'predefinido'];
    protected $primaryKey = "id_atributo";

    public function tiposCampos()
    {
        return $this->belongsTo(TiposCamposModel::class, 'tipo_campo', 'id_tipo_campo');
    }

    public function unidadMedida()
    {
        return $this->belongsTo(UnidadModel::class, 'unidad_medida', 'id_unidad_medida');
    }

    // Relación con los valores predefinidos
    public function valoresPredefinidos()
    {
        return $this->hasMany(AtributosValoresModel::class, 'id_atributo', 'id_atributo');
    }

    public function valores()
    {
        return $this->hasMany(AtributosValoresModel::class, 'id_atributo', 'id_atributo');
    }
}
