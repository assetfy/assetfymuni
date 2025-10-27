<?php

return [
    // Enlaces directos (se muestran si el usuario tiene el permiso)
    'directos' => [
        [
            'order'         => 1,
            'ruta'          => 'dashboard-empresa',
            'label'         => 'Inicio',
            'icon'          => 'fa-solid fa-house',
            'permiso'       => '',
            'company_types' => [1, 2],
        ],
        // [
        //     'order'         => 1,
        //     'ruta'          => 'dashboard-empresa',
        //     'label'         => 'Inicio',
        //     'icon'          => 'fa-solid fa-house',
        //     'permiso'       => '',
        //     'company_types' => [1],
        // ],
        [
            'order'         => 53,
            'ruta'          => 'agenda',
            'label'         => 'Agenda',
            'icon'          => 'fa-solid fa-calendar-alt',
            'permiso'       => '',
            'company_types' => [2],
        ],
        [
            'order'         => 52,
            'ruta'          => 'reportes-prestadora',
            'label'         => 'Reportes',
            'icon'          => 'fa-solid fa-chart-line',
            'permiso'       => 'reportes',
            'company_types' => [2],
        ],

        [
            'order'         => 52,
            'ruta'          => 'reportes',
            'label'         => 'Reportes',
            'icon'          => 'fa-solid fa-chart-line',
            'permiso'       => 'reportes',
            'company_types' => [1],
        ],
        [
            'order'         => 29,
            'ruta'          => 'vista-proveedores',
            'label'         => 'Proveedores',
            'icon'          => 'fa-solid fa-star',
            'permiso'       => 'mis-provedores-favoritos',
            'company_types' => [1],
        ],
        [
            'order'         => 3,
            'ruta'          => 'bienes-empresa',
            'label'         => 'Mis bienes',
            'icon'          => 'fa-solid fa-box-open',
            'permiso'       => '/bienes-empresa',
            'company_types' => [1],
        ],
    ],

    // Submenús: cada submenú tiene un título, icono, order y un array de items.
    'submenus' => [
        // Submenú "Mis solicitudes"
        [
            'order'         => 3,
            'titulo'        => [
                'default' => 'Mis solicitudes',
            ],
            'icon'          => 'fa-solid fa-comments',
            'company_types' => [1],
            'items'         => [
                [
                    'ruta'          => 'cotizaciones-general',
                    'label'         => [
                        'default' => 'Cotizaciones',
                    ],
                    'icon'          => 'fa-solid fa-users',
                    'permiso'       => '',
                    'company_types' => [1],
                ],
                [
                    'ruta'          => 'ordenes',
                    'label'         => [
                        'default' => ' Órdenes a Proveedores',
                    ],
                    'icon'          => 'fa-solid fa-file-invoice',
                    'permiso'       => '',
                    'company_types' => [1],
                ],
                [
                    'ruta'          => 'contratos-empresas',
                    'label'         => [
                        'default' => 'Contratos',
                    ],
                    'icon'          => 'fa-solid fa-file-invoice',
                    'permiso'       => '',
                    'company_types' => [1],
                ],
                [
                    'ruta'          => 'calificacion-general',
                    'label'         => [
                        'default' => 'Historial Servicios',
                    ],
                    'icon'          => 'fa-solid fa-history',
                    'permiso'       => '',
                    'company_types' => [1],
                ],
                [
                    'ruta'          => 'usuarios-servicios',
                    'label'         => [
                        'default' => 'Servicios pactados',
                    ],
                    'icon'          => 'fa-solid fa-wrench',
                    'permiso'       => '',
                    'company_types' => [4],
                ]
            ],
        ],
        // Submenú de Clientes (primera versión)
        [
            'order'         => 2,
            'titulo'        => 'Clientes',
            'icon'          => 'fa-solid fa-user-circle',
            'company_types' => [2],
            'items'         => [
                [
                    'ruta'  => 'cotizaciones-general',
                    'label' => [
                        'default' => 'Solicitudes',
                    ],
                    'icon'  => 'fa-solid fa-file-alt',
                    'permiso' => '',
                    'company_types' => [2],
                ],
                [
                    'ruta'  => 'ordenes',
                    'label' => [
                        'default' => 'Órdenes de trabajo',
                    ],
                    'icon'  => 'fa-solid fa-tasks',
                    'permiso' => '',
                    'company_types' => [2],
                ],
                [
                    'ruta'  => 'bienes-clientes',
                    'label' => [
                        'default' => 'Bienes de Clientes',
                    ],
                    'icon'  => 'fa-solid fa-archive',
                    'permiso' => 'bienes/bienes-clientes',
                    'company_types' => [2],
                ],
                [
                    'ruta'  => 'vista-clientes',
                    'label' => [
                        'default' => 'Mis Clientes',
                    ],
                    'icon'  => 'fa-solid fa-user-tie',
                    'permiso' => 'empresas/clientes',
                    'company_types' => [2],
                ],
            ],
        ],
        // Submenú de Parametrizaciones con submenu anidado de Permisos y Roles
        [
            'order'         => 29,
            'titulo'        => 'Parametrizaciones',
            'icon' => 'fa-solid fa-gear',
            'company_types' => [2, 1],
            'items'         => [
                [
                    'ruta'  => 'grupos-empresa',
                    'label' => [
                        'default' => 'Grupos',
                    ],
                    'icon'  => 'fa-solid fa-object-group',
                    'permiso' => 'grupos/grupos-empresa',
                    'company_types' => [],
                ],
                [
                    'ruta'  => 'importaciones',
                    'label' => [
                        'default' => 'Importación',
                    ],
                    'icon'  => 'fa-solid fa-file-import',
                    'permiso' => 'importaciones',
                    'company_types' => [1, 2],
                ],
                [
                    'ruta'  => 'exportaciones',
                    'label' => [
                        'default' => 'Exportación',
                    ],
                    'icon'  => 'fa-solid fa-file-export',
                    'permiso' => 'exportaciones',
                    'company_types' => [1, 2],
                ],
                [
                    'ruta'  => 'usuarios',
                    'label' => [
                        'default' => 'Usuarios',
                    ],
                    'icon'  => 'fa-solid fa-user-alt',
                    'permiso' => 'empresas/usuarios-empresa',
                    'company_types' => [1, 2],
                ],
                [
                    'ruta'  => 'empresa',
                    'label' => [
                        'default' => 'Información Empresa',
                    ],
                    'icon'  => 'fa-solid fa-university',
                    'permiso' => 'empresas/perfil',
                    'company_types' => [1, 2],
                ],
                [
                    'ruta'  => 'qr-configuracion',
                    'label' => [
                        'default' => 'Configuración QR',
                    ],
                    'icon'  => 'fa-solid fa-qrcode',
                    'permiso' => '',
                    'company_types' => [1, 2],
                ],
                [
                    'ruta'  => 'configuracion-general',
                    'label' => [
                        'default' => 'Configuración General',
                    ],
                    'icon'  => 'fa-solid fa-sliders',
                    'permiso' => '',
                    'company_types' => [1, 2],
                ],
                [
                    'ruta'          => 'ubicaciones-general',
                    'label'         => 'Ubicaciones',
                    'icon'          => 'fa-solid fa-map-marker-alt',
                    'permiso'       => 'Ubicaciones\Ubicaciones',
                    'company_types' => [1],
                ],
                [
                    'ruta'  => 'ubicaciones-general',
                    'label' => [
                        'default' => 'Ubicaciones de Clientes',
                    ],
                    'icon'  => 'fa-solid fa-map-marker',
                    'permiso' => 'ubicaciones/ubicaciones-delegadas',
                    'company_types' => [2],
                ],
                [
                    'ruta'  => 'empresas-organigrama',
                    'label' => [
                        'default' => 'Organizacion de Empresa',
                    ],
                    'icon' => 'fa-solid fa-sitemap',
                    'permiso' => '/empresas/empresas-organizacion',
                    'company_types' => [1, 2],
                ],
                [
                    'ruta'  => 'auditoria',
                    'label' => [
                        'default' => 'auditoria de Empresa',
                    ],
                    'icon' => 'fa-solid fa-clipboard-list',
                    'permiso' => '',
                    'company_types' => [1, 2],
                ],
                // Submenu anidado de Permisos y Roles
                [
                    'order'         => 10,
                    'titulo'        => 'Permisos y Licencias',
                    'icon'          => 'fa-solid fa-lock',
                    'company_types' => [1, 2],
                    'items'         => [
                        [
                            'ruta'  => 'roles',
                            'label' => [
                                'default'         => 'Licencias',
                                'Proveedor/Tecnico' => 'Licencias Personalizadas',
                                'Proveedor/Admin'   => 'Licencias Personalizadas'
                            ],
                            'icon'  => 'fa-solid fa-id-badge',
                            'permiso' => 'roles/roles',
                            'company_types' => [1, 2],
                        ],
                        [
                            'ruta'  => 'asignaciones-roles',
                            'label' => [
                                'default'         => 'Asignación de Licencias',
                                'Proveedor/Admin' => ''
                            ],
                            'icon'  => 'fa-solid fa-user-check',
                            'permiso' => 'roles/asignaciones-roles',
                            'company_types' => [1, 2],
                        ],
                        [
                            'ruta'  => 'permisos-roles',
                            'label' => [
                                'default' => 'Asignación de permisos a Licencias',
                            ],
                            'icon'  => 'fa-solid fa-shield-alt',
                            'permiso' => 'permisosRoles/permisos-roles',
                            'company_types' => [1, 2],
                        ],
                        [
                            'ruta'  => 'rutas',
                            'label' => [
                                'default' => 'Rutas del Sistema',
                            ],
                            'icon'  => 'fa-solid fa-road',
                            'permiso' => 'permisosRoles/rutas',
                            'company_types' => [1, 2],
                        ],
                        [
                            'ruta'  => 'permisos',
                            'label' => [
                                'default' => 'Permisos',
                            ],
                            'icon'  => 'fa-solid fa-fingerprint',
                            'permiso' => 'permisosRoles/permisos',
                            'company_types' => [1, 2],
                        ],
                        [
                            'ruta'  => 'permisos-por-tipo',
                            'label' => [
                                'default' => 'Permisos Rutas',
                            ],
                            'icon'  => 'fa-solid fa-sliders-h',
                            'permiso' => 'permisosRoles/permisos-por-tipo',
                            'company_types' => [1, 2],
                        ],
                    ],
                ],
            ],
        ],
    ],
];
