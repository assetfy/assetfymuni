<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\provedoresContratosModel; // asegúrate de importar el modelo

class MisProveedoresModel extends Model
{
    use HasFactory;
    public $timestamps = false;
    const TABLE = 'act.mis_proveedores_favoritos';
    protected $table = self::TABLE;
    protected $fillable = [
        'existe_en_la_plataforma',
        'cuit',
        'razon_social',
        'localidad',
        'provincia',
        'id_usuario',
        'email',
        'contrato',
        'empresa',
        'ordenes_sin_contrato'
    ];
    protected $primaryKey = "id";

    public function empresa()
    {
        return $this->belongsTo(EmpresasModel::class, 'id_empresa', 'id_empresa');
    }

    // Relación hacia el contrato (se asume que la FK en la tabla contratos es id_mis_proveedor y relaciona con id)
    public function contratoRelacion()
    {
        return $this->hasOne(provedoresContratosModel::class, 'id_mis_proveedor', 'id');
    }

    // Accesor para formatear el contrato
    public function getContratoFormattedAttribute()
    {
        if ($this->contratoRelacion) {
            // Opcional: puedes formatear el número y la fecha, o solamente mostrar el número.
            // Por ejemplo, mostramos el número del contrato y la fecha:
            $numero = e($this->contratoRelacion->numero);
            $fecha = date('Y-m-d', strtotime($this->contratoRelacion->fecha));
            return "{$numero} <small>({$fecha})</small>";
        } else {
            return '<span class="text-red-500 font-bold">Sin Contrato</span>';
        }
    }
}
