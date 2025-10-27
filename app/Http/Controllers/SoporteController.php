<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Mail\SoporteMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\JsonResponse;
use App\Models\EmpresasModel;

class SoporteController extends Controller
{
    /**
     * Procesa el envío de soporte (POST /soporte).
     */
    public function store(Request $request): JsonResponse
    {
        // 1) Validar campos
        $data = $request->validate([
            'asunto'      => 'required|string|max:255',
            'descripcion' => 'required|string',
            'adjunto.*'   => 'nullable|file|max:10240',
        ]);

        // 2) Datos del usuario autenticado
        $user = $request->user();
        $nombreUsuario = $user->name;
        $correoUsuario = $user->email;
        $empresa = EmpresasModel::where('cuit', $user->entidad)
            ->value('razon_social') ?? '—';

        try {
            // 3) Enviar correo PASANDO 5 ARGUMENTOS
            Mail::to('support@asset-fy.atlassian.net')
                ->send(new SoporteMail(
                    $data['asunto'],        // 1
                    $data['descripcion'],   // 2
                    $nombreUsuario,         // 3
                    $correoUsuario,         // 4
                    $empresa,
                    $request->file('adjunto')          // 5
                ));

            // 4) Responder éxito
            return response()->json([
                'title'   => '¡Enviado!',
                'message' => 'Tu solicitud de soporte fue enviada con éxito.',
            ], 201);
        } catch (\Throwable $e) {
            // 5) Capturar y devolver error para depuración
            return response()->json([
                'message' => 'Error al enviar el correo: ' . $e->getMessage(),
                'trace'   => $e->getTraceAsString(),
            ], 500);
        }
    }
}
