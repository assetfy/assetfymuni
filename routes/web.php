<?php

use App\Livewire\Atributos\NuevosAtributos\CreateNuevoAtributosActivos;
use App\Livewire\Controles\ControlesVistaDetalle;
use App\Livewire\Activos\ActivosVistaDetalle;
use App\Http\Controllers\SessionController;
use App\Livewire\Activos\DashboardActivos;
use App\Http\Controllers\AfipController;
use Illuminate\Support\Facades\Route;
use App\Livewire\Activos\Card;

use App\Livewire\ActivosDatos;
use App\Http\Controllers\SoporteController;
use App\Livewire\Servicios\OrdenesDeTrabajo\CerrarOrden;

// Mostrar el formulario, solo para usuarios autenticados
Route::middleware('auth')->get('/soporte', function () {
    return view('soporte');
})->name('soporte.form');

// Procesar el envío, solo para usuarios autenticados
Route::middleware('auth')->post('/soporte', [SoporteController::class, 'store'])
    ->name('soporte.store');
// Rutas de sesiónn
Route::get('/email/verify', function () {
    return view('auth.verify-email');
})->name('verification.notice');
Route::get('email/verify/{id}/{hash}', [SessionController::class, 'verify'])->middleware(['signed'])->name('verification.verify');
Route::post('/update-session', [SessionController::class, 'updateSession'])->name('update.session');
Route::get('/get-session-values', [SessionController::class, 'getSessionValues'])->name('get.session.values');
Route::get('/datos-activos/{id}', ActivosDatos::class)->name('datos.activos');
// Rutas generales

// Ruta para la creación de empresas sin protección

Route::get('/empresas/create-empresas/', function () {
    return view('create-empresas');
})->name('create-empresas');


Route::get('/', function () {
    return view('landing');
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'check.user.estado' => \App\Http\Middleware\CheckUserEstado::class,
    'verified',
])->group(function () {
    Route::get('/usuarios/perfiles', function () {
        return view('perfiles');
    })->name('usuarios-perfiles');
});

// Rutas con middleware de autenticación y verificación de estado del usuario
Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'check.user.estado' => \App\Http\Middleware\CheckUserEstado::class,
    'verified',
])->group(function () {
    Route::get('/ubicaciones/vistas-aperturas', function () {
        return view('vistas-aperturas');
    })->name('vistas-aperturas');
});

