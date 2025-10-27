<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AuditoriasModel extends Model
{
    use HasFactory;
    public $timestamps = false;
    const TABLE = 'act.empresas_auditoria';
    protected $table = self::TABLE;
    protected $fillable = [
                            'cuit',
                            'razon_social',
                            'estado',
                            'fecha_creacion',
                            'id_usuario'];                         
    protected $primaryKey = 'ID';
}