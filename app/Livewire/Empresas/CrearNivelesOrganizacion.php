<?php

namespace App\Livewire\Empresas;

use App\Models\OrganizacionUnidadesModel;
use App\Services\MiddlewareInvoker;
use Illuminate\Support\Facades\DB;
use App\Helpers\IdHelper;
use Livewire\Component;

class CrearNivelesOrganizacion extends Component
{
    public $open = false;
    public $nivelesPlano = [];
    private $allNivelesPlano = [];
    public $nombre = '';
    public $padreId = null;
    public $searchPadre = '';
    public $padreNombre = null;

    protected $listeners = ['crearNivelOrganizacion', 'setPadre'];

    public function crearNivelOrganizacion()
    {
        if (!MiddlewareInvoker::checkPermisosRoles()) {
            $this->dispatch('no-permission', ['message' => 'No tiene permiso.']);
            return;
        }

        $this->cargarNivelesPlano();
        $this->open = true;
    }

    private function resetForm()
    {
        $this->nombre       = '';
        $this->padreId      = null;
        $this->padreNombre  = null;
        $this->searchPadre  = '';
        $this->nivelesPlano = $this->allNivelesPlano;
    }

    public function cargarNivelesPlano()
    {
        $todos = OrganizacionUnidadesModel::where('CuitEmpresa', IdHelper::idEmpresa())
            ->orderBy('PadreId')
            ->get();

        $this->allNivelesPlano = $todos->map(fn($item) => (object)[
            'Id' => $item->Id,
            'Nombre' => $item->Nombre,
        ])->toArray();

        $this->nivelesPlano = $this->allNivelesPlano;

        $datosPrueba = collect($todos) // $todos ya es una Collection de Eloquent
            ->map(fn($item) => [
                // el ID como string
                'id'     => (string) $item->Id,
                // si tiene PadreId lo casteamos a string, si no dejamos null
                'padre'  => $item->PadreId ? (string) $item->PadreId : null,
                // el nombre tal cual
                'nombre' => $item->Nombre,
            ])
            ->values()   // reindexa numéricamente
            ->toArray(); // convierte a array puro
        //este evento dispara un js que genera el organigrama 
        $this->dispatch('mostrarOrganigrama1', $datosPrueba);
    }

    public function setPadre(int $id)
    {
        $this->padreId = $id;
        $u = OrganizacionUnidadesModel::find($id);
        $this->padreNombre = $u?->Nombre;
        // opcional: sincronizar también el texto de búsqueda
        $this->searchPadre = $this->padreNombre;
    }

    public function save()
    {
        $this->validate([
            'nombre'  => 'required|string|max:100',
            'padreId' => 'nullable',
        ]);
        DB::beginTransaction();
        try {
            OrganizacionUnidadesModel::create([
                'CuitEmpresa'     => IdHelper::idEmpresa(),
                'id_usuario' => auth()->id(),
                'PadreId'         => $this->padreId,
                'Nombre'          => $this->nombre,
            ]);
            DB::commit();
            // Emitir eventos necesarios
            $this->dispatch('lucky');
            $this->close();
        } catch (\Exception $e) {
            DB::rollBack();
            // Manejar el error y mostrar un mensaje al usuario
            $this->dispatch('errorCreacion');
        }
        $this->dispatch('tablarefrescar');
    }

    public function updatedSearchPadre($value)
    {
        if (trim($value) === '') {
            // Si el input queda vacío, recargamos la lista completa
            $this->cargarNivelesPlano();
            return;
        }
        $busqueda = OrganizacionUnidadesModel::query()
            ->where('CuitEmpresa', IdHelper::idEmpresa())
            ->where('Nombre', 'like', "%{$value}%")
            ->orderBy('PadreId')
            ->get();
        // Mapeamos a los objetos que usa el dropdown
        $this->nivelesPlano = $busqueda->map(fn($n) => (object)[
            'Id'     => $n->Id,
            'Nombre' => $n->Nombre,
        ])->toArray();
        // Si el padre seleccionado ya no está entre los resultados, lo reseteamos
        if ($this->padreId && ! $busqueda->pluck('Id')->contains($this->padreId)) {
            $this->padreId     = null;
            $this->padreNombre = null;
        }
    }

    public function close()
    {
        $this->open = false;
        $this->resetForm();
    }

    public function render()
    {
        return view('livewire.empresas.crear-niveles-organizacion');
    }
}