// Rutas con middleware de autenticación y verificación de permisos
//User comun
Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'check.user.estado' => \App\Http\Middleware\CheckUserEstado::class,
    'verified',
    // 'check.permisos.roles',  // Aquí aplicamos el middleware
])->group(function () {
    Route::get('/usuarios/dashboard-usuario', function () {
        return view('dashboard-usuario');
    })->name('dashboard-usuario');
    Route::get('/activos/dashboard-activos', function () {
        return view('dashboard-activos');
    })->name('dashboard-activos');
    Route::get('/empresa/estado', function () {
        return view('estado');
    })->name('estado');
    Route::get('/servicios/servicios-realizados/{id_activo}', function ($id_activo) {
        return view('activos-servicios', compact('id_activo'));
    })->name('activos-servicios');
    Route::get('/servicios/solicitud-servicios/{id_activo}', function ($id_activo) {
        return view('servicios-activos', compact('id_activo'));
    })->name('servicios-activos');
    Route::get('/ubicaciones/auditoria-activos/{id_activo}', function ($id_activo) {
        return view('auditoria-activos', compact('id_activo'));
    })->name('auditoria-activos');
    Route::get('/servicios-vista-formulario', function () {
        return view('servicios-vista-formulario');
    })->name('servicios-vista-formulario');
    Route::get('/afip/{cuit}', [AfipController::class, 'verificarAfip'])->name('afip');
    Route::get('/controles/control-detalle', function () {
        return view('control-detalle');
    })->name('control-detalle');
    Route::get('/dashboard_servicios', function () {
        return view('dashboard_servicios');
    })->name('dashboard_servicios');
    Route::get('/controles/controles-vista-detalle/{id_control}', [ControlesVistaDetalle::class, 'setIdActivo'])->name('controles-vista-detalle');
    Route::get('/controles/activos-controles', function () {
        return view('activos-controles');
    })->name('activos-controles');
    Route::get('/menus/activos-menu', function () {
        return view('activos-menu');
    })->name('activos-menu');
    Route::get('/menus/tablas', function () {
        return view('tablas');
    })->name('tablas');
    Route::get('/activos/card/{id_tipo}', function ($id_tipo) {
        return view('card', compact('id_tipo'));
    })->name('card');
    Route::get('/ubicaciones/ubicaciones-dashboard/{id_ubicacion}', function ($id_ubicacion) {
        return view('ubicaciones-dashboard', compact('id_ubicacion'));
    })->name('ubicaciones-dashboard');
    Route::get('/usuarios/notificaciones', function () {
        return view('notificaciones');
    })->name('notificaciones');
    Route::get('/activos/{id_activo}', [ActivosVistaDetalle::class, 'render'])->name('activos-vista-detalle');
    Route::get('/atributos/activos-atributos/{id_activo}', function ($id_activo) {
        return view('activos-atributos', compact('id_activo'));
    })->name('activos-atributos');
    Route::get('/usuarios/sidebar-usuario', function () {
        return view('sidebar-usuario');
    })->name('sidebar-usuario');
    Route::get('/download', [Card::class, 'download'])->name('download');
    Route::get('/ubicaciones/ubicaciones', function () {
        return view('ubicaciones-general');
    })->name('ubicaciones-general');
    Route::get('/calificacion-general', function () {
        return view('calificacion-general');
    })->name('calificacion-general');
    Route::get('/menus/tablas', function () {
        return view('tablas');
    })->name('tablas');
    Route::get('/reportes', function () {
        return view('reportes');
    })->name('reportes');
    Route::get('mis-provedores-favoritos', function () {
        return view('mis-provedores-favoritos');
    })->name('mis-provedores-favoritos');
    Route::get('proveedores', function () {
        return view('proveedores');
    })->name('proveedores');
    Route::get('usuarios/agenda', function () {
        return view('agenda');
    })->name('agenda');
    Route::get('/cotizaciones', function () {
        return view('cotizaciones-general');
    })->name('cotizaciones-general');
    Route::get('/organigrama', function () {
        return view('organigrama');
    })->name('organigrama');
    Route::get('/empresas-organigrama', function () {
        return view('empresas-organigrama');
    })->name('empresas-organigrama');
});

// ADMINISTRADOR
Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'check.user.estado' => \App\Http\Middleware\CheckUserEstado::class,
    'verified',
    //'check.user.tipo' => \App\Http\Middleware\CheckUserTipo::class,
    'check.permisos.roles',  // Aplicamos el middleware para verificar permisos
])->group(function () {
    Route::get('/atributos/create-nuevo-atributos-activos/{id_activo}', [CreateNuevoAtributosActivos::class, 'render'])->name('create-nuevo-atributos-activos');
    Route::get('/usuarios/tipos-usuarios', function () {
        return view('tipos-usuarios');
    })->name('tipos-usuarios');
    Route::get('/controles/controles-subcategoria', function () {
        return view('controles-subcategoria');
    })->name('control-subcategoria');
    Route::get('/subcategoria/atributos-subcategoria', function () {
        return view('atributos-subcategoria');
    })->name('atributos-subcategoria');
    Route::get('/controles', function () {
        return view('controles');
    })->name('controles');
    Route::get('/estados-altas', function () {
        return view('estados-altas');
    })->name('estados-altas');
    Route::get('/tipos_campos', function () {
        return view('tipos_campos');
    })->name('tipos_campos');
    Route::get('/atributos', function () {
        return view('atributos');
    })->name('atributos');
    Route::get('/estados', function () {
        return view('estado-general');
    })->name('estado-gral');
    Route::get('/subcategoria', function () {
        return view('subcategoria');
    })->name('subcategoria');
    Route::get('/categoria', function () {
        return view('categoria');
    })->name('categoria');
    Route::get('/unidad_medida', function () {
        return view('unidad_medida');
    })->name('unidad_medida');
    Route::get('/tipos', function () {
        return view('tipos');
    })->name('tipos');
    Route::get('/ubicaciones/tipos-ubicaciones', function () {
        return view('tipos-ubicaciones');
    })->name('tipos-ubicaciones');
    Route::get('/actividad/estado-actividad', function () {
        return view('estado-actividad');
    })->name('estado-actividad');
    Route::get('/empresas-vista', function () {
        return view('empresas-vista');
    })->name('empresas-vista');
    Route::get('/servicios.servicios', function () {
        return view('servicios');
    })->name('servicios');
    Route::get('/servicios-subcategorias', function () {
        return view('servicios-subcategorias');
    })->name('servicios-subcategorias');
    Route::get('/servicios-actividades-economicas', function () {
        return view('servicios-actividades-economicas');
    })->name('servicios-actividades-economicas');
    Route::get('/usuarios/usuarios-servicios', function () {
        return view('usuarios-servicios');
    })->name('usuarios-servicios');
    Route::get('/cotizaciones', function () {
        return view('cotizaciones-general');
    })->name('cotizaciones-general');
    Route::get('/actividad/actividad-empresas', function () {
        return view('actividad-empresas');
    })->name('actividad-empresas');
    Route::get('/atributos/activos-atributos/{id_activo}', function ($id_activo) {
        return view('activos-atributos', compact('id_activo'));
    })->name('activos-atributos');
    Route::get('/permisosRoles/permisos', function () {
        return view('permisos');
    })->name('permisos');
    Route::get('/permisosRoles/permisos-roles', function () {
        return view('permisos-roles');
    })->name('permisos-roles');
    Route::get('/roles/roles', function () {
        return view('roles');
    })->name('roles');
    Route::get('/roles/asignaciones-roles', function () {
        return view('asignaciones-roles');
    })->name('asignaciones-roles');
    Route::get('/permisosRoles/rutas', function () {
        return view('rutas');
    })->name('rutas');
    Route::get('/permisosRoles/permisos-por-tipo', function () {
        return view('permisos-por-tipo');
    })->name('permisos-por-tipo');
    // Route::get('/ubicaciones/ubicaciones-delegadas', function () {
    //     return view('ubicaciones-delegadas');
    // })->name('ubicaciones-delegadas');
    Route::get('/permisosRoles/config-permiso-rutas', function () {
        return view('config-permiso-rutas');
    })->name('config-permiso-rutas');
    Route::get('configuracion_qr', function () {
        return view('qr-configuracion');
    })->name('qr-configuracion');
});

