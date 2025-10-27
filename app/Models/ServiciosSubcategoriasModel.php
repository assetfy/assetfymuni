<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiciosSubcategoriasModel extends Model
{
    use HasFactory;
    public $timestamps = false;
    const TABLE = 'act.servicios_subcategorias';
    protected $table = self::TABLE;
    protected $fillable = ['id_servicio', 
                            'id_subcategoria', 
                            'id_categoria', 
                            'id_tipo', 
                            'req_fotos_carga_inicial'];
                            
    protected $primaryKey = "id_servicio";

    public function servicios()
    {
        return $this->belongsTo(ServiciosModel::class, 'id_servicio', 'id_servicio');
    }

    public function subcategorias()
    {
        return $this->belongsTo(SubcategoriaModel::class, 'id_subcategoria', 'id_subcategoria');
    }

    public function categorias()
    {
        return $this->belongsTo(CategoriaModel::class, 'id_categoria', 'id_categoria');
    }

    public function tipos()
    {
        return $this->belongsTo(TiposModel::class, 'id_tipo', 'id_tipo');
    }
}