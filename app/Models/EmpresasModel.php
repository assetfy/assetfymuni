<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmpresasModel extends Model
{
    use HasFactory;
    public $timestamps = false;
    const TABLE = 'act.empresas_o_particulares';
    protected $table = self::TABLE;
    protected $fillable = [
        'cuit',
        'razon_social',
        'tipo',
        'estado',
        'constancia_afip',
        'provincia',
        'localidad',
        'domicilio',
        'piso',
        'codigo_postal',
        'COD_ACTIVIDAD',
        'ultima_habilitacion',
        'autorizacion_empresa_reg',
        'autoriza',
        'estado_autorizante',
        'autorizacion_estado ',
        'empresa_reguladora_autorizante',
        'logo',
        'descripcion_actividad',
        'lat',
        'long',
        'url',
        'places'
    ];

    protected $primaryKey = 'cuit';

    public function actividades()
    {
        return $this->belongsTo(ActividadesEconomicasModel::class, 'COD_ACTIVIDAD', 'COD_ACTIVIDAD');
    }

    public function usuariosEmpresas()
    {
        return $this->hasMany(UsuariosEmpresasModel::class, 'cuit', 'cuit');
    }

    public function usuariosApoderado()
    {
        return $this->hasMany(UsuariosEmpresasModel::class, 'cuit', 'cuit')->where('cargo', 'Apoderado');
    }

    // Relación con la tabla fotos_empresa
    public function fotos()
    {
        return $this->hasMany(FotosDeEmpresaModel::class, 'cuit', 'cuit');
    }

    public function empresatipo()
    {
        return $this->belongsTo(TiposEmpresaModel::class, 'tipo', 'id_tipo_empresa');
    }

    public function ubicaciones()
    {
        return $this->hasMany(UbicacionesModel::class, 'cuit', 'cuit');
    }
}
