<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrdenSlaModel extends Model
{
    use HasFactory;
    public $timestamps = false;
    const TABLE = 'act.ordenes_sla';
    protected $table = self::TABLE;
    protected $fillable = [
        'id_orden',
        'sla_horas',
    ];
    protected $primaryKey = 'id_orden';
}
