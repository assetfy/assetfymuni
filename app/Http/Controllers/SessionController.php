<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Auth;
use Illuminate\Auth\Access\AuthorizationException;
use App\Models\User;
use App\Actions\Jetstream\DeleteUser;

class SessionController extends Controller
{
    // Actualizar la sesión con el nombre del usuario y la vista actual
    public function updateSession(Request $request)
    {
        session(['userName' => $request->userName]);
        session(['currentView' => $request->currentView]);

        return response()->json(['status' => 'success']);
    }

    // Obtener los valores de la sesión
    public function getSessionValues()
    {
        return response()->json([
            'userName' => session('userName'),
            'currentView' => session('currentView')
        ]);
    }

    // Funcion creada para la verificacion de un usuario recien registrado. SI EL USUARIO NO ACEPTA LA VERIFICACION DE CORREO NO PODRA ACCEDER A SU CUENTA
    public function verify(Request $request, $id, $hash)
    {
        // Verifica el enlace de verificación del correo electrónico
        if (! URL::hasValidSignature($request)) {
            abort(403, 'Invalid or expired URL.');
        }

        // Verifica el usuario basado en el ID y el hash
        $user = User::findOrFail($id);

        if (! hash_equals($hash, sha1($user->email))) {
            throw new AuthorizationException('Invalid verification link.');
        }

        if ($user->markEmailAsVerified()) {
            // Redirige al inicio de sesión con un mensaje de éxito
            return redirect()->route('login')->with('status', 'Your email has been verified. Please log in.');
        }

        return redirect()->route('login')->withErrors(['verification' => 'Your email has already been verified.']);
    }

    public function resendVerification(Request $request)
    {
        // Verifica que el usuario esté autenticado
        $request->user()->sendEmailVerificationNotification();
    
        // Redirige de vuelta con un mensaje de éxito
        return redirect()->route('verification.notice')->with('status', 'verification-link-sent');
    }

    // Método para eliminar la cuenta del usuario
    public function destroy(Request $request, DeleteUser $action)
    {
        $user = Auth::user();
        
        // Llama a la acción de eliminación
        $action->delete($user);
        
        // Cierra la sesión del usuario
        Auth::logout();

        // Redirige al usuario a la ruta de login
        return redirect()->route('login')->with('status', 'Tu cuenta ha sido desactivada.');
    }
}
