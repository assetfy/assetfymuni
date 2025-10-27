<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ModelosModel extends Model
{
    use HasFactory;
    public $timestamps = false;
    const TABLE = 'act.modelos';
    protected $table = self::TABLE;
    protected $fillable = ['id_marca', 'id_subcategoria', 'id_categoria', 'id_tipo', 'nombre'];
    protected $primaryKey = "id_modelo";

    public function marca()
    {
        return $this->belongsTo(MarcasModel::class, 'id_marca', 'id_marca');
    }
}