// EMPRESA
Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'check.user.estado' => \App\Http\Middleware\CheckUserEstado::class,
    'verified',
    'check.tipo.empresa' => \App\Http\Middleware\CheckTipoEmpresa::class,

])->group(function () {
    Route::get('/empresas/dashboard-empresa', function () {
        return view('dashboard-empresa');
    })->name('dashboard-empresa');
    Route::get('/empresas/autorizacion', function () {
        return view('autorizacion');
    })->name('autorizacion');
    Route::get('/empresas/autorizacion-actividad', function () {
        return view('autorizacion-actividad');
    })->name('autorizacion-actividad');
    Route::get('/actividad/actividades-extra', function () {
        return view('actividades-extra');
    })->name('actividades-extra');
    Route::get('/actividad/actividad-empresa-vista', function () {
        return view('actividad-empresa-vista');
    })->name('actividad-empresa-vista');
    Route::get('/prestadora-solicitud-servicios', function () {
        return view('prestadora-solicitud-servicios');
    })->name('prestadora-solicitud-servicios');
    Route::get('/servicios-activos-pendientes', function () {
        return view('servicios-activos-pendientes');
    })->name('servicios-activos-pendientes');
    Route::get('/servicios-realizar-servicios/{servicio}', function ($servicio) {
        return view('servicios-realizar-servicios', compact('servicio'));
    })->name('servicios-realizar-servicios');
    Route::get('/empresas/sidebar-empresa', function () {
        return view('sidebar-empresa');
    })->name('sidebar-empresa');
    Route::get('/empresas/dashboard-prestadora', function () {
        return view('dashboard-prestadora');
    })->name('dashboard-prestadora');
    Route::get('/empresas/reportes-prestadora', function () {
        return view('reportes-prestadora');
    })->name('reportes-prestadora');
    Route::get('importaciones', function () {
        return view('importaciones');
    })->name('importaciones');
    Route::get('exportaciones', function () {
        return view('exportaciones');
    })->name('exportaciones');
    Route::get('/empresas/clientes', function () {
        return view('clientes');
    })->name('clientes');
    Route::get('/ubicaciones/ubicaciones-delegadas', function () {
        return view('ubicaciones-delegadas');
    })->name('ubicaciones-delegadas');
    Route::get('/bienes/bienes-clientes', function () {
        return view('bienes-terceros');
    })->name('bienes-clientes');
    Route::get('/servicios/ordenes-de-trabajos', function () {
        return view('ordenes-de-trabajo');
    })->name('ordenes-de-trabajo');
    Route::get('/servicios/mis-ordenes', function () {
        return view('mis-ordenes');
    })->name('mis-ordenes');
    // Route::get('/servicios.ordenes-de-trabajo/ordenesdetrabajo', function () {
    //     return view('ordenes-de-trabajo');
    // })->name('ordenes-de-trabajo');
    Route::get('/delegar-bienes', function () {
        return view('delegar-bienes');
    })->name('delegar-bienes');
    Route::get('/bienes-empresa', function () {
        return view('bienes-empresa');
    })->name('bienes-empresa');
    Route::get('/empresas/buscar-clientes', function () {
        return view('buscar-clientes');
    })->name('buscar-clientes');
    Route::get('/grupos/grupos-empresa', function () {
        return view('grupos-empresa');
    })->name('grupos-empresa');
    Route::get('/servicios/vista-proveedores', function () {
        return view('vista-proveedores');
    })->name('vista-proveedores');
    Route::get('/servicios/ordenes-generadas', function () {
        return view('ordenes-generadas');
    })->name('ordenes-generadas');
    Route::get('/servicios/ordenes', function () {
        return view('ordenes');
    })->name('ordenes');
    Route::get('/servicios/ordenes-sin-asignar', function () {
        return view('ordenes-sin-asignar');
    })->name('ordenes-sin-asignar');
    Route::get('/servicios/ordenes-proveedores-fav', function () {
        return view('ordenes-proveedores');
    })->name('ordenes-proveedores');
    Route::get('/servicios/mis-ordenes-solicitadas', function () {
        return view('mis-ordenes-solicitadas');
    })->name('mis-ordenes-solicitadas');
    Route::get('/servicios/ordenes-clientes', function () {
        return view('ordenes-clientes');
    })->name('ordenes-clientes');
    Route::get('/empresas/vista-clientes', function () {
        return view('vista-clientes');
    })->name('vista-clientes');
    Route::get('/empresas/auditoria', function () {
        return view('auditoria');
    })->name('auditoria');
    Route::get('/configuracion-general', function () {
        return view('configuracion-general');
    })->name('configuracion-general');
    Route::get('/servicios/ordenes/{id}/cerrar', function (int $id) {
        return view('cerrar-orden', ['id' => $id]);
    });
});

