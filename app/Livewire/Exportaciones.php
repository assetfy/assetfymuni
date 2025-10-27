<?php

namespace App\Livewire;

use App\Helpers\IdHelper;
use App\Livewire\Exports\BienesClientesTemplateExport;
use Livewire\Component;
use App\Livewire\Exports\BienesTemplateExport;
use App\Livewire\Exports\CotizacionesTemplateExport;
use App\Livewire\Exports\OrdenesTrabajoTemplateExport;
use App\Livewire\Exports\SolicitudesTemplateExport;
use App\Livewire\Exports\UbicacionesClientesTemplateExport;
use App\Livewire\Exports\UbicacionesPropiasTemplateExport;
use App\Livewire\Exports\UsuariosTemplateExport;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Excel as ExcelFormat;

class Exportaciones extends Component
{
    public $tipoDatos = 1;
    public string $empresaCuit = '';

    public function render()
    {
        return view('livewire.exportaciones');
    }

    public function descargarEjemploActivo()
    {
        $cuit = IdHelper::idEmpresa(); // o $this->empresaCuit
        $filename = 'Bienes_Propios_' . $cuit . '_' . now()->format('Ymd_His') . '.xlsx';

        // Genera el XLSX en memoria y devuélvelo como descarga
        return Excel::download(
            new BienesTemplateExport($cuit),
            $filename,
            ExcelFormat::XLSX,
            [
                'Content-Disposition'       => 'attachment; filename="' . $filename . '"; filename*=UTF-8\'\'' . rawurlencode($filename),
                'Content-Type'              => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'X-Content-Type-Options'    => 'nosniff',
                'Cache-Control'             => 'no-store, no-cache, must-revalidate, max-age=0',
                'Pragma'                    => 'no-cache',
            ]
        );
    }

    public function descargarEjemploClientes()
    {
        $cuit = IdHelper::idEmpresa(); // o $this->empresaCuit
        $filename = 'Bienes_Clientes_' . $cuit . '_' . now()->format('Ymd_His') . '.xlsx';

        // Genera el XLSX en memoria y devuélvelo como descarga
        return Excel::download(
            new BienesClientesTemplateExport($cuit),
            $filename,
            ExcelFormat::XLSX,
            [
                'Content-Disposition'       => 'attachment; filename="' . $filename . '"; filename*=UTF-8\'\'' . rawurlencode($filename),
                'Content-Type'              => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'X-Content-Type-Options'    => 'nosniff',
                'Cache-Control'             => 'no-store, no-cache, must-revalidate, max-age=0',
                'Pragma'                    => 'no-cache',
            ]
        );
    }

    public function descargarEjemploUsuario()
    {
        $cuit = IdHelper::idEmpresa(); // o $this->empresaCuit
        $filename = 'Usuarios_' . $cuit . '_' . now()->format('Ymd_His') . '.xlsx';

        // Genera el XLSX en memoria y devuélvelo como descarga
        return Excel::download(
            new UsuariosTemplateExport($cuit),
            $filename,
            ExcelFormat::XLSX,
            [
                'Content-Disposition'       => 'attachment; filename="' . $filename . '"; filename*=UTF-8\'\'' . rawurlencode($filename),
                'Content-Type'              => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'X-Content-Type-Options'    => 'nosniff',
                'Cache-Control'             => 'no-store, no-cache, must-revalidate, max-age=0',
                'Pragma'                    => 'no-cache',
            ]
        );
    }

    public function descargarEjemploUbicacionesPropias()
    {
        $cuit = IdHelper::idEmpresa(); // o $this->empresaCuit
        $filename = 'Ubicaciones_Propias_' . $cuit . '_' . now()->format('Ymd_His') . '.xlsx';

        // Genera el XLSX en memoria y devuélvelo como descarga
        return Excel::download(
            new UbicacionesPropiasTemplateExport($cuit),
            $filename,
            ExcelFormat::XLSX,
            [
                'Content-Disposition'       => 'attachment; filename="' . $filename . '"; filename*=UTF-8\'\'' . rawurlencode($filename),
                'Content-Type'              => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'X-Content-Type-Options'    => 'nosniff',
                'Cache-Control'             => 'no-store, no-cache, must-revalidate, max-age=0',
                'Pragma'                    => 'no-cache',
            ]
        );
    }

    public function descargarEjemploUbicacionesClientes()
    {
        $cuit = IdHelper::idEmpresa(); // o $this->empresaCuit
        $filename = 'Ubicaciones_Clientes_' . $cuit . '_' . now()->format('Ymd_His') . '.xlsx';

        // Genera el XLSX en memoria y devuélvelo como descarga
        return Excel::download(
            new UbicacionesClientesTemplateExport($cuit),
            $filename,
            ExcelFormat::XLSX,
            [
                'Content-Disposition'       => 'attachment; filename="' . $filename . '"; filename*=UTF-8\'\'' . rawurlencode($filename),
                'Content-Type'              => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'X-Content-Type-Options'    => 'nosniff',
                'Cache-Control'             => 'no-store, no-cache, must-revalidate, max-age=0',
                'Pragma'                    => 'no-cache',
            ]
        );
    }

    public function descargarEjemploOrdenes()
    {
        $cuit = IdHelper::idEmpresa(); // o $this->empresaCuit
        $filename = 'Ordenes_Trabajo_' . $cuit . '_' . now()->format('Ymd_His') . '.xlsx';

        // Genera el XLSX en memoria y devuélvelo como descarga
        return Excel::download(
            new OrdenesTrabajoTemplateExport($cuit),
            $filename,
            ExcelFormat::XLSX,
            [
                'Content-Disposition'       => 'attachment; filename="' . $filename . '"; filename*=UTF-8\'\'' . rawurlencode($filename),
                'Content-Type'              => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'X-Content-Type-Options'    => 'nosniff',
                'Cache-Control'             => 'no-store, no-cache, must-revalidate, max-age=0',
                'Pragma'                    => 'no-cache',
            ]
        );
    }

    public function descargarEjemploCotizaciones()
    {
        $cuit = IdHelper::idEmpresa(); // o $this->empresaCuit
        $filename = 'Cotizaciones_' . $cuit . '_' . now()->format('Ymd_His') . '.xlsx';

        // Genera el XLSX en memoria y devuélvelo como descarga
        return Excel::download(
            new CotizacionesTemplateExport($cuit),
            $filename,
            ExcelFormat::XLSX,
            [
                'Content-Disposition'       => 'attachment; filename="' . $filename . '"; filename*=UTF-8\'\'' . rawurlencode($filename),
                'Content-Type'              => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'X-Content-Type-Options'    => 'nosniff',
                'Cache-Control'             => 'no-store, no-cache, must-revalidate, max-age=0',
                'Pragma'                    => 'no-cache',
            ]
        );
    }

    public function descargarEjemploSolicitudes()
    {
        $cuit = IdHelper::idEmpresa(); // o $this->empresaCuit
        $filename = 'Solicitudes_' . $cuit . '_' . now()->format('Ymd_His') . '.xlsx';

        // Genera el XLSX en memoria y devuélvelo como descarga
        return Excel::download(
            new SolicitudesTemplateExport($cuit),
            $filename,
            ExcelFormat::XLSX,
            [
                'Content-Disposition'       => 'attachment; filename="' . $filename . '"; filename*=UTF-8\'\'' . rawurlencode($filename),
                'Content-Type'              => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'X-Content-Type-Options'    => 'nosniff',
                'Cache-Control'             => 'no-store, no-cache, must-revalidate, max-age=0',
                'Pragma'                    => 'no-cache',
            ]
        );
    }
}
