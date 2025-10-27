<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrdenesItemsModel extends Model
{
    use HasFactory;
    public $timestamps = false;
    const TABLE = 'act.ordenes_items';
    protected $table = self::TABLE;
    protected $fillable =
    [
        'id_orden',
        'item_tipo',
        'item_precio',
        'item_cantidad',
        'id_ot',
        'item_nombre',
        'id_material',
        'id_servicio'
    ];
    protected $primaryKey = 'id_item';

    public function material()
    {
        return $this->belongsTo(MaterialesModel::class, 'id_material', 'id_material');
    }

    public function servicio()
    {
        return $this->belongsTo(ServiciosModel::class, 'id_servicio', 'id_servicio');
    }
}
