<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QrBienModel extends Model
{
    use HasFactory;
    public $timestamps = false;
    const TABLE = 'act.qr_table';
    protected $table = self::TABLE;
    protected $fillable = ['cuit', 'foto', 'texto'];
    protected $primaryKey = "id_foto";
}