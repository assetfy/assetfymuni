<?php

namespace App\Livewire;

use Livewire\Component;

class ModalContainer extends Component
{
    /** @var string[] */
    public array $components = [];

    protected $listeners = [
        'openModal',   // monta el/los modal(es)
        'closeModal',  // desmonta todos
    ];

    /**
     * Abre exactamente estos modales (reemplaza los anteriores).
     *
     * @param  string|string[]  $components  Un único nombre o un array de kebab-names o FQCNs
     */
    // en ModalContainer.php
    public function openModal($components = null)
    {
        if (empty($components) || (is_array($components) && count($components) === 0)) {
            $this->components = [];
            return;
        }

        $this->components = is_array($components)
            ? $components
            : [$components];
    }


    /** Cierra todos los montados */
    public function closeModal()
    {
        $this->components = [];
    }

    public function render()
    {
        return view('livewire.modal-container');
    }
}
