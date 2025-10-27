<div>
    @if($controles)
    <x-danger-button wire:click="$set('open',true)"> Ver foto </x-danger-button>
    <x-dialog-modal wire:model.live="open">
        <x-slot name="title">
            Fotos cargadas
        </x-slot>
        <x-slot name="content">
        <div id="carouselExample{{ $controles->id_controlesactivos }}" class="carousel slide">
            <div class="carousel-inner">
                    @for ($i = 1; $i <= 5; $i++)
                        @if (isset($controles["foto{$i}"]))
                            <div class="carousel-item @if($i === 1) active @endif">
                                <img src="{{ asset(Storage::url($controles["foto{$i}"])) }}" alt="Imagen" class="d-block w-100">
                            </div>
                        @endif
                    @endfor
                </div>
                <button class="carousel-control-prev" type="button" data-bs-target="#carouselExample{{ $controles->id_controlesactivos }}" data-bs-slide="prev">
                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Previous</span>
                </button>
                <button class="carousel-control-next" type="button" data-bs-target="#carouselExample{{ $controles->id_controlesactivos }}" data-bs-slide="next">
                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Next</span>
                </button>
            </div>
        </div>
        </x-slot>
        <x-slot name="footer">
            <x-secondary-button class="mr-2" wire:click="$set('open',false)">
                Cerrar
            </x-secondary-button>
        </x-slot>
    </x-dialog-modal>
    @endif
</div>