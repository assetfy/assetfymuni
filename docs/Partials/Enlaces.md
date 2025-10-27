**Documentación del componente Enlaces**

## Tabla de Contenidos
- [Descripción General](#descripción-general)
- [Propiedades](#propiedades)
- [Métodos](#métodos)
- [Vista del Componente](#vista-del-componente)

### Descripción General
El componente `Enlaces` es un componente de Livewire que centraliza la carga de otros subcomponentes dentro de la aplicación. Este componente se utiliza para renderizar diferentes subcomponentes relacionados con usuarios, permisos, roles, categorías, atributos, y más. De esta forma, se simplifica el proceso de manejo de cada uno de estos elementos, integrándolos en una única vista de fácil acceso.

### Propiedades
Este componente no posee propiedades definidas, ya que su único objetivo es la inclusión de otros subcomponentes dentro de la vista.

### Métodos
- **render()**: Este método se encarga de renderizar la vista `livewire.partials.enlaces`, la cual contiene todos los subcomponentes definidos en la vista.

### Vista del Componente
La vista asociada al componente `Enlaces` contiene una serie de subcomponentes que se cargan secuencialmente. A continuación, se presenta una lista con cada uno de los subcomponentes incluidos:

1. **Usuarios y Autorizaciones**
   - `usuarios.usuarios-servicios-autorizacion`
2. **Permisos y Roles**
   - `permisosRoles.editar-permisos`
   - `permisosRoles.editar-permisos-roles`
   - `permisos-roles.cargar-rutas`
   - `permisos-roles.crear-permisos`
   - `permisos-roles.crear-permiso-tipo-empresa`
   - `permisosRoles.crear-permisos-roles`
3. **Roles**
   - `roles.edit-asignaciones-roles`
   - `roles.editar-roles`
   - `roles.create-asignaciones-roles`
   - `roles.create-roles`
4. **Categorías y Subcategorías**
   - `categoria.edit-categoria`
   - `subcategoria.edit-subcategoria`
   - `subcategoria.atributossubcategorias.edit-atributos-subcategoria`
   - `subcategoria.atributossubcategorias.create-atributo-subcategoria`
   - `categoria.create-categoria`
   - `subcategoria.create-subcategoria`
5. **Atributos**
   - `atributos.editar-atributos`
   - `atributos.nuevosatributos.create-nuevo-atributos-activos`
   - `Atributos.create-atributos`
6. **Tipos**
   - `tipos.edit-tipos`
   - `tipos.create-tipos`
7. **Controles y Activos**
   - `controles.editar-controles`
   - `controles.controlessubcategorias.editar-controles-subcategoria`
   - `controles.activoscontroles.create-controles-activos`
   - `controles.controlessubcategorias.create-controles-subcategoria`
   - `controles.create-controles`
8. **Servicios**
   - `servicios.editar-servicios`
   - `servicios.activos.crear-solicitud-servicio`
   - `servicios.prestadora.prestadora-servicios-solicitudes-editar-estado`
   - `servicios.create-servicios`
   - `servicios.actividadeseconomicas.create-servicios-actividades-economicas`
   - `servicios.subcategorias.create-servicios-subcategorias`
9. **Ubicaciones**
   - `ubicaciones.crear-ubicaciones`
   - `ubicaciones.cambiar-ubicacion`
   - `ubicaciones.crear-tipos-ubicaciones`
10. **Actividades**
    - `actividad.cambiar-estado-actividad`
    - `actividad.cargar-reguladora`
    - `actividad.actividad-extra-autorizacion`
    - `actividad.actividad-nueva-actividad-empresa`
11. **Unidades**
    - `unidad.edit-unidad`
    - `unidad.create-unidad`
12. **Empresas**
    - `empresas.empresas-actividad-representante`
    - `empresas.EmpresasUsuarios.create-usuario`
13. **Altas**
    - `altas.editar-estados-altas`
    - `altas.create-estados-altas`
14. **General**
    - `general.edit-estado-general`
    - `general.create-estado-general`

Este componente permite centralizar la inclusión y manejo de todos los subcomponentes mencionados, mejorando la modularidad y el mantenimiento del código al trabajar con varios elementos interrelacionados en la aplicación.

