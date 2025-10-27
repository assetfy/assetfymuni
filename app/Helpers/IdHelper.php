<?php
// app/Helpers/IdHelper.php

namespace App\Helpers;

use App\Models\EmpresasModel;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Session;

class IdHelper
{
    public static function identificador()
    {
        $identificador = session()->get('cuitEmpresaSeleccionado');

        return $identificador !== null ? $identificador : auth()->user()->cuil;
    }

    public static function identificadorParcial()
    {
        $cuitEmpresaSeleccionado = session()->get('cuitEmpresaSeleccionado');
        $user = auth()->user()->cuil;

        // Verifica si hay un usuario autenticado antes de acceder a sus atributos
        if ($cuitEmpresaSeleccionado) {
            $identificadores = [
                'cuit' => $cuitEmpresaSeleccionado,
                'user' => null,
            ];
        } else {
            $identificadores = [
                'cuit' =>  null,
                'user' => $user,
            ];
        }
        return $identificadores;
    }

    public static function idEmpresa()
    {
        $cuitEmpresaSeleccionado = Session::get('cuitEmpresaSeleccionado') ?? (Auth::check() ? Auth::user()->entidad : null);
        return $cuitEmpresaSeleccionado;
    }

    public static function identificadoresCompletos()
    {
        $cuitEmpresaSeleccionado = session()->get('cuitEmpresaSeleccionado');
        $user = auth()->user();

        // Verifica si hay un usuario autenticado antes de acceder a sus atributos
        if ($cuitEmpresaSeleccionado) {
            $identificadores = [
                'cuit' => $cuitEmpresaSeleccionado,
                'user' => null,
            ];
        } else {
            $identificadores = [
                'cuit' =>  null,
                'user' => $user,
            ];
        }

        return $identificadores;
    }

    /**
     * Devuelve el modelo Empresa basado en el CUIT en sesión o del usuario.
     * Lo cachea 1 hora para acelerar futuras consultas.
     */
    public static function empresaActual(): ?EmpresasModel
    {
        $cuit = self::idEmpresa();

        if (! $cuit) {
            return null;
        }

        // TTL de 60 minutos
        return Cache::remember(
            "empresa_actual_{$cuit}",
            now()->addHour(),
            fn() => EmpresasModel::where('cuit', $cuit)->first()
        );
    }

    /**
     * Olvida la cache de la empresa actual.
     * Úsalo en los eventos Eloquent (booted()) de EmpresasModel.
     */
    public static function olvidarEmpresaActual(): void
    {
        $cuit = self::idEmpresa();
        $userId = Auth::id();
        if ($cuit) {
            Cache::forget("empresa_actual_{$cuit}");
            cache::forget("usuario_actual_{$userId}");
        }
    }

    public static function usuarioEmpresaActual(): ?\App\Models\UsuariosEmpresasModel
    {
        $cuit = self::idEmpresa();
        $userId = Auth::id();

        if (! $cuit || ! $userId) {
            return null;
        }
        return Cache::remember(
            "usuario_empresa_{$userId}_{$cuit}",
            now()->addMinutes(60),
            fn() => \App\Models\UsuariosEmpresasModel::where('id_usuario', $userId)
                ->where('cuit', $cuit)
                ->first()
        );
    }

    public static function usuarioActual(): ?User
    {
        $userId = Auth::id();

        if (! $userId) {
            return null;
        }

        return Cache::remember(
            "usuario_actual_{$userId}",
            now()->addMinutes(60),
            fn() => User::find($userId)
        );
    }
}
