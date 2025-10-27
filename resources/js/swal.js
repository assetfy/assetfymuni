document.addEventListener('livewire:init', function () {
  if (typeof Swal === 'undefined' || typeof Livewire === 'undefined') {
    console.warn('SweetAlert2 o Livewire no están disponibles.');
    return;
  }

  /* ============================
   *  Tema corporativo (inline)
   * ============================ */
  var THEME = {
    bg: '#ffffff',
    bg2: '#f8fafc',    // slate-50
    fg: '#0f172a',     // slate-900
    muted: '#475569',  // slate-600
    border: 'rgba(2,6,23,.08)',
    shadow: '0 16px 40px rgba(2,6,23,.12)',
    primary: '#2563eb',
    primary500: '#3b82f6',
    secondary: '#64748b',
    success: '#16a34a',
    warning: '#d97706',
    error:   '#ef4444'
  };

  function styleToast(el) {
    if (!el) return;
    el.style.background = 'linear-gradient(180deg,' + THEME.bg + ' 0%,' + THEME.bg2 + ' 100%)';
    el.style.color = THEME.fg;
    el.style.border = '1px solid ' + THEME.border;
    el.style.boxShadow = THEME.shadow;
    el.style.borderRadius = '14px';
    el.style.padding = '10px 12px';
    el.style.width = '400px';
    el.style.backdropFilter = 'saturate(1.1)';
    // animación suave sin CSS
    el.style.transform = 'translate3d(20px,-8px,0)';
    el.style.opacity = '0';
    el.style.transition = 'transform .25s ease-out, opacity .25s ease-out';
    requestAnimationFrame(function () {
      el.style.transform = 'translate3d(0,0,0)';
      el.style.opacity = '1';
    });

    var title = el.querySelector('.swal2-title');
    if (title) {
      title.style.fontWeight = '600';
      title.style.color = THEME.fg;
    }
    var html = el.querySelector('.swal2-html-container');
    if (html) {
      html.style.color = THEME.muted;
      html.style.marginTop = '2px';
    }
    var close = el.querySelector('.swal2-close');
    if (close) {
      close.style.color = THEME.secondary;
    }
    var bar = el.querySelector('.swal2-timer-progress-bar');
    if (bar) {
      bar.style.background = 'rgba(2,6,23,.18)';
      bar.style.height = '3px';
    }
  }

  function styleButton(btn, opts) {
    if (!btn) return;
    btn.style.display = 'inline-flex';
    btn.style.alignItems = 'center';
    btn.style.gap = '.5rem';
    btn.style.padding = '.6rem 1rem';
    btn.style.borderRadius = '9999px';
    btn.style.border = '1px solid ' + (opts.border || 'transparent');
    btn.style.fontWeight = '600';
    btn.style.transition = 'all .18s ease-in-out';
    btn.style.background = opts.bg;
    btn.style.color = opts.fg;

    // hover (simulado con :hover JS → onmouseenter/onmouseleave)
    btn.addEventListener('mouseenter', function () {
      if (opts.bgHover) btn.style.background = opts.bgHover;
      if (opts.fgHover) btn.style.color = opts.fgHover;
      btn.style.transform = 'translateY(-1px)';
    });
    btn.addEventListener('mouseleave', function () {
      btn.style.background = opts.bg;
      btn.style.color = opts.fg;
      btn.style.transform = 'translateY(0)';
    });
  }

  function styleDialog(el) {
    if (!el) return;
    el.style.background = 'linear-gradient(180deg,' + THEME.bg + ' 0%,' + THEME.bg2 + ' 100%)';
    el.style.color = THEME.fg;
    el.style.border = '1px solid ' + THEME.border;
    el.style.boxShadow = THEME.shadow;
    el.style.borderRadius = '18px';
    el.style.padding = '20px 20px 16px';
    el.style.backdropFilter = 'blur(8px) saturate(1.2)';

    var title = el.querySelector('.swal2-title');
    if (title) {
      title.style.color = THEME.fg;
      title.style.fontWeight = '700';
    }
    var html = el.querySelector('.swal2-html-container');
    if (html) {
      html.style.color = THEME.muted;
    }

    // Botones
    var confirm = el.querySelector('.swal2-confirm');
    styleButton(confirm, {
      bg: THEME.primary, fg: '#fff', bgHover: THEME.primary500
    });

    var deny = el.querySelector('.swal2-deny');
    styleButton(deny, {
      bg: '#e2e8f0', fg: THEME.fg, border: '#cbd5e1', bgHover: '#e5e7eb'
    });

    var cancel = el.querySelector('.swal2-cancel');
    styleButton(cancel, {
      bg: 'transparent', fg: THEME.secondary, border: '#e5e7eb', bgHover: '#f1f5f9', fgHover: THEME.fg
    });

    var actions = el.querySelector('.swal2-actions');
    if (actions) {
      actions.style.display = 'flex';
      actions.style.gap = '8px';
      actions.style.justifyContent = 'end';
    }
  }

  // Mixin Toast + Dialog con estilos inline
  var Toast = Swal.mixin({
    toast: true,
    position: 'top-end',
    showConfirmButton: false,
    showCloseButton: true,
    timer: 3400,
    timerProgressBar: true,
    didOpen: function (t) {
      styleToast(t);
      t.addEventListener('mouseenter', Swal.stopTimer);
      t.addEventListener('mouseleave', Swal.resumeTimer);
    }
  });

  var Dialog = Swal.mixin({
    buttonsStyling: false,
    reverseButtons: true,
    showCancelButton: true,
    focusConfirm: false,
    backdrop: 'rgba(15,23,42,0.25)',
    didOpen: function (el) { styleDialog(el); }
  });

  // Atajos
  function ok(title, text){ Toast.fire({ icon: 'success', iconColor: THEME.success, title: title, text: text || '' }); }
  function info(title, text){ Toast.fire({ icon: 'info',    iconColor: THEME.primary, title: title, text: text || '' }); }
  function warn(title, text){ Toast.fire({ icon: 'warning', iconColor: THEME.warning, title: title, text: text || '' }); }
  function err(title, text){ Toast.fire({ icon: 'error',   iconColor: THEME.error,   title: title, text: text || '' }); }

  function payloadOf(data){ return (Array.isArray(data) && data.length) ? data[0] : (data || {}); }

  /* ============================
   *  Notificaciones (toasts)
   * ============================ */
  Livewire.on('lucky', function (message) { ok('¡Buen trabajo!', message); });

  Livewire.on('ubicacionCreacion', function (data) {
    var p = payloadOf(data);
    ok('¡Buen trabajo!', p.message ? p.message : 'Ubicación creada.');
  });

  Livewire.on('errorInfo', function (data) {
    var p = payloadOf(data);
    info(p.title ? p.title : 'Info', p.message ? p.message : '');
  });

  Livewire.on('Exito', function (data) {
    var p = payloadOf(data);
    ok(p.title ? p.title : 'Éxito', p.message ? p.message : 'Operación realizada.');
  });

  Livewire.on('errorCreacion', function (message) { err('Error al crear', message); });
  Livewire.on('errorServicio', function (message) { err('Error al actualizar el servicio', message); });
  Livewire.on('errorUbicacion', function (message) { err('Falta ubicación', message || 'Necesitás una ubicación asignada.'); });

  Livewire.on('warning', function (data) {
    var msg = (Array.isArray(data) && data.length && data[0].message) ? data[0].message : 'No se recibió mensaje';
    warn('Atención', msg);
  });

  Livewire.on('errorEmpresa', function (message) { err('Empresa ya registrada', message); });
  Livewire.on('confirmada', function (message) { ok('Solicitud aprobada', message); });
  Livewire.on('denegado', function (message) { err('Solicitud rechazada', message); });
  Livewire.on('errorEditar', function (message) { err('Error al editar', message); });
  Livewire.on('errorActividad', function (message) { err('Actividad económica faltante', message); });
  Livewire.on('eliminado', function () { info('Proveedor eliminado de favoritos'); });
  Livewire.on('ServicioEliminado', function () { info('Servicio cancelado'); });
  Livewire.on('report', function () { ok('Datos actualizados'); });

  Livewire.on('ordenTrabajo', function (data) {
    var p = payloadOf(data);
    ok(p.title ? p.title : 'Ok', p.message ? p.message : 'Orden generada');
  });

  Livewire.on('delegacionExitosa', function (data) {
    var p = payloadOf(data);
    Toast.fire({
      icon: 'success',
      iconColor: THEME.success,
      title: p.title ? p.title : 'Delegación exitosa',
      text: p.message ? p.message : '',
      timer: 2000
    }).then(function () { location.reload(); });
  });

  Livewire.on('no-permission', function (event) {
    err('Permiso denegado', event && event.message ? event.message : '');
  });

  Livewire.on('no-roles', function (event) {
    err('Usuario sin roles', event && event.message ? event.message : '');
  });

  // Descarga silenciosa + toast
  Livewire.on('download-template', function (data) {
    var p = payloadOf(data);
    if (!p.url) { err('Error de descarga', 'URL no provista.'); return; }
    fetch(p.url)
      .then(function (r) { return r.blob(); })
      .then(function (blob) {
        var blobUrl = window.URL.createObjectURL(blob);
        var link = document.createElement('a');
        link.style.display = 'none';
        link.href = blobUrl;
        link.download = p.filename || 'download.xlsx';
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
        window.URL.revokeObjectURL(blobUrl);
        ok('Descarga iniciada');
      })
      .catch(function () { err('Error de descarga', 'No se pudo descargar el archivo.'); });
  });

  /* ===================================================
   *  Confirmaciones / flujos con interacción
   * =================================================== */
  Livewire.on('check', function () {
    Dialog.fire({
      icon: 'question',
      title: '¿Quieres guardar los cambios?',
      showDenyButton: true,
      confirmButtonText: 'Guardar',
      denyButtonText: 'No guardar',
      cancelButtonText: 'Cancelar'
    }).then(function (result) {
      if (result.isConfirmed) {
        Livewire.dispatch('guardado');
      } else if (result.isDenied) {
        info('Cambios no hechos');
        Livewire.dispatch('cerrar');
      } else {
        Livewire.dispatch('cerrar');
      }
    });
  });

  Livewire.on('aprobar', function () {
    Dialog.fire({
      icon: 'question',
      title: '¿Desea aprobar esta solicitud?',
      showDenyButton: true,
      confirmButtonText: 'Sí',
      denyButtonText: 'No',
      cancelButtonText: 'Cancelar'
    }).then(function (result) {
      if (result.isConfirmed) {
        Livewire.dispatch('autorizar');
      } else if (result.isDenied) {
        info('Cambios no hechos');
        Livewire.dispatch('cerrar');
      } else {
        Livewire.dispatch('cerrar');
      }
    });
  });

  Livewire.on('checkCargarContrato', function (data) {
    var p = payloadOf(data);
    Dialog.fire({
      icon: 'question',
      title: '¿Desea cargar el contrato?',
      text: 'Si selecciona "Sí", se abrirá el modal para cargar el contrato. Si selecciona "No", se agregará el proveedor sin contrato.',
      showDenyButton: true,
      confirmButtonText: 'Sí, cargar contrato',
      denyButtonText: 'No, agregar proveedor',
      cancelButtonText: 'Cancelar'
    }).then(function (result) {
      if (result.isConfirmed) {
        Livewire.dispatch('openEditContrato', p);
      } else if (result.isDenied) {
        Livewire.dispatch('guardarFav', p);
      }
    });
  });

  // Nota: mantenemos los dos listeners 'rechazar' como en tu flujo original
  Livewire.on('rechazar', function () {
    Dialog.fire({
      icon: 'warning',
      title: '¿Desea Rechazar esta solicitud?',
      showDenyButton: true,
      confirmButtonText: 'Sí',
      denyButtonText: 'No',
      cancelButtonText: 'Cancelar'
    }).then(function (result) {
      if (result.isConfirmed) {
        Livewire.dispatch('noAutorizar');
      } else if (result.isDenied) {
        info('Cambios no hechos');
        Livewire.dispatch('cerrar');
      } else {
        Livewire.dispatch('cerrar');
      }
    });
  });

  Livewire.on('rechazar', function () {
    Dialog.fire({
      icon: 'warning',
      title: '¿Desea cancelar esta orden de trabajo?',
      showDenyButton: true,
      confirmButtonText: 'Sí',
      denyButtonText: 'No',
      cancelButtonText: 'Cerrar'
    }).then(function (result) {
      if (result.isConfirmed) {
        Livewire.dispatch('Rechazar');
      } else if (result.isDenied) {
        info('Cambios no hechos');
        Livewire.dispatch('cerrar');
      } else {
        Livewire.dispatch('cerrar');
      }
    });
  });

  Livewire.on('confirmar-eliminacion', function (arg) {
    var detail = (arg && arg.detail) ? arg.detail : arg;
    var title = (detail && detail.message) ? detail.message : '¿Eliminar?';
    Dialog.fire({
      icon: 'warning',
      title: title,
      showCancelButton: true,
      confirmButtonText: 'Sí, eliminar',
      cancelButtonText: 'No, conservar'
    }).then(function (result) {
      if (result.isConfirmed) {
        Livewire.dispatch('eliminarNivel', (detail && detail.id) ? detail.id : null);
      }
    });
  });

  Livewire.on('eliminarNivel', function (data) {
    var p = payloadOf(data);
    Dialog.fire({
      icon: 'warning',
      title: '¿Estás seguro que quieres eliminar este nivel?',
      showDenyButton: true,
      confirmButtonText: 'Sí, eliminar',
      denyButtonText: 'No conservar',
      cancelButtonText: 'Cancelar'
    }).then(function (result) {
      if (result.isConfirmed) {
        Livewire.dispatch('removerNivel', { id: p.id });
      } else {
        Livewire.dispatch('cerrar');
      }
    });
  });

  Livewire.on('cancelar', function () {
    Dialog.fire({
      icon: 'warning',
      title: '¿Desea cancelar el servicio ya pactado?',
      text: 'Esta acción no se puede deshacer.',
      showDenyButton: true,
      confirmButtonText: 'Sí, cancelar',
      denyButtonText: 'No',
      cancelButtonText: 'Cerrar'
    }).then(function (result) {
      if (result.isConfirmed) {
        Livewire.dispatch('save');
      } else if (result.isDenied) {
        info('El servicio sigue activo');
        Livewire.dispatch('cerrar');
      } else {
        Livewire.dispatch('cerrar');
      }
    });
  });

  Livewire.on('delegar', function (data) {
    var p = payloadOf(data);
    Dialog.fire({
      icon: 'question',
      title: '¿Estás seguro que quieres delegar estos bienes?',
      text: (p && p.message) ? p.message : '',
      showDenyButton: true,
      confirmButtonText: 'Sí',
      denyButtonText: 'No',
      cancelButtonText: 'Cerrar'
    }).then(function (result) {
      if (result.isConfirmed) {
        Livewire.dispatch('autorizar');
      } else if (result.isDenied) {
        info('Bienes no delegados');
        Livewire.dispatch('cerrar');
      } else {
        Livewire.dispatch('cerrar');
      }
    });
  });

  Livewire.on('eliminarDelegacion', function (data) {
    var p = payloadOf(data);
    Dialog.fire({
      icon: 'warning',
      title: '¿Estás seguro que quieres eliminar la delegación de este bien?',
      text: (p && p.message) ? p.message : '',
      showDenyButton: true,
      confirmButtonText: 'Sí',
      denyButtonText: 'No',
      cancelButtonText: 'Cerrar'
    }).then(function (result) {
      if (result.isConfirmed) {
        Livewire.dispatch('removerDelegacion', { id: p.id });
      } else if (result.isDenied) {
        info('Bien no eliminado de delegación');
        Livewire.dispatch('cerrar');
      } else {
        Livewire.dispatch('cerrar');
      }
    });
  });

  Livewire.on('mostrarConfirmacionDelegacion', function (data) {
    var p = payloadOf(data);
    Dialog.fire({
      icon: 'question',
      title: '¿Estás seguro que quieres aceptar la delegación de este bien?',
      text: (p && p.message) ? p.message : '',
      showDenyButton: true,
      confirmButtonText: 'Sí',
      denyButtonText: 'No',
      cancelButtonText: 'Cerrar'
    }).then(function (result) {
      if (result.isConfirmed) {
        Livewire.dispatch('admitirDelegacion', { id: p.id });
      } else if (result.isDenied) {
        info('Bien no aceptado');
        Livewire.dispatch('cerrar');
      } else {
        Livewire.dispatch('cerrar');
      }
    });
  });

  Livewire.on('terminarregistro', function () {
    Dialog.fire({
      icon: 'success',
      title: 'Usuario y Empresa creados con éxito',
      text: 'Por favor, valide su correo electrónico y vuelva a iniciar sesión.',
      showCancelButton: false,
      confirmButtonText: 'Ok'
    }).then(function (result) {
      if (result.isConfirmed) {
        Livewire.dispatch('dispatchSuccessAndClose');
      }
    });
  });

  Livewire.on('seleccionarSolicitud', function (data) {
    var p = payloadOf(data);
    Dialog.fire({
      icon: 'question',
      title: '¿Qué solicitud quieres realizar?',
      showDenyButton: true,
      confirmButtonText: 'Cotización',
      denyButtonText: 'Orden de trabajo',
      cancelButtonText: 'Cancelar'
    }).then(function (result) {
      if (result.isConfirmed) {
        Livewire.dispatch('solicitarServicios', { data: p.id });
      } else if (result.isDenied) {
        Livewire.dispatch('openSoliitarOrden', { data: p.id });
      }
    });
  });

});
