<?php

namespace App\Livewire\Configuraciones;

use Livewire\Component;

class ConfiguracionGeneral extends Component
{
    protected bool $modalDispatched = false;

    public function hydrate(): void
    {
        if (!$this->modalDispatched) {
            $this->dispatch('openModal', ['configuraciones.proveedores-contratos']);
        }
        $this->modalDispatched = true;
    }

    public function render()
    {
        return view('livewire.configuraciones.configuracion-general');
    }

    public function openModal()
    {
        $this->dispatch('crearConfContratos')
            ->to('configuraciones.proveedores-contratos');
    }
}
