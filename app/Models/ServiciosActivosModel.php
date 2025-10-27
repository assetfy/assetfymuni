<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiciosActivosModel extends Model
{
    public $timestamps = false;
    use HasFactory;
    const TABLE = 'act.servicios_activos';
    protected $table = self::TABLE;
    protected $primaryKey = "id_serviciosActivos";
    protected $fillable = [
        'id_servicio',
        'id_activo',
        'id_subcategoria_activo',
        'id_categoria_activo',
        'id_tipo_activo',
        'proveedor',
        'fecha',
        'estado_vigencia',
        'avalado',
        'solicitud',
        'comentarios',
        'foto',
        'representante_tecnico',
        'id_relacion_usuario',
        'id_usuario'
    ];

    public function servicios()
    {
        return $this->belongsTo(ServiciosModel::class, 'id_servicio', 'id_servicio');
    }

    public function tipos()
    {
        return $this->belongsTo(TiposModel::class, 'id_tipo_activo', 'id_tipo');
    }

    public function categorias()
    {
        return $this->belongsTo(CategoriaModel::class, 'id_categoria_activo', 'id_categoria');
    }

    public function subcategorias()
    {
        return $this->belongsTo(SubcategoriaModel::class, 'id_subcategoria_activo', 'id_subcategoria');
    }

    public function activos()
    {
        return $this->belongsTo(ActivosModel::class, 'id_activo', 'id_activo');
    }

    public function empresas()
    {
        return $this->belongsTo(EmpresasModel::class, 'proveedor', 'cuit');
    }

    public function usuarios()
    {
        return $this->belongsTo(User::class, 'id_usuario', 'id');
    }
}
