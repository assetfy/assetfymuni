Asunto: {{ $asunto }}

Descripción:
{{ $descripcion }}

---
Datos del solicitante
- Nombre: {{ $nombreUsuario }}
- Email: {{ $correoUsuario }}
- Empresa: {{ $empresa }}

@isset($adjuntos)
    Adjuntos:
    @foreach ((array) $adjuntos as $archivo)
        - {{ $archivo->getClientOriginalName() }}
    @endforeach
@endisset
