{{-- Dashboard con fondo blanco, cards mejoradas y mapa estable (Alpine + Livewire) --}}
<div id="container-unique" class="min-h-screen bg-white p-6">
    {{-- Header de bienvenida --}}
    <div id="header-welcome-unique" class="mb-6">
        <div>
            <h1>Hola, {{ Auth::user()->name ?? 'usuario' }} 👋</h1>
            <p class="text-sm">Tu panel general con accesos rápidos, métricas y ubicaciones.</p>
        </div>
        <div class="ml-auto flex gap-2">
            <a href="{{ route('empresa') }}" class="btn-ghost text-indigo-700 text-sm flex items-center gap-2">
                <i class="fa-solid fa-sliders"></i> Preferencias
            </a>
            <a href="{{ route('soporte.form') }}" class="btn-ghost text-slate-700 text-sm flex items-center gap-2">
                <i class="fa-solid fa-life-ring"></i> Soporte
            </a>
        </div>
    </div>

    {{-- Contadores / accesos (cards) --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4 items-stretch">
        @if (($tipoEmpresa ?? null) == 1 && (($apoderado ?? false) || ($rol ?? false) || ($manager ?? false)))
            <a href="{{ route('bienes-empresa') }}" class="block h-full" aria-label="Bienes Registrados">
                <div class="stat-card card-neo p-4 text-center h-full flex flex-col justify-between">
                    <div class="mx-auto mb-2 grid place-items-center w-12 h-12 ring-soft" style="--c:37,99,235">
                        <i class="fa-solid fa-warehouse text-blue-600 text-xl"></i>
                    </div>
                    <h2 class="text-sm font-medium text-slate-600">Bienes Registrados</h2>
                    @unless ($manager ?? false)
                        <p class="text-2xl font-extrabold tracking-tight text-blue-600">{{ $bienesCount ?? 0 }}</p>
                    @endunless
                </div>
            </a>

            @if (!($manager ?? false))
                <a href="{{ route('usuarios') }}" class="block h-full" aria-label="Usuarios Registrados">
                    <div class="stat-card card-neo p-4 text-center h-full flex flex-col justify-between">
                        <div class="mx-auto mb-2 grid place-items-center w-12 h-12 ring-soft" style="--c:16,185,129">
                            <i class="fa-solid fa-users text-emerald-600 text-xl"></i>
                        </div>
                        <h2 class="text-sm font-medium text-slate-600">Usuarios Registrados</h2>
                        <p class="text-2xl font-extrabold tracking-tight text-emerald-600">{{ $users ?? 0 }}</p>
                    </div>
                </a>
            @else
                <div class="stat-card card-neo p-4 text-center h-full flex flex-col justify-between">
                    <div class="mx-auto mb-2 grid place-items-center w-12 h-12 ring-soft" style="--c:99,102,241">
                        <i class="fa-solid fa-file-invoice-dollar text-indigo-600 text-xl"></i>
                    </div>
                    <h2 class="text-sm font-medium text-slate-600">Cotizaciones en Curso</h2>
                    <p class="text-xs text-slate-500">Acceso limitado por rol</p>
                </div>
            @endif

            <a href="{{ route('mis-ordenes-solicitadas') }}" class="block h-full" aria-label="Órdenes en curso">
                <div class="stat-card card-neo p-4 text-center h-full flex flex-col justify-between">
                    <div class="mx-auto mb-2 grid place-items-center w-12 h-12 ring-soft" style="--c:34,197,94">
                        <i class="fa-solid fa-briefcase text-green-600 text-xl"></i>
                    </div>
                    <h2 class="text-sm font-medium text-slate-600">Órdenes en curso</h2>
                    @unless ($manager ?? false)
                        <p class="text-2xl font-extrabold tracking-tight text-green-600">{{ $ordenesGestora ?? 0 }}</p>
                    @endunless
                </div>
            </a>

            <a href="{{ route('usuarios-servicios') }}" class="block h-full" aria-label="Servicios en Curso">
                <div class="stat-card card-neo p-4 text-center h-full flex flex-col justify-between">
                    <div class="mx-auto mb-2 grid place-items-center w-12 h-12 ring-soft" style="--c:239,68,68">
                        <i class="fa-solid fa-screwdriver-wrench text-rose-600 text-xl"></i>
                    </div>
                    <h2 class="text-sm font-medium text-slate-600">Servicios en Curso</h2>
                    @unless ($manager ?? false)
                        <p class="text-2xl font-extrabold tracking-tight text-rose-600">{{ $serviciosGestora ?? 0 }}</p>
                    @endunless
                </div>
            </a>
        @endif

        @if (($tipoEmpresa ?? null) != 1)
            @if (($apoderado ?? false) || ($rol ?? false))
                <a href="{{ route('cotizaciones-general') }}" class="block h-full" aria-label="Pedidos de Cotización">
                    <div class="stat-card card-neo p-4 text-center h-full flex flex-col justify-between">
                        <div class="mx-auto mb-2 grid place-items-center w-12 h-12 ring-soft" style="--c:34,197,94">
                            <i class="fa-solid fa-bag-shopping text-green-600 text-xl"></i>
                        </div>
                        <h2 class="text-sm font-medium text-slate-600">Pedidos de Cotización</h2>
                        <p class="text-2xl font-extrabold tracking-tight text-green-600">
                            {{ $ServiciosPendienteCotizacion ?? 0 }}</p>
                    </div>
                </a>

                <a href="{{ route('ordenes') }}" class="block h-full" aria-label="Órdenes de trabajo">
                    <div class="stat-card card-neo p-4 text-center h-full flex flex-col justify-between">
                        <div class="mx-auto mb-2 grid place-items-center w-12 h-12 ring-soft" style="--c:34,197,94">
                            <i class="fa-solid fa-clipboard-check text-green-600 text-xl"></i>
                        </div>
                        <h2 class="text-sm font-medium text-slate-600">Órdenes de trabajo</h2>
                        <p class="text-2xl font-extrabold tracking-tight text-green-600">{{ $ordenesTrabajo ?? 0 }}</p>
                    </div>
                </a>

                <a href="{{ route('ordenes') }}" class="block h-full" aria-label="Órdenes de Servicios">
                    <div class="stat-card card-neo p-4 text-center h-full flex flex-col justify-between">
                        <div class="mx-auto mb-2 grid place-items-center w-12 h-12 ring-soft" style="--c:147,51,234">
                            <i class="fa-solid fa-clipboard-list text-purple-600 text-xl"></i>
                        </div>
                        <h2 class="text-sm font-medium text-slate-600">Órdenes de Servicios</h2>
                        <p class="text-2xl font-extrabold tracking-tight text-purple-600">{{ $ordenesServicio ?? 0 }}
                        </p>
                    </div>
                </a>

                <a href="{{ route('usuarios') }}" class="block h-full" aria-label="Técnicos registrados">
                    <div class="stat-card card-neo p-4 text-center h-full flex flex-col justify-between">
                        <div class="mx-auto mb-2 grid place-items-center w-12 h-12 ring-soft" style="--c:147,51,234">
                            <i class="fa-solid fa-user-gear text-purple-600 text-xl"></i>
                        </div>
                        <h2 class="text-sm font-medium text-slate-600">Técnicos registrados</h2>
                        <p class="text-2xl font-extrabold tracking-tight text-purple-600">{{ $tecnicoCount ?? 0 }}</p>
                    </div>
                </a>

                @if (($tecnico ?? false) || ($tecnicos ?? false))
                    @unless ($tecnicos ?? false)
                        <a href="{{ route('cotizaciones-general') }}" class="block h-full"
                            aria-label="Cotizaciones Pendientes">
                            <div class="stat-card card-neo p-4 text-center h-full flex flex-col justify-between">
                                <div class="mx-auto mb-2 grid place-items-center w-12 h-12 ring-soft" style="--c:34,197,94">
                                    <i class="fa-solid fa-file-pen text-green-600 text-xl"></i>
                                </div>
                                <h2 class="text-sm font-medium text-slate-600">Cotizaciones Pendientes</h2>
                                <p class="text-xs text-slate-500">Revisá trabajos asignados</p>
                            </div>
                        </a>
                    @endunless

                    <a href="{{ route('ordenes') }}" class="block h-full" aria-label="Órdenes en Curso">
                        <div class="stat-card card-neo p-4 text-center h-full flex flex-col justify-between">
                            <div class="mx-auto mb-2 grid place-items-center w-12 h-12 ring-soft"
                                style="--c:147,51,234">
                                <i class="fa-solid fa-list-check text-purple-600 text-xl"></i>
                            </div>
                            <h2 class="text-sm font-medium text-slate-600">Órdenes en Curso</h2>
                            <p class="text-xs text-slate-500">Detalle en el módulo</p>
                        </div>
                    </a>

                    <a href="{{ route('cotizaciones-general') }}" class="block h-full"
                        aria-label="Servicios en Curso">
                        <div class="stat-card card-neo p-4 text-center h-full flex flex-col justify-between">
                            <div class="mx-auto mb-2 grid place-items-center w-12 h-12 ring-soft"
                                style="--c:239,68,68">
                                <i class="fa-solid fa-screwdriver-wrench text-rose-600 text-xl"></i>
                            </div>
                            <h2 class="text-sm font-medium text-slate-600">Servicios en Curso</h2>
                            <p class="text-xs text-slate-500">Seguimiento operativo</p>
                        </div>
                    </a>
                @endif
            @endif
        @endif

        {{-- Onboarding si falta actividad --}}
        @if (empty($empresa->COD_ACTIVIDAD ?? null))
            <a href="{{ route('empresa') }}" class="block h-full" aria-label="Completar configuración">
                <div
                    class="card-neo p-5 text-center h-full flex flex-col justify-center items-center gap-2 animate-breathe-blue">
                    <div class="mx-auto mb-1 grid place-items-center w-12 h-12 ring-soft" style="--c:79,70,229">
                        <i class="fa-solid fa-bolt text-indigo-600 text-xl"></i>
                    </div>
                    <h2 class="text-sm font-semibold text-slate-700">Completar configuración</h2>
                    <p class="text-xs text-indigo-700">Asigná tu actividad económica</p>
                </div>
            </a>
        @endif
    </div>

    {{-- Panel de Mapa --}}
    <div class="mt-6 flex flex-col md:flex-row gap-4">
        <div class="md:w-full card-neo p-6 border border-slate-200/60 rounded-xl" x-data="dashboardMap(@js($ubicacionesArray ?? []))"
            x-init="init()">

            <div class="mb-4 flex items-center gap-2">
                <div class="relative flex-1">
                    <input type="text" x-model="searchQuery"
                        placeholder="Buscar ubicación (ej. Depósito, Sucursal)..."
                        class="w-full px-4 py-2 pr-9 border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-400 focus:border-indigo-300 placeholder-slate-400" />
                    <i class="fa-solid fa-magnifying-glass absolute right-3 top-2.5 text-slate-400"></i>
                </div>
                <button type="button" @click="clearSearch()"
                    class="btn-ghost text-slate-700 text-sm flex items-center gap-2">
                    <i class="fa-solid fa-rotate-left"></i> Limpiar
                </button>
            </div>

            {{-- Muy importante: Livewire NO toca el DOM del mapa --}}
            <div x-ref="mapContainer" wire:ignore class="w-full h-96 rounded-xl shadow-inner overflow-hidden"></div>
        </div>
    </div>

    {{-- (Opcional) grilla de servicios usando tus estilos --}}
    @if (!empty($servicios ?? []))
        <div class="mt-8">
            <h3 class="text-slate-800 font-semibold mb-3">Servicios recientes</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach ($servicios as $s)
                    <div class="servicio-card">
                        @if (!empty($s['img']))
                            <img src="{{ $s['img'] }}" alt="Servicio">
                        @endif
                        <div class="info">
                            <div class="details">
                                <span class="name">{{ $s['nombre'] ?? 'Servicio' }}</span>
                                <span class="text-sm text-slate-500">{{ $s['desc'] ?? '—' }}</span>
                            </div>
                            <div class="bottom">
                                <div class="location-type">
                                    <i class="fa-solid fa-location-dot"></i>
                                    <span>{{ $s['lugar'] ?? 'Sin ubicación' }}</span>
                                    <span class="separator">·</span>
                                    <i class="fa-solid fa-tag"></i>
                                    <span>{{ $s['tipo'] ?? 'Tipo' }}</span>
                                </div>
                                <div class="actions">
                                    <button onclick="window.location.href='{{ $s['link'] ?? '#' }}'">Ver</button>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>

{{-- ====== Script Alpine: fuera del x-data para evitar errores de parsing ====== --}}
@once
    <script>
        // Recibe el array de ubicaciones desde Blade.
        function dashboardMap(ubicacionesInit) {
            return {
                map: null,
                markers: [],
                searchQuery: '',
                _interval: null,
                init() {
                    const start = () => {
                        const center = {
                            lat: -27.4799,
                            lng: -58.8361
                        };
                        // Crear mapa
                        this.map = new google.maps.Map(this.$refs.mapContainer, {
                            center,
                            zoom: 13,
                            mapTypeId: 'roadmap',
                            fullscreenControl: true,
                            streetViewControl: false,
                        });

                        // Marcas
                        const bounds = new google.maps.LatLngBounds();
                        (Array.isArray(ubicacionesInit) ? ubicacionesInit : [])
                        .forEach(ubi => {
                            if (!ubi || !ubi.lat || !ubi.lng) return;
                            const pos = {
                                lat: parseFloat(ubi.lat),
                                lng: parseFloat(ubi.lng)
                            };
                            const marker = new google.maps.Marker({
                                position: pos,
                                map: this.map
                            });
                            marker.myTitle = (ubi.tipo_nombre || '').toString();

                            const iw = new google.maps.InfoWindow({
                                content: '<div style="font-size:12px;line-height:1.25"><strong>' +
                                    marker.myTitle + '</strong><br>' + (ubi.direccion || '') + '</div>',
                                maxWidth: 160
                            });

                            marker.addListener('mouseover', () => iw.open(this.map, marker));
                            marker.addListener('mouseout', () => iw.close());

                            this.markers.push(marker);
                            bounds.extend(pos);
                        });
                        if (!bounds.isEmpty()) this.map.fitBounds(bounds);

                        // Watch de búsqueda
                        this.$watch('searchQuery', () => this.filter());
                    };

                    // Si google ya está, iniciamos; sino esperamos al <script id="google-maps-js">
                    if (window.google && window.google.maps) {
                        start();
                    } else {
                        const tag = document.getElementById('google-maps-js');
                        if (tag) {
                            tag.addEventListener('load', () => start(), {
                                once: true
                            });
                        } else {
                            // Fallback: poll suave si no existe el tag (no debería pasar si está en el layout)
                            this._interval = setInterval(() => {
                                if (window.google && window.google.maps) {
                                    clearInterval(this._interval);
                                    this._interval = null;
                                    start();
                                }
                            }, 120);
                        }
                    }

                    // Limpieza cuando Alpine destruya el componente
                    this.$cleanup && this.$cleanup(() => {
                        if (this._interval) {
                            clearInterval(this._interval);
                            this._interval = null;
                        }
                        this.markers.forEach(m => {
                            try {
                                m.setMap(null);
                            } catch (_) {}
                        });
                        this.markers = [];
                        this.map = null;
                    });
                },
                filter() {
                    const q = (this.searchQuery || '').toLowerCase().trim();
                    this.markers.forEach(m => m.setVisible(m.myTitle ? m.myTitle.toLowerCase().includes(q) : true));
                },
                clearSearch() {
                    this.searchQuery = '';
                    this.filter();
                }
            }
        }
    </script>
@endonce
