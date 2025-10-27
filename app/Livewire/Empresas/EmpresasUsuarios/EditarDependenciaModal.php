<?php

namespace App\Livewire\Empresas\EmpresasUsuarios;

use Livewire\Component;

class EditarDependenciaModal extends Component
{
    public $open = false;
    public $usuarioEmpresaId;

    public $editMode = true;
    public $padreId = null;
    public $padreNombre = null;
    public $selectedLevel = null;
    public $allNivelesPlano;
    public $nivelesPlano;

    protected $listeners = [
        'openModalEditarDependencia' => 'openModalEditarDependencia',
        'setPadre'                   => 'setPadre',
    ];
    public $origen = 'usuarios';
    public $esCargaMasiva = false;
    public $rowIndex = null;

    public function openModalEditarDependencia($payload = null)
    {
        if (!\App\Services\MiddlewareInvoker::checkPermisosRoles()) {
            $this->dispatch('no-permission', ['message' => 'No tiene permiso para realizar esta acción.']);
            return;
        }

        // 1) Tomo todo del payload
        $this->origen   = is_array($payload) ? ($payload['origen'] ?? 'usuarios') : 'usuarios';
        $this->rowIndex = is_array($payload) ? ($payload['rowIndex'] ?? null)      : null;

        $ueId           = is_array($payload) ? ($payload['id'] ?? null)            : $payload;
        $depIdExcel     = is_array($payload) ? ($payload['depIdExcel'] ?? null)    : null;
        $depNombreExcel = is_array($payload) ? ($payload['depNombreExcel'] ?? null) : null;

        // 2) AHORA calculo si es carga masiva
        $this->esCargaMasiva = ($this->origen === 'usuarios_masiva' || $this->origen === 'bienes_masiva');

        // 3) Si hay id, cargo de BD
        $ueId = is_numeric($ueId) ? (int)$ueId : null;
        $this->show($ueId);

        // 4) Si no hay dato en BD, intento precargar con Excel
        if (!$this->padreId && ($depIdExcel || $depNombreExcel)) {
            [$id, $nombre] = $this->resolverPreseleccionDesdeExcel($depIdExcel, $depNombreExcel);
            $this->padreId     = $id;
            $this->padreNombre = $nombre ?? 'Sin Dependencia';
        }

        // 5) Armo árbol y abro
        $this->cargarNivelesPlano();
        $this->open = true;
    }

    // nuevo: para devolver selección a la tabla en carga masiva
    public function seleccionarParaCargaMasiva()
    {
        $this->dispatch('dependenciaSeleccionada', [
            'origen'   => $this->origen,         // <— importantísimo
            'rowIndex' => $this->rowIndex,
            'id'       => $this->padreId,
            'nombre'   => $this->padreNombre ?? 'Sin Dependencia',
        ]);

        $this->close();
    }

    public function show(?int $id = null)
    {
        $this->resetValidation();
        $this->usuarioEmpresaId = $id;

        if ($id) {
            $ue = \App\Models\UsuariosEmpresasModel::with('nivelOrganizacion')->find($id);
            if ($ue) {
                $this->padreId     = $ue->id_Nivel_Organizacion;
                $this->padreNombre = $ue->nivelOrganizacion->Nombre ?? 'Sin Dependencia';
            } else {
                $this->padreId = null;
                $this->padreNombre = 'Sin Dependencia';
            }
        } else {
            $this->padreId = null;
            $this->padreNombre = 'Sin Dependencia';
        }
    }

    private function resolverPreseleccionDesdeExcel($depIdExcel, $depNombreExcel): array
    {
        $cuit = \App\Helpers\IdHelper::empresaActual()->cuit;

        // Preferir ID si corresponde a la empresa
        if ($depIdExcel) {
            $nombre = \App\Models\OrganizacionUnidadesModel::where('CuitEmpresa', $cuit)
                ->where('Id', (int)$depIdExcel)
                ->value('Nombre');
            if ($nombre) {
                return [(int)$depIdExcel, $nombre];
            }
        }

        // Si vino nombre en el Excel, intentar match por nombre (case-insensitive)
        if ($depNombreExcel && trim($depNombreExcel) !== '') {
            $match = \App\Models\OrganizacionUnidadesModel::where('CuitEmpresa', $cuit)
                ->whereRaw('LOWER(Nombre) = ?', [mb_strtolower(trim($depNombreExcel))])
                ->first(['Id', 'Nombre']);
            if ($match) {
                return [(int)$match->Id, $match->Nombre];
            }
        }

        // Sin coincidencia
        return [null, null];
    }

    public function setPadre($payload)
    {
        $id = is_array($payload) ? ($payload['id'] ?? null) : $payload;
        $id = $id ? (int) $id : null;

        $this->padreId = $id;
        $this->padreNombre = $id
            ? \App\Models\OrganizacionUnidadesModel::where('Id', $id)->value('Nombre')
            : 'Sin Dependencia';
    }

    public function cargarNivelesPlano()
    {
        $empresaActual  = \App\Helpers\IdHelper::empresaActual()->cuit;
        $todos = \App\Models\OrganizacionUnidadesModel::where('CuitEmpresa',  $empresaActual)
            ->orderBy('PadreId')
            ->get();

        $this->allNivelesPlano = $todos->map(fn($item) => (object)[
            'Id' => $item->Id,
            'Nombre' => $item->Nombre,
        ])->toArray();

        $this->nivelesPlano = $this->allNivelesPlano;

        $datosPrueba = collect($todos)->map(fn($item) => [
            'id'     => (string) $item->Id,
            'padre'  => $item->PadreId ? (string) $item->PadreId : null,
            'nombre' => $item->Nombre,
        ])->values()->toArray();

        // Mantengo tu payload original (solo data), no toco la implementación global del jsTree
        $this->dispatch('init-jstree-edit', ['data' => $datosPrueba]);
    }

    public function guardar()
    {
        // Si estamos editando un usuario existente, persistimos
        if ($this->usuarioEmpresaId) {
            $ue = \App\Models\UsuariosEmpresasModel::find($this->usuarioEmpresaId);
            if ($ue) {
                $ue->id_Nivel_Organizacion = $this->padreId ?? null;
                $ue->save();
            }
            $this->dispatch('refreshUsuarios');
        }
        $this->close();
    }

    public function close()
    {
        $this->open = false;
        $this->reset(['usuarioEmpresaId', 'padreId', 'padreNombre', 'selectedLevel', 'nivelesPlano']);
    }

    public function render()
    {
        return view('livewire.empresas.empresas-usuarios.editar-dependencia-modal');
    }
}