// ADMIN
Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'check.user.estado' => \App\Http\Middleware\CheckUserEstado::class,
    'verified',
    'check.tipo.empresa' => \App\Http\Middleware\CheckTipoEmpresa::class,

])->group(function () {
    Route::get('/empresas/empresas-tipo-user', function () {
        return view('empresas-tipo-user');
    })->name('empresas-tipo-user');
    Route::get('/empresas/usuarios-empresas', function () {
        return view('usuarios');
    })->name('usuarios');
    Route::get('/permisosRoles/permisos-roles', function () {
        return view('permisos-roles');
    })->name('permisos-roles');
    Route::get('/roles/roles', function () {
        return view('roles');
    })->name('roles');
    Route::get('/roles/asignaciones-roles', function () {
        return view('asignaciones-roles');
    })->name('asignaciones-roles');
    Route::get('/empresas/perfil', function () {
        return view('empresa');
    })->name('empresa');
    Route::get('/empresas/empresas-organizacion', function () {
        return view('empresas-organizacion');
    })->name('empresas-organizacion');
    Route::get('/empresas/contratos/contratos-empresas', function () {
        return view('contratos-empresas');
    })->name('contratos-empresas');
    Route::get('/empresas/contratos/formulario-contratos', function () {
        return view('formulario-contratos');
    })->name('formulario-contratos');
});

// ESTADO
Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
    'check.permisos.roles',  // Aplicamos el middleware para verificar permisos
])->group(function () {
    Route::get('/estado/dashboard-estado', function () {
        return view('dashboard-estado');
    })->name('dashboard-estado');
    Route::get('/estado/vista-solicitud-alta/{solicitud}', function ($solicitud) {
        return view('vista-solicitud-alta', compact('solicitud'));
    })->name('vista-solicitud-alta');
    Route::get('/estado/servicios-prestadora-tabla', function () {
        return view('servicios-prestadora-tabla');
    })->name('servicios-prestadora-tabla');
    Route::get('/estado/tabla-estado', function () {
        return view('tabla-estado');
    })->name('tabla-estado');
});
