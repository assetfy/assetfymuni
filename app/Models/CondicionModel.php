<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CondicionModel extends Model
{
    use HasFactory;

    public $timestamps = false;

    const TABLE = 'act.condicion';
    protected $table = self::TABLE;
    protected $primaryKey = 'id_condicion';

    protected $fillable = ['nombre', 'descripcion'];
}