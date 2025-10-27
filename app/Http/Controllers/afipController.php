<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Afip;

class afipController extends Controller
{
    public function verify(Request $request)
    {
        // Establecer el tiempo de espera de la solicitud
        ini_set('max_execution_time', 60); // Establecer el tiempo de espera en 30 segundos

        try {
            // Llamar a la función para verificar AFIP
            $result = $this->verificarAfip($request->cuit);
            return $result;
        } catch (\Exception $e) {
            // Manejar cualquier excepción capturada mostrando un mensaje de error
            return "La API de AFIP está fuera de servicio. Por favor, inténtalo de nuevo más tarde.";
        }
    }

    public function verificarAfip($cuit)
{
    // Asegúrate de que el CUIT sea un string
    $cuit = (string)$cuit;

    // Validar que el CUIT tiene 11 caracteres (longitud estándar de un CUIT)
    if (strlen($cuit) !== 11 || !ctype_digit($cuit)) {
        return "El CUIT debe tener 11 dígitos.";
    }

    try {
        // Crea una instancia de Afip
        $afip = new Afip(array('CUIT' => 20409378472));

        // Obtén los detalles del contribuyente
        $taxpayer_details = $afip->RegisterInscriptionProof->GetTaxpayerDetails($cuit);

        // Verifica si se recibió un error de constancia
        if (isset($taxpayer_details->errorConstancia)) {
            $errors = $taxpayer_details->errorConstancia->error;
            $errorMessage = implode("\n", $errors);
            return $errorMessage;
        } elseif ($taxpayer_details && $taxpayer_details->datosGenerales && $taxpayer_details->datosGenerales->estadoClave == 'ACTIVO') {
            return $taxpayer_details;
        } else {
            return "El contribuyente no está activo o no está registrado";
        }
    } catch (\Exception $e) {
        return "La API de AFIP está fuera de servicio. Por favor, inténtalo de nuevo más tarde.";
    }
}

}
