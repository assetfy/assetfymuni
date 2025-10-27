<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\AuditableEmpresa;

class UbicacionesModel extends Model
{
    use HasFactory, AuditableEmpresa;
    public $timestamps = false;
    const TABLE = 'act.ubicaciones';
    protected $table = self::TABLE;
    protected $fillable = [
        'nombre',
        'pais',
        'provincia',
        'ciudad',
        'codigo_postal',
        'calle',
        'altura',
        'piso',
        'depto',
        'lat',
        'long',
        'cuil',
        'cuit',
        'nombre_apertura_1',
        'propiedad',
        'tipo',
        'id_externo',
        'cuit_empresa',
        'cuil_gestor',
        'fecha_carga',
        'cuil_asignado',
        'multipisos',
        'subsuelo',
    ];
    protected $primaryKey = "id_ubicacion";

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        // === Configuracin de auditoria ===
        $this->claseAuditoria = 'Ubicaciones';
        // $this->camposIncluirAuditoria = ['id_estado_sit_general','usuario_titular','empresa_titular','id_ubicacion','prestado','garantia_vigente'];
        $this->camposExcluirAuditoria = ['pais', 'provincia', 'codigo_postal', 'lat', 'long', 'fecha_carga',];
        // Comportamiento:
        $this->creacionCompacta = true;        // 1 registro en alta (campo='CREACION', evento='Creacion')
        $this->actualizacionCompacta = false;  // si lo pones true => 1 registro por update con JSON
    }

    public function tiposUbicaciones()
    {
        return $this->belongsTo(TiposUbicacionesModel::class, 'tipo', 'id_tipo');
    }

    public function apertura1()
    {
        return $this->hasMany(AperturaModel1::class, 'id_ubicacion', 'id_ubicacion');
    }

    public function apertura2()
    {
        return $this->hasMany(AperturaModel2::class, 'id_ubicacion', 'id_ubicacion');
    }

    public function apertura3()
    {
        return $this->hasMany(AperturaModel3::class, 'id_ubicacion', 'id_ubicacion');
    }

    public function apertura4()
    {
        return $this->hasMany(AperturaModel::class, 'id_ubicacion', 'id_ubicacion');
    }

    public function activos()
    {
        return $this->hasMany(ActivosModel::class, 'id_ubicacion', 'id_ubicacion');
    }

    public function empresas()
    {
        return $this->hasMany(EmpresasModel::class, 'cuit', 'cuit');
    }
}
