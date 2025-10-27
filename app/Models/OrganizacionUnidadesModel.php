<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\AuditableEmpresa;

class OrganizacionUnidadesModel extends Model
{
    use HasFactory, AuditableEmpresa;
    public $timestamps = false;
    const TABLE = 'act.OrganizacionUnidades';
    protected $table = self::TABLE;
    protected $fillable = [
        'CuitEmpresa',
        'PadreId',
        'id_usuario',
        'Nombre'
    ];

    protected $primaryKey = 'Id';


    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        // === Configuracin de auditoria ===
        $this->claseAuditoria = 'Organigrama';
        // $this->camposIncluirAuditoria = ['id_estado_sit_general','usuario_titular','empresa_titular','id_ubicacion','prestado','garantia_vigente'];
        $this->camposExcluirAuditoria = ['PadreId', 'CreatedAt', 'UpdatedAt', 'id_usuario', 'Id', 'CuitEmpresa',];
        // Comportamiento:
        $this->creacionCompacta = true;        // 1 registro en alta (campo='CREACION', evento='Creacion')
        $this->actualizacionCompacta = false;  // si lo pones true => 1 registro por update con JSON
    }


    protected static function booted()
    {
        // Borrar hijos antes que el padre (y disparar auditoría por cada uno)
        static::deleting(function (OrganizacionUnidadesModel $nodo) {
            $nodo->hijos()->get()->each->delete();
        });
    }

    public function hijos()
    {
        return $this->hasMany(self::class, 'PadreId', 'Id');
    }

    public function padre()
    {
        return $this->belongsTo(self::class, 'PadreId');
    }

    public function Creador()
    {
        return $this->belongsTo(User::class, 'id_usuario', 'id');
    }
}
