<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ordenesBienesModel extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $table = 'act.ordenes_Bienes';
    protected $primaryKey = 'id_orden_bien';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'id_orden',
        'id_activo',
        'id_subcategoria',
        'id_categoria',
        'id_tipo',
        'estado',
        'resolucion'
    ];

    public function activos()
    {
        return $this->belongsTo(ActivosModel::class, 'id_activo', 'id_activo');
    }

    // Relación con SubcategoriaModel
    public function subcategoria()
    {
        return $this->belongsTo(SubcategoriaModel::class, 'id_subcategoria', 'id_subcategoria');
    }

    // Relación con TiposModel
    public function tipo()
    {
        return $this->belongsTo(TiposModel::class, 'id_tipo', 'id_tipo');
    }

    // Relación con CategoriaModel
    public function categoria()
    {
        return $this->belongsTo(CategoriaModel::class, 'id_categoria', 'id_categoria');
    }
}
