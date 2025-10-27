<?php

namespace App\Livewire\Empresas;

use App\Services\AccesosUserService;
use App\Helpers\IdHelper;
use App\Models\UbicacionesModel;
use Livewire\Component;

class SidebarEmpresa extends Component
{
    public $empresa, $usuario, $tipoEmpresa;
    public $colorSidebar;
    public $rutasPermitidas = [];
    public $menuConfig, $menuConfigPermisos, $menuUnificado, $ubicaciones;

    // ¡Mantenelo protegido para que Livewire no intente serializarlo!
    protected AccesosUserService $accesos;

    public function mount()
    {
        $this->empresa     = IdHelper::empresaActual();
        $this->usuario     = IdHelper::usuarioEmpresaActual();
        $this->tipoEmpresa = $this->empresa?->tipo;

        // Servicio de accesos
        $this->accesos = app(AccesosUserService::class);

        // Delegar al servicio
        $this->rutasPermitidas = $this->accesos->getRutasPermitidas();
        $this->colorSidebar    = $this->accesos->getSidebarColor();

        // Config de menú
        $directos = config('menu.directos', []);
        $submenus = config('menu.submenus', []);

        $this->menuUnificado = collect($directos)
            ->map(fn($item) => $item + ['type' => 'directo'])
            ->merge(collect($submenus)->map(fn($item) => $item + ['type' => 'submenu']))
            ->sortBy('order')
            ->values()
            ->toArray();

        $this->menuConfig         = config('menu');
        $this->menuConfigPermisos = config('menu_labels');
    }

    public function render()
    {
        return view('livewire.empresas.sidebar-empresa', [
            // Usá el servicio: incluye Apoderado o tipo_user===3 si así lo definiste
            'apoderado'       => $this->accesos->isPrivileged(), // o isApoderado() si querés solo Apoderado
            'menuConfig'      => $this->menuConfig,
            'rutasPermitidas' => $this->rutasPermitidas,
            'colorSidebar'    => $this->colorSidebar,
            'tipoEmpresa'     => $this->tipoEmpresa, // <-- fix: antes usabas $this->tipo
        ]);
    }

    /** Cargar ubicaciones por CUIT seleccionado o el de la empresa actual */
    private function cargarUbicaciones()
    {
        $cuit = session('cuitEmpresaSeleccionado') ?? $this->empresa?->cuit;
        $this->ubicaciones = $cuit
            ? UbicacionesModel::where('cuit', $cuit)->get()
            : collect();
    }

    public function refreshUbicaciones()
    {
        $this->cargarUbicaciones();
    }

    public function crearActivos()
    {
        $this->dispatch('createActivos')->to('activos.create-activos');
    }

    public function crearubicacionesDelegadas()
    {
        $this->dispatch('crearUbicacion')->to('ubicaciones.crear-ubicaciones');
    }

    /**
     * Wrapper por compatibilidad si la vista llama a $apoderado:
     * (podés eliminarlo y usar directamente $this->accesos->isPrivileged() en el render)
     */
    public function apoderado(): bool
    {
        return $this->accesos->isPrivileged(); // o ->isApoderado()
    }
}
