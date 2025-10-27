<?php

namespace App\Livewire\Empresas;

use Livewire\Component;
use App\Services\CalificacionesService;
use App\Helpers\IdHelper;
use App\Models\EmpresasModel;
use App\Models\SolicitudesServiciosModel;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Livewire\WithPagination;

class ReportesPrestadora extends Component
{
    public $totalGlobal;
    public $porcentajeContratacion1;
    public $porcentajeContratacion0;
    public $idPrestadora, $serviciosFinalizados;

    protected $calificacionesService, $clientesRecurrentes, $empresasSolicitantes, $idSolicitantes, $empresas, $usuarios, $numero, $serviciosSolicitados, $rankingMotivos, $estrellasPrestadora, $puntajeGoogle = 0, $puntajeAssetFly = 0;
    use WithPagination;

    public function mount(CalificacionesService $calificacionesService)
    {
        $this->calificacionesService = $calificacionesService;
        // Obtener el ID de la empresa prestadora
        $this->idPrestadora = IdHelper::idEmpresa();
        // Obtener los valores usando el servicio
        $this->totalGlobal = $this->calificacionesService->obtenerValorTotal($this->idPrestadora);
        $this->porcentajeContratacion1 = $this->calificacionesService->obtenerPorcentajeContratacion1($this->idPrestadora);
        $this->porcentajeContratacion0 = $this->calificacionesService->obtenerPorcentajeContratacion0($this->idPrestadora);
        $this->serviciosFinalizados = $this->numeroServiciosSolicitado();
        $this->clientesRecurrentes = $this->ClientesRecurrentes();
        $this->serviciosSolicitados = $this->clientesRecurrentes->pluck('solicitud_count')->sum();

        $this->DatosClientes();
        $this->ReorderClientesRecurrentes();
        $this->EstrellasPrestadora($this->idPrestadora);
        // Obtener el ranking de motivos (tanto para recomendación como para no recomendación)
        $this->rankingMotivos = $this->calificacionesService->obtenerRankingMotivos($this->idPrestadora);
    }

    private function EstrellasPrestadora($idPrestadora)
    {
        $prestadora = EmpresasModel::find($this->idPrestadora);
        $this->puntajeGoogle = 0; // Valor por defecto en caso de no obtener datos
        if ($prestadora && $prestadora->places) {
            $googleData = $this->calificacionesService->getReviewsGoogle($prestadora->places);
            $this->puntajeGoogle = $googleData['rating'] ?? 0;
        }
        // 2. Puntaje en Asset Fly (local):
        // Se calcula a partir de la diferencia neta entre % recomendados y no recomendados.
        // Fórmula: ((%Recomendado - %NoRecomendado) + 100) / 200 * 5
        $this->puntajeAssetFly = (($this->porcentajeContratacion1 - $this->porcentajeContratacion0) + 100) / 200 * 5;
        $this->puntajeAssetFly = round($this->puntajeAssetFly, 2);
    }

    private function DatosClientes()
    {
        // Extraemos los valores únicos de cada campo
        $empresaSolicitanteIDs = $this->clientesRecurrentes->pluck('empresa_solicitante')->unique();
        $idSolicitanteIDs = $this->clientesRecurrentes->pluck('id_solicitante')->unique();

        // Consultamos las empresas si existen IDs
        if ($empresaSolicitanteIDs->isNotEmpty()) {
            $this->empresas = EmpresasModel::whereIn('cuit', $empresaSolicitanteIDs)->get();
        } else {
            $this->empresas = collect();
        }

        // Consultamos los usuarios si existen IDs
        if ($idSolicitanteIDs->isNotEmpty()) {
            $this->usuarios = User::whereIn('id', $idSolicitanteIDs)->get();
        } else {
            $this->usuarios = collect();
        }

        // Reordenamos la colección de clientes
        $this->ReorderClientesRecurrentes();
    }

    private function ReorderClientesRecurrentes()
    {
        // Se reordena la colección de clientes:
        // - Los que tienen empresa (según que exista en $this->empresas) tendrán prioridad (valor 0)
        // - Luego se ordena de mayor a menor según solicitud_count
        $this->clientesRecurrentes = $this->clientesRecurrentes->sort(function ($a, $b) {
            $aHasEmpresa = $this->empresas->contains('cuit', $a->empresa_solicitante) ? 0 : 1;
            $bHasEmpresa = $this->empresas->contains('cuit', $b->empresa_solicitante) ? 0 : 1;

            // Si ambos tienen (o no tienen) empresa, se ordena de mayor a menor por solicitud_count
            if ($aHasEmpresa === $bHasEmpresa) {
                return $b->solicitud_count <=> $a->solicitud_count;
            }
            // Los que tienen empresa (valor 0) pasan antes que los que no tienen (valor 1)
            return $aHasEmpresa <=> $bHasEmpresa;
        })->values(); // ->values() para reindexar la colección
    }



    private function numeroServiciosSolicitado()
    {
        return SolicitudesServiciosModel::where('empresa_prestadora', $this->idPrestadora)
            ->where('estado_presupuesto', 'Servicio Realizado, Solicitud Cerrada')->count();
    }

    private function ClientesRecurrentes()
    {
        return SolicitudesServiciosModel::select(
            'empresa_solicitante',
            'id_solicitante',
            DB::raw('COUNT(*) as solicitud_count')
        )
            ->where('empresa_prestadora', $this->idPrestadora)
            ->groupBy('empresa_solicitante', 'id_solicitante')
            ->havingRaw('COUNT(*) > 1')
            ->orderByRaw('COUNT(*) DESC')
            ->paginate(4); // Solo 4 registros por página
    }


    public function render()
    {
        return view('livewire.empresas.reportes-prestadora', [
            'totalGlobal' => $this->totalGlobal,
            'porcentajeContratacion1' => $this->porcentajeContratacion1,
            'porcentajeContratacion0' => $this->porcentajeContratacion0,
            'ClientesRecurrentes' => $this->clientesRecurrentes,
            'empresas' => $this->empresas,
            'usuarios' => $this->usuarios,
            'serviciosSolicitados' => $this->serviciosSolicitados,
            'rankingMotivos' => $this->rankingMotivos,
            'puntajeGoogle'           => $this->puntajeGoogle,
            'puntajeAssetFly'         => $this->puntajeAssetFly,
        ]);
    }
}
