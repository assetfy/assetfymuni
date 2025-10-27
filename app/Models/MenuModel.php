<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MenuModel extends Model
{
    use HasFactory;
    public $timestamps = false;
    const TABLE = 'menu';
    protected $table = self::TABLE;
    protected $fillable = ['id_tipo', 'imagen','descripcion'];
    protected $primaryKey = "id_tipo";
}