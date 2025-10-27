<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AtributosValoresModel extends Model
{
    use HasFactory;
    public $timestamps = false;
    const TABLE = 'act.atributos_valores';
    protected $table = self::TABLE;
    protected $fillable = ['id_atributo', 'valor'];
    protected $primaryKey = "id_valor";
}