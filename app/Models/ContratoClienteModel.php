<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContratoClienteModel extends Model
{
    use HasFactory;
    public $timestamps = false;
    const TABLE = 'act.contrato_cliente';
    protected $table = self::TABLE;
    protected $fillable = ['id_clientes_empresa', 'contrato'];
    protected $primaryKey = "id_contrato";
}
