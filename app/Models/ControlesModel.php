<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ControlesModel extends Model
{
    use HasFactory;
    public $timestamps = false;
    const TABLE = 'act.controles';
    protected $table = self::TABLE;
    protected $fillable = ['nombre','descripcion'];
    protected $primaryKey = "id_control";
}