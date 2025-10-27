@props([
'qrSrc' => null,
'assetName' => 'Nombre del bien',
'logoUrl' => null,
'text' => null,
'w' => '70mm',
'h' => '100mm',
'padding' => '3mm',
'space' => '2mm', // espacio base (sigue existiendo)
'qrSize' => '40mm',
'border' => true,

// <<< NUEVOS GAPS ESPECÍFICOS>>>
  'gapQrTitle' => '2.2mm', // separación QR → Nombre (antes ~1.2mm)
  'gapTitleLogo' => '2.0mm', // separación Nombre → Logo
  'gapLogoText' => '2.2mm', // separación Logo → Texto
  'gapTitleText' => '1.6mm', // separación Nombre → Texto (cuando NO hay logo)
  ])

  @php
  $hasLogo = filled($logoUrl);
  $hasText = filled($text);
  $compact = !$hasLogo && !$hasText; // sin logo ni texto
  @endphp

  <style>
    .qr-paper {
      background: #fff;
      border: 1px dashed #D1D5DB;
      border-radius: 10px;
    }

    @media screen {
      .qr-paper {
        height: auto
      }
    }

    @media print {
      @page {
        size: {
            {
            $w
          }
        }

          {
            {
            $h
          }
        }

        ;
        margin:0
      }

      .qr-paper {
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
      }
    }

    .qr-header {
      margin-bottom: calc(var(--space) * .7)
    }

    /* mantiene tu header compacto */
    .qr-box {
      width: var(--qr);
      height: var(--qr)
    }

    /* <<< márgenes FINOS controlados por variables >>> */
    .qr-title {
      margin-top: var(--gap-qr-title)
    }

    /* QR → Nombre */
    .qr-logo {
      margin-top: var(--gap-title-logo)
    }

    /* Nombre → Logo */
    .qr-text {
      margin-top: var(--gap-logo-text)
    }

    /* Logo → Texto */
    .qr-text-tight {
      margin-top: var(--gap-title-text)
    }

    /* Nombre → Texto (sin logo) */

    /* MODO compacto: sin logo ni texto */
    .qr-compact .qr-header {
      margin-bottom: calc(var(--space) * 1.3)
    }

    .qr-compact {
      padding-bottom: calc(var(--pad) - 2mm) !important;
    }
  </style>

  <div class="qr-paper text-gray-900 {{ $compact ? 'qr-compact' : '' }}"
    style="
       width:{{ $w }};
       padding:{{ $padding }};
       --space:{{ $space }};
       --qr:{{ $qrSize }};
       --pad:{{ $padding }};
       --gap-qr-title: {{ $gapQrTitle }};
       --gap-title-logo: {{ $gapTitleLogo }};
       --gap-logo-text: {{ $gapLogoText }};
       --gap-title-text: {{ $gapTitleText }};
     ">

    {{-- Encabezado (logo app) --}}
    <div class="qr-header flex items-center justify-center gap-[1.2mm]">
      <img src="{{ asset('logos/asset-fy.png') }}" class="h-[5mm] object-contain" alt="Logo app">
    </div>

    <div class="grid justify-items-center" x-data="{ previewUrl: null }">
      {{-- QR --}}
      <div class="qr-box {{ $border ? 'border border-gray-300' : '' }}">
        @if($qrSrc)
        <img src="{{ $qrSrc }}" alt="QR" class="w-full h-full object-contain">
        @else
        <div class="w-full h-full grid place-items-center text-gray-400 text-[3mm]">QR</div>
        @endif
      </div>

      {{-- Nombre del bien --}}
      <div class="qr-title text-center text-[3mm] font-medium leading-tight">
        {{ $assetName }}
      </div>

      {{-- Logo empresa (opcional) --}}
      <template x-if="previewUrl">
        <img :src="previewUrl" alt="Logo empresa" class="qr-logo max-h-[10mm] object-contain">
      </template>
      @if($hasLogo)
      <img x-show="!previewUrl" x-cloak src="{{ $logoUrl }}" alt="Logo empresa" class="qr-logo max-h-[10mm] object-contain">
      @endif

      {{-- Texto (opcional) --}}
      @if($hasText)
      <p class="{{ $hasLogo ? 'qr-text' : 'qr-text-tight' }} text-center text-[2.6mm] leading-tight break-words"
        style="max-width: calc({{ $w }} - ({{ $padding }} * 2));
                display:-webkit-box; -webkit-line-clamp:5; -webkit-box-orient:vertical; overflow:hidden;">
        {{ $text }}
      </p>
      @endif
    </div>
  </div>