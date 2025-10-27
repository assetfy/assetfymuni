<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class contratosMaterialesModel extends Model
{
    use HasFactory;
    public $timestamps = false;
    const TABLE = 'contratos_materiales';
    protected $table = self::TABLE;
    protected $primaryKey = "id_contrato";

    protected $fillable = ['id_material'];
}
