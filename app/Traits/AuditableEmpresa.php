<?php

namespace App\Traits;

use App\Helpers\IdHelper;
use App\Models\AuditoriaEmpresaModel;

trait AuditableEmpresa
{
    /** nombre de la tabla; si queda vacío se usa $this->getTable() */
    protected string $claseAuditoria = '';

    /** Filtrado de campos para ACTUALIZACIÓN  */
    protected array $camposIncluirAuditoria = [];                 // vacío => todos
    protected array $camposExcluirAuditoria = ['created_at', 'updated_at'];

    /** Modo de escritura */
    protected bool $creacionCompacta = true;       // una sola fila en "Creacion"
    protected bool $actualizacionCompacta = false; // una sola fila en "Actualizacion" (opcional)

    /** Habilitar/deshabilitar auditoría temporalmente */
    protected static bool $auditoriaDeshabilitada = false;

    public static function sinAuditoria(callable $callback)
    {
        $prev = static::$auditoriaDeshabilitada;
        static::$auditoriaDeshabilitada = true;
        try {
            return $callback();
        } finally {
            static::$auditoriaDeshabilitada = $prev;
        }
    }

    /** Eloquent boot del trait */
    protected static function bootAuditableEmpresa(): void
    {
        static::created(fn($m) => $m->escribirAuditoria('creado'));
        static::updated(fn($m) => $m->escribirAuditoria('actualizado'));
        static::deleted(fn($m) => $m->escribirAuditoria('eliminado'));

        if (method_exists(static::class, 'restored')) {
            static::restored(fn($m) => $m->escribirAuditoria('restaurado'));
        }
    }

