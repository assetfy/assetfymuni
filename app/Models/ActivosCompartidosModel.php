<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActivosCompartidosModel extends Model
{
    use HasFactory;
    public $timestamps = false;
    const TABLE = 'act.activos_compartidos';
    protected $table = self::TABLE;
    protected $fillable = [
        'id_activo',
        'id_subcat',
        'id_cat',
        'id_tipo',
        'empresa_titular',
        'empresa_proveedora',
        'fecha_carga',
        'fecha_fin',
        'estado_asignacion'
    ];

    protected $primaryKey = 'id_activos_compartidos';

    // Empresa por empresa_proveedor
    public function empresaProveedor()
    {
        return $this->belongsTo(EmpresasModel::class, 'empresa_proveedora', 'cuit');
    }

    // Empresa por empresa_titular
    public function empresaTitular()
    {
        return $this->belongsTo(EmpresasModel::class, 'empresa_titular', 'cuit');
    }

    // Relación con SubcategoriaModel
    public function subcategoria()
    {
        return $this->belongsTo(SubcategoriaModel::class, 'id_subcat', 'id_subcategoria');
    }

    // Relación con TiposModel
    public function tipo()
    {
        return $this->belongsTo(TiposModel::class, 'id_tipo', 'id_tipo');
    }

    // Relación con CategoriaModel
    public function categoria()
    {
        return $this->belongsTo(CategoriaModel::class, 'id_cat', 'id_categoria');
    }

    public function activo()
    {
        return $this->belongsTo(\App\Models\ActivosModel::class, 'id_activo', 'id_activo');
    }

    // Relación con EstadoGeneralModel
    public function estadoGeneral()
    {
        return $this->belongsTo(EstadoGeneralModel::class, 'id_estado_sit_general', 'id_estado_sit_general');
    }

    // Relación con ActivosAsignacionModel
    public function asignaciones()
    {
        return $this->hasMany(ActivosAsignacionModel::class, 'id_activo', 'id_activo');
    }
}
