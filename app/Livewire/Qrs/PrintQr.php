<?php

namespace App\Livewire\Qrs;

use Livewire\Component;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Storage;
use App\Models\ActivosModel;
use App\Models\QrBienModel;
use App\Helpers\IdHelper;

class PrintQr extends Component
{
    protected $listeners = ['open-qr-modal' => 'openQrModal'];

    public bool $open = false;

    public array $selectedIds = [];
    public int   $selectedCount = 0;

    public string $pageSize = 'A4';
    public string $orientation = 'portrait';
    public ?int    $margin = 10;
    public ?int    $gap    = 6;

    public ?int $labelW = 70;
    public ?int $labelH = 85;
    public ?int $qrSize = 42;
    public ?int $padding = 4;

    public const MIN_QR_MM      = 32;
    public const MIN_LABEL_W_MM = 50;
    public const MIN_LABEL_H_MM = 60;

    public bool $withExtras = true;
    public ?string $logoEmp = null;
    public ?string $textoEmp = null;

    public array $qrUrls = [];     // cache [assetId => url]
    public array $assetNames = []; // cache [assetId => nombre]

    public function openQrModal($payload): void
    {
        // Normaliza: puede venir como [$id1, $id2] o ['ids' => [...]]
        $ids = is_array($payload) && array_is_list($payload)
            ? $payload
            : (is_array($payload['ids'] ?? null) ? $payload['ids'] : []);

        // dd($ids);
        $this->selectedIds   = array_values(array_unique($ids));
        $this->selectedCount = count($this->selectedIds);
        $this->labelH = $this->withExtras ? 85 : 70;

        $this->assetNames = ActivosModel::query()
            ->whereIn('id_activo', $this->selectedIds)
            ->pluck('nombre', 'id_activo')
            ->toArray();

        $this->open = true;
    }

    // public function updated($field): void
    // {
    //     $this->qrSize = max(self::MIN_QR_MM, $this->qrSize);
    //     $this->labelW = max(self::MIN_LABEL_W_MM, $this->labelW);
    //     $this->labelH = max(self::MIN_LABEL_H_MM, $this->labelH);

    //     $minW = $this->qrSize + 2 * $this->padding;
    //     $minH = $this->qrSize + 2 * $this->padding + ($this->withExtras ? 10 : 0);
    //     $this->labelW = max($this->labelW, $minW);
    //     $this->labelH = max($this->labelH, $minH);
    // }

    public function getPageDimsProperty(): array
    {
        [$w, $h] = [210, 297];
        if ($this->orientation === 'landscape') [$w, $h] = [$h, $w];
        return [$w, $h];
    }

    public function getUsableDimsProperty(): array
    {
        [$w, $h] = $this->pageDims;
        return [$w - 2 * $this->margin, $h - 2 * $this->margin];
    }

    public function getPerRowProperty(): int
    {
        [$uw] = $this->usableDims;
        return max(1, (int) floor(($uw + $this->gap) / ($this->labelW + $this->gap)));
    }

    public function getPerColProperty(): int
    {
        [, $uh] = $this->usableDims;
        return max(1, (int) floor(($uh + $this->gap) / ($this->labelH + $this->gap)));
    }

    // public function getPerPageProperty(): int
    // {
    //     return $this->perRow * $this->perCol;
    // }
    public function getTotalPagesProperty(): int
    {
        return $this->perPage <= 0 ? 0 : (int) ceil($this->selectedCount / $this->perPage);
    }

    // PrintQr.php

    public function getValidationErrorProperty(): ?string
    {
        // Tamaño de hoja y área útil
        [$pw, $ph] = $this->pageDims;
        [$uw, $uh] = $this->usableDims; // ya lo tienes

        // 1) Margen no puede consumir toda la hoja
        if ($this->margin * 2 >= $pw || $this->margin * 2 >= $ph) {
            return 'El margen es demasiado grande para el tamaño de la hoja.';
        }

        // 2) La etiqueta debe caber en el área útil
        if ($this->labelW > $uw) {
            return 'La etiqueta es más ancha que el área útil (máx: ' . floor($uw) . ' mm).';
        }
        if ($this->labelH > $uh) {
            return 'La etiqueta es más alta que el área útil (máx: ' . floor($uh) . ' mm).';
        }

        // 3) El QR + padding debe entrar en la etiqueta
        $minLado = $this->qrSize + 2 * $this->padding;
        if ($minLado > $this->labelW || $minLado > $this->labelH) {
            return 'El QR con su padding no entra dentro de la etiqueta.';
        }

        return null; // todo OK
    }

    public function getIsLayoutValidProperty(): bool
    {
        return $this->validationError === null;
    }

    // Si el layout no es válido, no mostramos celdas
    public function getPerPageProperty(): int
    {
        return $this->isLayoutValid ? ($this->perRow * $this->perCol) : 0;
    }

    public function open(): void
    {
        $this->open = true;
    }
    public function close(): void
    {
        $this->open = false;
    }

    public function print(): void
    {
        if (! $this->isLayoutValid) return;
        $this->dispatch('print-qr'); // NO cambies el nombre si usas x-on:print-qr.window
    }

    public function qrUrlFor(int $assetId): string
    {
        if (isset($this->qrUrls[$assetId])) return $this->qrUrls[$assetId];

        $base = rtrim(config('app.url'), '/');
        $enc  = urlencode(Crypt::encrypt($assetId));
        $url  = $base . '/datos-activos/' . $enc;

        return $this->qrUrls[$assetId] = 'https://quickchart.io/qr?text=' . urlencode($url) . '&size=400';
    }

    public function updatedLabelW($v)
    {
        $this->labelW  = is_numeric($v) ? max(self::MIN_LABEL_W_MM, (int)$v) : self::MIN_LABEL_W_MM;
    }
    public function updatedLabelH($v)
    {
        $this->labelH  = is_numeric($v) ? max(self::MIN_LABEL_H_MM, (int)$v) : self::MIN_LABEL_H_MM;
    }
    public function updatedMargin($v)
    {
        $this->margin  = is_numeric($v) ? max(0, (int)$v) : 0;
    }
    public function updatedGap($v)
    {
        $this->gap     = is_numeric($v) ? max(0, (int)$v) : 0;
    }
    public function updatedQrSize($v)
    {
        $this->qrSize  = is_numeric($v) ? max(self::MIN_QR_MM, (int)$v) : self::MIN_QR_MM;
    }
    public function updatedPadding($v)
    {
        $this->padding = is_numeric($v) ? max(0, (int)$v) : 0;
    }

    public function render()
    {
        return view('livewire.qrs.print-qr');
    }
}