    /** Genera filas de auditoría */
    protected function escribirAuditoria(string $evento): void
    {
        if (static::$auditoriaDeshabilitada) return;
        if ($this instanceof AuditoriaEmpresaModel) return; // evita recursión

        $actor        = auth()->user();
        $autor        = $actor?->name ?? 'Sistema';
        $autorEmpresa = IdHelper::empresaActual()->razon_social;
        $ip           = request()?->ip();
        $cuit_empresa = IdHelper::empresaActual()->cuit;
        $clase    = $this->claseAuditoria ?: $this->getTable(); // ej.: 'bien' o 'act.activos'
        $pkNombre = $this->getKeyName();                        // ej.: 'id_activo'
        $pkValor  = (string) $this->getKey();                   // ej.: '3658'

        // Texto EXACTO para la columna 'evento'
        $eventoTexto = match ($evento) {
            'creado'      => 'Creacion',
            'actualizado' => 'Actualizacion',
            'eliminado'   => 'Eliminacion',
            'restaurado'  => 'Restauracion',
            default       => 'Actualizacion',
        };

        /* -------------------------
         *  CREACION (compacta = 1 fila)
         * ------------------------- */
        if ($evento === 'creado' && $this->creacionCompacta) {
            AuditoriaEmpresaModel::insert([[
                'clase_tabla'      => $clase,
                'pk_nombre'        => $pkNombre,
                'id_tabla'         => $pkValor,
                'campo'            => 'CREACION',   // marcador
                'valor_previo'     => null,
                'valor_actual'     => null,         // si querés, podés guardar JSON con atributos
                'evento'           => $eventoTexto, // <-- texto
                'fechahora_cambio' => now(),
                'autor'            => $autor,
                'autor_empresa'    => $autorEmpresa,
                'ip'               => $ip,
                'cuit_empresa'     => $cuit_empresa,
            ]]);
            return;
        }

        /* -------------------------
         *  ACTUALIZACION (fila única opcional)
         * ------------------------- */
        if ($evento === 'actualizado' && $this->actualizacionCompacta) {
            $dirty = array_keys($this->getDirty()); //getDirty() devuelve un array asociativo con todos los atributos del modelo
            if ($this->camposIncluirAuditoria) {
                $dirty = array_values(array_intersect($dirty, $this->camposIncluirAuditoria));
            } else {
                $dirty = array_values(array_diff($dirty, $this->camposExcluirAuditoria));
            }
            if (!$dirty) return;

            $cambios = [];
            foreach ($dirty as $campo) {
                $antes = $this->aTexto($this->getOriginal($campo));
                $desp  = $this->aTexto($this->getAttribute($campo));
                if ($this->normalizar($antes) === $this->normalizar($desp)) continue;
                $cambios[$campo] = [$antes, $desp];
            }
            if (!$cambios) return;

            AuditoriaEmpresaModel::insert([[
                'clase_tabla'      => $clase,
                'pk_nombre'        => $pkNombre,
                'id_tabla'         => $pkValor,
                'campo'            => 'ACTUALIZACION',
                'valor_previo'     => null,
                'valor_actual'     => json_encode($cambios, JSON_UNESCAPED_UNICODE),
                'evento'           => $eventoTexto, // <-- texto
                'fechahora_cambio' => now(),
                'autor'            => $autor,
                'autor_empresa'    => $autorEmpresa,
                'ip'               => $ip,
                'cuit_empresa'     => $cuit_empresa,
            ]]);
            return;
        }

        /* -------------------------
         *  Modo por CAMPO (default)
         * ------------------------- */
        $campos = match ($evento) {
            'creado'      => array_keys($this->getAttributes()), // solo si creacionCompacta = false
            'actualizado' => array_keys($this->getDirty()),
            'eliminado'   => array_keys($this->getOriginal()),
            'restaurado'  => array_keys($this->getDirty()),
            default       => array_keys($this->getDirty()),
        };

        if (!empty($this->camposIncluirAuditoria)) {
            $campos = array_values(array_intersect($campos, $this->camposIncluirAuditoria));
        } else {
            $campos = array_values(array_diff($campos, $this->camposExcluirAuditoria));
        }

        if ($evento === 'creado') {
            // En creación detallada no tiene sentido registrar la PK
            $campos = array_values(array_diff($campos, [$pkNombre]));
        }

        $filas = [];
        foreach ($campos as $campo) {
            $previo = null;
            $nuevo = null;

            if ($evento === 'creado') {
                $previo = null;
                $nuevo  = $this->getAttribute($campo);
            } elseif ($evento === 'eliminado') {
                $previo = $this->getOriginal($campo);
                $nuevo  = null;
            } else { // actualizado / restaurado
                $previo = $this->getOriginal($campo);
                $nuevo  = $this->getAttribute($campo);
                if ($this->normalizar($previo) === $this->normalizar($nuevo)) continue;
            }

            $filas[] = [
                'clase_tabla'      => $clase,
                'pk_nombre'        => $pkNombre,
                'id_tabla'         => $pkValor,
                'campo'            => $campo,
                'valor_previo'     => $this->aTexto($previo),
                'valor_actual'     => $this->aTexto($nuevo),
                'evento'           => $eventoTexto, // <-- texto
                'fechahora_cambio' => now(),
                'autor'            => $autor,
                'autor_empresa'    => $autorEmpresa,
                'ip'               => $ip,
                'cuit_empresa'     => $cuit_empresa,
            ];
        }

        if ($filas) {
            AuditoriaEmpresaModel::insert($filas);
        }
    }

    /** A texto para guardar */
    protected function aTexto($valor): ?string
    {
        if ($valor === null) return null;
        if ($valor instanceof \DateTimeInterface) return $valor->format('c');
        if (is_scalar($valor)) return (string) $valor;
        return json_encode($valor, JSON_UNESCAPED_UNICODE);
    }

    /** Normaliza para comparar cambios */
    protected function normalizar($valor): string
    {
        if ($valor === null) return '';
        if ($valor instanceof \DateTimeInterface) return $valor->format('c');
        if (is_bool($valor)) return (string) (int) $valor;
        if (is_numeric($valor)) return (string) +$valor;
        return (string) $valor;
    }
}
