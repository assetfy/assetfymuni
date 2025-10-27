<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MarcasModel extends Model
{
    use HasFactory;
    public $timestamps = false;
    const TABLE = 'act.marcas';
    protected $table = self::TABLE;
    protected $fillable = ['nombre'];
    protected $primaryKey = "id_marca";

    public function modelos()
    {
        return $this->hasMany(ModelosModel::class, 'id_marca', 'id_marca');
    }
}
