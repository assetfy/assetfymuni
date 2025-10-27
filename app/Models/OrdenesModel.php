<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\AuditableEmpresa;

class OrdenesModel extends Model
{
    use HasFactory, AuditableEmpresa;
    public $timestamps = false;
    const TABLE = 'act.ordenes';
    protected $table = self::TABLE;
    protected $fillable =
    [
        'proveedor',
        'estado_vigencia',
        'comentarios',
        'representante_tecnico',
        'id_relacion_usuario',
        'tipo_orden',
        'estado_orden',
        'fecha',
        'id_usuario',
        'cuit_Cliente',
        'fecha_carga',
    ];

    protected $primaryKey = 'id_orden';

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->claseAuditoria = 'ordenes';
        // $this->camposIncluirAuditoria = ['estado_orden','comentarios','proveedor','tipo_orden','representante_tecnico'];
        $this->camposExcluirAuditoria = [];  // por ahora audita todo;
        // Comportamiento:
        $this->creacionCompacta = true;
        $this->actualizacionCompacta = false;
    }

    public function Representante_tecnico()
    {
        return $this->belongsTo(UsuariosEmpresasModel::class, 'id_relacion_usuario', 'id_relacion');
    }

    public function tecnico()
    {
        return $this->belongsTo(UsuariosEmpresasModel::class, 'id_relacion_usuario', 'id_relacion');
    }

    public function Cliente()
    {
        return $this->belongsTo(EmpresasModel::class, 'cuit_Cliente', 'cuit');
    }


    public function activos()
    {
        return $this->belongsTo(ActivosModel::class, 'id_activo', 'id_activo');
    }

    public function proveedores()
    {
        return $this->belongsTo(EmpresasModel::class, 'proveedor', 'cuit');
    }

    public function getEstadoOrdenColoredAttribute()
    {
        $color = 'black';
        switch ($this->estado_orden) {
            case 'Rechazado':
                $color = 'red';
                break;
            case 'Realizado':
                $color = 'green';
                break;
            case 'Pendiente':
                $color = 'orange';
                break;
        }
        return '<span style="color:' . $color . ';">' . $this->estado_orden . '</span>';
    }

    // Accesor para formatear el campo tipo_orden con colores:
    public function getTipoOrdenColoredAttribute()
    {
        $color = 'black';
        if ($this->tipo_orden === 'Correctivo/Reparación') {
            $color = 'Red';
        } elseif ($this->tipo_orden === 'Preventivo') {
            $color = 'green';
        }
        return '<span style="color:' . $color . ';">' . $this->tipo_orden . '</span>';
    }
}
