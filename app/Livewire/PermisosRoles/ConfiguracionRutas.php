<?php

namespace App\Livewire\Permisosroles;

use App\Helpers\IdHelper;
use App\Models\CategoriaModel;
use App\Models\ClientesEmpresaModel;
use App\Models\ConfiguracionRutasModel;
use App\Models\RutasModel;
use App\Models\SubcategoriaModel;
use App\Models\TiposModel;
use App\Models\TiposUbicacionesModel;
use App\Services\MiddlewareInvoker;
use Livewire\Component;

class ConfiguracionRutas extends Component
{
    protected $listeners = ['EditarConfiguracionRutas'];

    public $open;
    public $tipos, $categoria, $subcategoria, $cuit_empresa, $clientes, $empresasClientes, $opcion, $clientesEmpresa, $id_ruta;

    // Propiedades para cada select
    public $tipoSeleccionado, $categoriaSeleccionada, $subcategoriaSeleccionada, $clienteOempresa;
    public $tipoUbicacion; // Esto es la colección de tipos de ubicación.
    public $tipoUbicacionSeleccionado; // Esta propiedad almacenará el valor seleccionado.

    // Aquí se almacenarán todas las selecciones del usuarios
    public $atributosSeleccionados = [];
    public $categoriasFiltradas = [];
    public $subcategoriasFiltradas = [];



    public function EditarConfiguracionRutas($data)
    {
        if (!MiddlewareInvoker::checkPermisosRoles()) {
            $this->dispatch('no-permission', ['message' => 'No tiene permiso para realizar esta acción.']);
            return;
        }
        $this->datos($data);
        $this->open = true;
    }

    private function datos($data)
    {
        $this->id_ruta = $data;
        $this->cuit_empresa = IdHelper::idEmpresa();
        $this->tipos = TiposModel::all();
        $this->categoria = CategoriaModel::all();
        $this->subcategoria = SubcategoriaModel::all();
        $this->clientesEmpresa = ClientesEmpresaModel::where('empresa_cuit', $this->cuit_empresa)
            ->whereNotNull('cliente_cuit')
            ->get();
        $this->clientes = ClientesEmpresaModel::where('empresa_cuit', $this->cuit_empresa)
            ->whereNotNull('cuil')
            ->get();
        $this->tipoUbicacion = TiposUbicacionesModel::all();
    }

    // Actualiza el array y filtra las categorías según el tipo seleccionado
    public function updatedTipoSeleccionado($value)
    {
        $this->atributosSeleccionados['tipo'] = $value;
        $this->categoriasFiltradas = CategoriaModel::where('id_tipo', $value)->get();
        // Reiniciamos la categoría y subcategoría seleccionada
        $this->categoriaSeleccionada = null;
        $this->atributosSeleccionados['categoria'] = null;
        $this->subcategoriasFiltradas = [];
        $this->subcategoriaSeleccionada = null;
        $this->atributosSeleccionados['subcategoria'] = null;
    }

    // Actualiza el array y filtra las subcategorías según la categoría seleccionada
    public function updatedCategoriaSeleccionada($value)
    {
        $this->atributosSeleccionados['categoria'] = $value;
        $this->subcategoriasFiltradas = SubcategoriaModel::where('id_categoria', $value)->get();
        $this->subcategoriaSeleccionada = null;
        $this->atributosSeleccionados['subcategoria'] = null;
    }

    // Guarda la subcategoría seleccionada
    public function updatedSubcategoriaSeleccionada($value)
    {
        $this->atributosSeleccionados['subcategoria'] = $value;
    }

    // Si fuese necesario, para la configuración de clientes/empresa puedes almacenar su valor:
    public function updatedClienteOempresa($value)
    {
        $this->atributosSeleccionados['clienteOempresa'] = $value;
    }

    public function updatedTipoUbicacionSeleccionado($value)
    {
        // Guarda el valor seleccionado en el array de atributos.
        $this->atributosSeleccionados['tipo_ubicacion'] = $value;
    }

    public function actualizar()
    {
        // Iteramos sobre cada clave en el array y guardamos solo si el valor no está vacío
        foreach ($this->atributosSeleccionados as $key => $atributo) {
            if (!empty($atributo)) {
                ConfiguracionRutasModel::create([
                    'id_ruta'       => $this->id_ruta,
                    'cuit_empresa'  => $this->cuit_empresa,
                    'nombre_config' => $this->opcion, // Por ejemplo, 'bienes'
                    'atributos'     => $atributo,     // Guarda el valor seleccionado
                ]);
            }
        }

        $this->dispatch('lucky');
        $this->open = false;
    }


    public function render()
    {
        return view('livewire.permisosroles.configuracion-rutas');
    }
}
