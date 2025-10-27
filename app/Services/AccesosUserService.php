<?php
// app/Services/AccesosUserService.php

namespace App\Services;

use App\Models\AsignacionesRolesModel;
use App\Models\PermisoPorEmpresaModel;
use App\Models\PermisosRolesModel;
use App\Models\RutasModel;
use App\Helpers\IdHelper;

class AccesosUserService
{
    protected ?object $UsuarioEmpresa = null;
    protected ?object $empresa = null;

    protected array $roles = [];
    protected bool $isApoderado = false;
    protected bool $isTipo3 = false;
    protected bool $isPrivileged = false;

    public function __construct()
    {
        $this->UsuarioEmpresa = IdHelper::usuarioEmpresaActual();
        $this->empresa        = IdHelper::empresaActual();

        $this->cargarRoles();
        $this->checkPrivilegios();
    }

    protected function cargarRoles(): void
    {
        if (!$this->UsuarioEmpresa) {
            $this->roles = [];
            return;
        }

        $this->roles = AsignacionesRolesModel::where('usuario_empresa', $this->UsuarioEmpresa->id_usuario)
            ->where('cuit', $this->UsuarioEmpresa->cuit)
            ->where('id_relacion_empresa', $this->UsuarioEmpresa->id_relacion)
            ->pluck('id_rol')
            ->toArray();
    }

    protected function checkPrivilegios(): void
    {
        $ue = $this->UsuarioEmpresa;

        $this->isApoderado = $ue && ($ue->cargo === 'Apoderado');
        $this->isTipo3     = $ue && ((int) $ue->tipo_user === 3);
        $this->isPrivileged = $this->isApoderado || $this->isTipo3;
    }

    public function isApoderado(): bool
    {
        return $this->isApoderado;
    }
    public function isTipo3(): bool
    {
        return $this->isTipo3;
    }
    public function isPrivileged(): bool
    {
        return $this->isPrivileged;
    }

    public function getRoles(): array
    {
        return $this->roles;
    }

    public function getRutasPermitidas(): array
    {
        // Bypass total si es Apoderado o tipo_user === 3
        if ($this->isPrivileged) {
            return RutasModel::pluck('ruta')->toArray();
        }

        if (!$this->empresa) {
            return [];
        }

        $rolesPermisos = PermisosRolesModel::whereIn('id_rol', $this->roles)->pluck('id_permiso');
        if ($rolesPermisos->isEmpty()) {
            return [];
        }

        $permisos = PermisoPorEmpresaModel::whereIn('id_permiso', $rolesPermisos)
            ->where('tipo_empresa', $this->empresa->tipo)
            ->pluck('id_ruta');

        if ($permisos->isEmpty()) {
            return [];
        }

        return RutasModel::whereIn('id_ruta', $permisos)
            ->pluck('ruta')
            ->toArray();
    }

    public function getSidebarColor(): string
    {
        return ($this->empresa && (int) $this->empresa->tipo === 2)
            ? '#61c5dd'
            : '#1e06a3';
    }
}
