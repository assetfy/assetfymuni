<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class provedoresContratosModel extends Model
{
    use HasFactory;
    public $timestamps = false;
    const TABLE = 'act.provedores_contratos';
    protected $table = self::TABLE;
    protected $fillable = ['id_mis_proveedor', 'numero', 'fecha'];
    protected $primaryKey = "id_contrato";

    public function contratoRelacion()
    {
        return $this->hasOne(MisProveedoresModel::class, 'id', 'id_mis_proveedor');
    }
}
