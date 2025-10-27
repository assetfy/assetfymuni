<?php

namespace App\Livewire\Empresas\Contratos;

use Livewire\Component;
use Illuminate\Validation\Rule;
use App\Models\ContratoModel;
use App\Models\ContratosTiposModel;
use App\Models\EstadoContradoModel;

class EditarContrato extends Component
{
    // Modal
    public bool $open = false;

    // Identificador
    public ?int $contratoId = null;

    // Campos editables
    public ?string $nombre = null;
    public ?string $fecha_inicio = null;   // formato YYYY-MM-DD
    public ?string $fecha_fin = null;      // formato YYYY-MM-DD
    public $id_tipo_contrato = null;
    public $id_estado_contrato = null;
    public ?float $monto = null;
    public ?string $moneda = 'ARS';

    // Solo lectura / display
    public ?string $nro_contrato = null;
    public ?string $prestadora_cuit = null;
    public ?string $prestadora_nombre = null;

    // Listas para selects
    public array $tipos = [];
    public array $estados = [];
    public array $monedas = [
        'ARS' => 'ARS $',
        'USD' => 'USD $',
        'EUR' => 'EUR €',
    ];

    protected $listeners = [
        // Espera: $dispatch('openEditContrato', contratoId)
        'openEditContrato' => 'open',
    ];

    public function render()
    {
        return view('livewire.empresas.contratos.editar-contrato');
    }

    public function open($data)
    {
        $this->resetValidation();
        $this->loadLists();

        // Traemos lo necesario + relaciones
        $c = ContratoModel::select(
            'id_contrato',
            'nro_contrato',
            'nombre',
            'fecha_inicio',
            'fecha_fin',
            'id_tipo_contrato',
            'id_estado_contrato',
            'monto',
            'moneda',
            'prestadora'
        )
            ->with([
                'tiposContratos:id_tipo_contrato,nombre',
                'prestadoras:cuit,razon_social',
                'estadoContrato:id_estado_contrato,nombre_estado',
            ])
            ->findOrFail($data);

        // Mapear a los campos del formulario
        $this->contratoId        = $c->id_contrato;
        $this->nro_contrato      = (string) $c->nro_contrato;
        $this->nombre            = (string) $c->nombre;
        $this->fecha_inicio      = $c->fecha_inicio ? substr((string)$c->fecha_inicio, 0, 10) : null;
        $this->fecha_fin         = $c->fecha_fin ? substr((string)$c->fecha_fin, 0, 10) : null;
        $this->id_tipo_contrato  = $c->id_tipo_contrato;
        $this->id_estado_contrato = $c->id_estado_contrato;
        $this->monto             = $c->monto !== null ? (float)$c->monto : null;
        $this->moneda            = $c->moneda ?: 'ARS';
        $this->prestadora_cuit   = (string)$c->prestadora;
        $this->prestadora_nombre = optional($c->prestoras)->razon_social
            ?? optional($c->prestadoras)->razon_social // por si tenés el nombre 'prestoras' en algún lugar
            ?? optional($c->prestadoras)->razon_social;

        $this->open = true;
    }

    protected function loadLists(): void
    {
        $this->tipos   = ContratosTiposModel::orderBy('nombre')->pluck('nombre', 'id_tipo_contrato')->toArray();
        $this->estados = EstadoContradoModel::orderBy('nombre_estado')->pluck('nombre_estado', 'id_estado_contrato')->toArray();
    }

    protected function rules(): array
    {
        return [
            'nombre'             => ['required', 'string', 'max:255'],
            'fecha_inicio'       => ['required', 'date'],
            'fecha_fin'          => ['nullable', 'date', 'after_or_equal:fecha_inicio'],
            'id_tipo_contrato'   => ['required', Rule::in(array_keys($this->tipos))],
            'id_estado_contrato' => ['required', Rule::in(array_keys($this->estados))],
            'monto'              => ['required', 'numeric', 'min:0'],
            'moneda'             => ['required', Rule::in(array_keys($this->monedas))],
        ];
    }

    public function update(): void
    {
        $this->validate();

        if (!$this->contratoId) {
            $this->dispatch('swal', type: 'error', title: 'Error', text: 'Contrato no identificado.');
            return;
        }

        ContratoModel::where('id_contrato', $this->contratoId)->update([
            'nombre'             => $this->nombre,
            'fecha_inicio'       => $this->fecha_inicio,
            'fecha_fin'          => $this->fecha_fin,
            'id_tipo_contrato'   => $this->id_tipo_contrato,
            'id_estado_contrato' => $this->id_estado_contrato,
            'monto'              => $this->monto,
            'moneda'             => $this->moneda,
        ]);

        $this->dispatch('refreshLivewireTable');
        $this->dispatch('lucky');

        $this->open = false;
    }
}
