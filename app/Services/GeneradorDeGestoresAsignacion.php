<?php

namespace App\Services;

use App\Models\ActivosAsignacionModel;
use App\Models\NotificacionesModel;
use Carbon\Carbon;

class GeneradorDeGestoresAsignacion
{
    protected $activo;
    protected ?int $idAsignado;
    protected ?int $idResponsable;
    protected string $fechaAsignacion;
    protected int $idCreador;
    protected string $empresaEmpleados;

    /**
     * @param  \App\Models\ActivosModel  $activo
     * @param  int|null                   $idAsignado     ID de usuario asignado (o null)
     * @param  int|null                   $idResponsable  ID de usuario responsable (o null)
     * @param  string                     $fechaAsignacion Fecha en formato 'Y-m-d H:i:s' o parsable
     * @param  int                        $idCreador      ID del usuario que crea la asignación
     * @param  string                     $empresaEmpleados CUIT de la empresa
     */
    public function __construct($activo, ?int $idAsignado, ?int $idResponsable, ?string     $fechaAsignacion, int $idCreador, string $empresaEmpleados)
    {
        $this->activo            = $activo;
        $this->idAsignado        = $idAsignado;
        $this->idResponsable     = $idResponsable;
        $this->fechaAsignacion   = $fechaAsignacion  ?? now()->format('Y-m-d H:i:s');;
        $this->idCreador         = $idCreador;
        $this->empresaEmpleados  = $empresaEmpleados;
    }

    /**
     * Crea el registro en ActivosAsignacionModel y, si aplica, la notificación.
     */
    public function handle(): void
    {
        // 1) Armar data básica
        $data = [
            'id_activo'        => $this->activo->id_activo,
            'id_tipo'          => $this->activo->id_tipo,
            'id_categoria'     => $this->activo->id_categoria,
            'id_subcategoria'  => $this->activo->id_subcategoria,
            'asignado_a'       => $this->idAsignado,
            'gestionado_por'   => $this->idCreador,
            'fecha_asignacion' => Carbon::parse($this->fechaAsignacion)->format('Y-m-d H:i:s'),
            'responsable'      => $this->idResponsable ?? $this->idCreador,
            'empresa_empleados' => $this->empresaEmpleados,
            'estado_asignacion' => 'Aceptado',
        ];

        // 2) Crear la asignación
        ActivosAsignacionModel::create($data);

        // 3) Si hay un usuario asignado, crear notificación
        if ($this->idAsignado) {
            NotificacionesModel::create([
                'cuit_empresa' => $this->empresaEmpleados,
                'id_usuario'   => $this->idAsignado,
                'descripcion'  => 'Se le ha asignado el bien ' . $this->activo->nombre,
            ]);
        }
    }
}
