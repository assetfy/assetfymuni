<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\HtmlString;

class ClientesEmpresaModel extends Model
{
    use HasFactory;
    public $timestamps = false;
    const TABLE = 'act.clientes_empresa';
    protected $table = self::TABLE;
    protected $fillable =
    [
        'user_id',
        'empresa_cuit',
        'verificado',
        'cliente_cuit',
        'cuil',
        'numero_cliente',
        'estado',
    ];
    protected $primaryKey = 'id_clientes_empresa';

    public function usuarios()
    {
        return $this->belongsTo(User::class, 'cuil', 'cuil');
    }

    public function empresa()
    {
        return $this->belongsTo(EmpresasModel::class, 'cliente_cuit', 'cuit');
    }

    // Método que retorna el nombre a mostrar:
    public function getDisplayUsuarioAttribute()
    {
        // Si 'cuil' tiene valor y se encuentra el usuario, se muestra el nombre del usuario.
        if (!empty($this->cuil) && $this->usuarios) {
            return $this->usuarios->name;
        }
        // De lo contrario, si existe la empresa, se muestra la razón social.
        if ($this->empresa) {
            return $this->empresa->razon_social;
        }
        return '';
    }

    // Método que retorna el valor para la columna Cuil:
    public function getDisplayCuilAttribute()
    {
        // Si 'cuil' no está vacío, se muestra; de lo contrario, se muestra 'cliente_cuit'.
        return !empty($this->cuil) ? $this->cuil : $this->cliente_cuit;
    }

    public function contratos()
    {
        return $this->hasMany(ContratoClienteModel::class, 'id_clientes_empresa', 'id_clientes_empresa');
    }

    public function getContratosHtmlAttribute(): HtmlString
    {
        if ($this->contratos->isNotEmpty()) {
            $text = $this->contratos
                ->pluck('contrato')
                ->implode(', ');
        } else {
            $text = '<span class="text-red-600 font-semibold">Sin contrato</span>';
        }

        return new HtmlString($text);
    }
}
