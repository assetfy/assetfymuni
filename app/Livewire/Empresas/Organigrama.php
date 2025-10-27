<?php

namespace App\Livewire\Empresas;

use App\Helpers\IdHelper;
use App\Models\OrganizacionUnidadesModel;
use Livewire\Component;

class Organigrama extends Component
{
    public $allNivelesPlano;
    public $nivelesPlano;

    // Datos para el primer render (usados por el JS vía data-org)
    public $datosPrueba = [];

    public function mount()
    {
        $this->cargarNivelesPlano();
    }

    public function render()
    {
        // Re-despacha para que el gráfico se actualice tras cada render si corresponde
        if (!empty($this->datosPrueba)) {
            $this->dispatch('mostrarOrganigrama1', $this->datosPrueba);
        }

        return view('livewire.empresas.organigrama');
    }

    public function cargarNivelesPlano()
    {
        $todos = OrganizacionUnidadesModel::where('CuitEmpresa', IdHelper::idEmpresa())
            ->orderBy('PadreId')
            ->get();

        // Listas auxiliares (como ya usabas)
        $this->allNivelesPlano = $todos->map(fn($item) => (object)[
            'Id'     => $item->Id,
            'Nombre' => $item->Nombre,
        ])->toArray();

        $this->nivelesPlano = $this->allNivelesPlano;

        // Normalización para Mermaid (id/padre como string)
        $datosPrueba = $todos->map(fn($item) => [
            'id'     => (string) $item->Id,
            'padre'  => $item->PadreId ? (string) $item->PadreId : null,
            'nombre' => $item->Nombre,
        ])->values()->toArray();

        // Guardar para primer render
        $this->datosPrueba = $datosPrueba;

        // Mantener compatibilidad con el listener del front
        $this->dispatch('mostrarRender1', $this->datosPrueba);
    }
}
