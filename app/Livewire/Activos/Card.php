<?php

namespace App\Livewire\Activos;

use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use App\Traits\SortableTrait;
use App\Models\ActivosModel;
use Livewire\WithPagination;
use Livewire\Component;
use Dompdf\Dompdf;
use Dompdf\Options;

class Card extends Component
{
    use SortableTrait;
    use WithPagination;

    public $search = ''; // Variable para búsqueda
    public $sort = 'id_activo'; // Campo por el cual ordenar
    public $direction = 'desc'; // Dirección de ordenamiento
    public $open;
    public $tipo, $tipos, $altas, $general, $subcategorias, $categorias, $controlesCategoria, $controlesSubCategoria, $id;

    protected $listeners = ['render'];

    // Inicializar variables
    public function mount($id_tipo)
    {
        $this->tipo = $id_tipo;
        Session::put('tipo', $this->tipo); // Asigna el valor de la vble recibida por parametro a tipo
        $this->id = $this->getUserId(); // Obtiene el ID del usuario
        $this->cargarDatosTipos(); // Invoca a la funcion cargarDatosTipos
    }

    public function render()
    {
        $activos = $this->fetchActivos($this->id);
        return view('livewire.activos.card', compact('activos'));
    }

    // Esta funcion permite obtener todos los datos de la tabla de tipos
    private function cargarDatosTipos()
    {
        return DB::table('act.tipos')->get();
    }

    // Obtiene el ID del usuario
    private function getUserId()
    {
        $id = session('cuitEmpresaSeleccionado');
        if ($id == null) {
            $id = auth()->user()->cuil;
        }
        return $id;
    }

    // Obtiene los activos del usuario
    private function fetchActivos($id)
    {
        return ActivosModel::where('id_tipo', $this->tipo)
            ->where(function ($query) use ($id) {
                $query->where('usuario_titular', (int)$id)
                    ->orWhere('empresa_titular', (int)$id);
            })
            ->when($this->search, function ($query) {
                $query->where('nombre', 'like', '%' . $this->search . '%');
            })
            ->whereHas('estadoGeneral', function ($query) {
                $query->where('nombre', '!=', 'Baja');
            })
            ->orderBy($this->sort, $this->direction)
            ->paginate(6);
    }

    public function download()
    {
        $tipo = session()->get('tipo');

        $id = $this->getUserId(); // Obtiene el ID del usuario

        $activos = ActivosModel::where('id_tipo', $tipo)
            ->where(function ($query) use ($id) {
                $query->where('usuario_titular', (int)$id)
                    ->orWhere('empresa_titular', (int)$id);
            })
            ->whereHas('estadoGeneral', function ($query) {
                $query->where('nombre', '!=', 'Baja');
            })->get();

        $options = new Options();
        $options->set('defaultFont', 'Arial');
        $dompdf = new Dompdf($options);

        $html = '<html><body>';
        $html .= '<h1 style="text-align: center;">Códigos QR de Activos en Assetfy</h1>';
        $html .= '<table style="width: 100%; text-align: center; border-spacing: 10px;">'; // Tabla para estructurar por columnas
        $html .= '<tr>';

        $count = 0; // Contador para gestionar las filas

        foreach ($activos as $activo) {
            $qrUrl = 'https://quickchart.io/qr?text=http://192.168.3.165:8000/datos-activos/' . $activo->id_activo . '&caption=' . $activo->nombre . '&captionFontFamily=mono&captionFontSize=15';

            $qrImageData = base64_encode(file_get_contents($qrUrl));

            // Crea una celda para cada QR
            $html .= '<td style="width: 50%; padding: 10px;">';
            $html .= '<img src="data:image/png;base64,' . $qrImageData . '" style="width: 100%; height: auto;" />';
            $html .= '</td>';

            $count++;

            // Crea una nueva fila cada dos celdas
            if ($count % 2 == 0) {
                $html .= '</tr><tr>';
            }
        }

        // Cierra la última fila y la tabla
        $html .= '</tr>';
        $html .= '</table>';
        $html .= '</body></html>';

        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        // Descargar el archivo PDF
        return $dompdf->stream('qr_codigos-activos.pdf');
    }

    public function openCreateControlModal($activo)
    {
        $this->dispatch('openModal', ['activoId' => $activo])->to('controles.activoscontroles.create-controles-activos');
    }

    public function openCreateAtributoModal($activo)
    {
        $this->dispatch('openModal', ['activoId' => $activo])->to('atributos.nuevosatributos.create-nuevo-atributos-activos');
    }

    public function openServiciosActivosModal($activo)
    {
        $this->dispatch('openModal', ['activoId' => $activo])->to('servicios.activos.crear-solicitud-servicio');
    }


    public function openModalCambiarUbicacion($activo)
    {
        $this->dispatch('openModalCambiarUbicacion', ['activo' => $activo])->to('ubicaciones.cambiar-ubicacion');
    }
}
