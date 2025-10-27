<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ControlesSubcategoriaModel extends Model
{
    use HasFactory;
    public $timestamps = false;
    const TABLE = 'act.controles_subcategorias';
    protected $table = self::TABLE;
    protected $fillable = ['id_control', 
                            'id_subcategoria', 
                            'id_categoria', 
                            'id_tipo', 
                            'obligatorio_carga_ini',
                            'es_periodico',
                            'frecuencia_control', 
                            'cantidad_estandar', 
                            'unico', 
                            'req_foto'];
    protected $primaryKey = "id_control";

    public function controles()
    {
        return $this->belongsTo(ControlesModel::class, 'id_control', 'id_control');
    }

    public function subcategoria()
    {
        return $this->belongsTo(SubcategoriaModel::class, 'id_subcategoria', 'id_subcategoria');
    }
}