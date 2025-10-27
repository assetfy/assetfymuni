<?php

namespace App\Livewire\Configuraciones;

use Livewire\Component;
use Livewire\Attributes\On; // Livewire v3
use App\Helpers\IdHelper;
use App\Models\MisProveedoresModel;

class ProveedoresContratos extends Component
{
    public $open = false;

    public string $searchEmpresa = '';
    public array $empresas = [];
    public array $filteredEmpresas = [];
    public ?string $cuit = null;
    public $empresaBusqueda = null;

    public bool $ordersWithoutContract = false;

    protected $listeners = ['crearConfContratos'];

    public function crearConfContratos(): void
    {
        // $this->loadEmpresas();
        // $this->filteredEmpresas = $this->empresas;
        $this->resetFields();
        $this->open = true;
    }

    private function resetFields(): void
    {
        $db = MisProveedoresModel::query()
            ->where('empresa', IdHelper::idEmpresa())
            ->first(['ordenes_sin_contrato']);

        $this->ordersWithoutContract = (bool)($db->ordenes_sin_contrato ?? 0);
    }

    // private function loadEmpresas(): void
    // {
    //     $this->empresas = MisProveedoresModel::query()
    //         ->where('empresa', IdHelper::idEmpresa())
    //         ->orderBy('razon_social')
    //         ->get(['cuit', 'razon_social'])
    //         ->unique('cuit')
    //         ->map(fn($r) => ['cuit' => (string)$r->cuit, 'razon_social' => (string)$r->razon_social])
    //         ->all();
    // }

    // public function updatedSearchEmpresa(string $value): void
    // {
    //     $needle = mb_strtolower(trim($value), 'UTF-8');
    //     $this->filteredEmpresas = array_values(array_filter(
    //         $this->empresas,
    //         static fn(array $e) => $needle === '' ||
    //             mb_stripos(mb_strtolower($e['razon_social'] ?? '', 'UTF-8'), $needle, 0, 'UTF-8') !== false
    //     ));
    // }

    // public function setCuitEmpresa(string $cuit): void
    // {
    //     $this->cuit = $cuit;
    //     $row = collect($this->empresas)->firstWhere('cuit', $cuit);
    //     $this->empresaBusqueda = $row ? (object)$row : null;

    //     $db = MisProveedoresModel::query()
    //         ->where('empresa', IdHelper::idEmpresa())
    //         ->where('cuit', $cuit)
    //         ->first(['ordenes_sin_contrato']);

    //     $this->ordersWithoutContract = (bool)($db->ordenes_sin_contrato ?? 0);
    // }

    public function closeModal(): void
    {
        $this->open = false;
        $this->ordersWithoutContract = false; // en la tarjeta queda No
        // $this->searchEmpresa = '';
        // $this->filteredEmpresas = [];
        // $this->cuit = '';
    }

    public function save(): void
    {
        // if (!$this->cuit) {
        //     $this->addError('cuit', 'Seleccioná una empresa.');
        //     return;
        // }

        MisProveedoresModel::query()
            ->where('empresa', IdHelper::idEmpresa())
            // ->where('cuit', $this->cuit)
            ->update(['ordenes_sin_contrato' => $this->ordersWithoutContract ? 1 : 0]);
        // dd('aca');

        $this->dispatch('lucky');
        $this->closeModal();
    }

    public function render()
    {
        return view('livewire.configuraciones.proveedores-contratos');
    }
}
