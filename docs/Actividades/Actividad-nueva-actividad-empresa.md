# Documentación del Componente Livewire: ActividadNuevaActividadEmpresa

## Tabla de Contenidos
- [Descripción General](#descripción-general)
- [Propiedades](#propiedades)
- [Métodos](#métodos)
- [Eventos Livewire](#eventos-livewire)
- [Vista y Controlador](#vista-y-controlador)
- [Dependencias](#dependencias)

## Descripción General
El componente `ActividadNuevaActividadEmpresa` permite registrar una nueva actividad económica para una empresa utilizando la información verificada por AFIP. Actualmente, el componente está en funcionamiento, pero aún no se ha definido completamente cómo se implementará en el flujo general de la aplicación.

## Propiedades

- **actividad**: Almacena las actividades económicas disponibles para la empresa.
- **cuit**: CUIT de la empresa a la que se le asignará la nueva actividad.
- **cod_actividad**: Código de la actividad seleccionada.
- **provincia**: Provincia de la empresa.
- **localidad**: Localidad de la empresa.
- **Afip**: Almacena los datos obtenidos del servicio de verificación de AFIP.
- **codActividad**: Lista de códigos de actividades obtenidas de AFIP.
- **datos**: Identificador del usuario o empresa relacionado.
- **datosEmpresa**: Datos de la empresa obtenidos de la base de datos.
- **ubicaciones**: Ubicaciones asociadas a la empresa.
- **reguladora**: Almacena la información sobre la entidad reguladora de la actividad.
- **selectedActividad**: Actividad seleccionada por el usuario.
- **razonSocial**: Razón social de la empresa.
- **piso**: Piso de la dirección de la empresa.
- **codigo_postal**: Código postal de la empresa.
- **entidad**: Entidad reguladora de la actividad.
- **open**: Controla si el modal de registro de actividad está abierto o cerrado. Inicialmente está establecido como **false**.

## Métodos

### mount()
Este método se ejecuta cuando se monta el componente. Llama al método **manejarDatos** para cargar la información inicial de la empresa y actividades disponibles.

### CrearNuevaActividadEmpresa()
Este método se activa al recibir el evento **CrearNuevaActividadEmpresa**. Verifica los permisos del usuario utilizando `MiddlewareInvoker::checkPermisosRoles()`. Si el usuario tiene permisos, se procede a manejar los datos y abrir el modal de registro.

### manejarDatos()
Este método obtiene el identificador del usuario o empresa y luego carga los datos de la empresa correspondiente mediante el método **cargarDatosEmpresa**.

### cargarDatosEmpresa(\$cuit)
Este método carga los datos de la empresa a partir del **CUIT** proporcionado, incluyendo la razón social, ubicación, provincia, localidad, etc. También se encarga de verificar el CUIT en el servicio de AFIP y cargar las ubicaciones de la empresa.

### verificarCuitAfip(\$cuit)
Este método utiliza el **afipController** para verificar los datos del CUIT proporcionado. Filtra las actividades que no existen y llama al método **BuscarActividad** para buscar las actividades disponibles.

### BuscarActividad(\$codActividadNoExistentes)
Este método busca las actividades económicas que no están registradas para la empresa en el modelo **ActividadesEconomicasModel** y las almacena en la propiedad **actividad**.

### reguladora()
Este método obtiene la información sobre la entidad reguladora de la actividad seleccionada y la almacena en la propiedad **reguladora**.

### ubicacionesEmpresa(\$datosEmpresa)
Este método carga las ubicaciones disponibles para la empresa a partir del **CUIT** proporcionado.

### save()
Este método valida los datos del formulario, crea una nueva actividad mediante **crearActividad** y luego dispara los eventos necesarios para cerrar el modal y actualizar la vista.

### crearActividad()
Este método crea un nuevo registro en **EmpresasActividadesModel** con la información de la actividad seleccionada y los datos de la empresa.

### close()
Este método reinicia algunas de las propiedades del componente y cierra el modal.

## Eventos Livewire

- **CrearNuevaActividadEmpresa**: Activa el método **CrearNuevaActividadEmpresa** para manejar la creación de una nueva actividad empresarial.

## Vista y Controlador
La vista asociada a este componente se encuentra en el archivo `livewire.actividad.actividad-nueva-actividad-empresa`. Proporciona un formulario para registrar una nueva actividad económica para una empresa, permitiendo seleccionar entre las actividades disponibles y asociar una ubicación específica.

El controlador principal del componente es la clase `ActividadNuevaActividadEmpresa`, que maneja la lógica de negocio y se comunica con los modelos para realizar operaciones en la base de datos.

## Dependencias

El componente `ActividadNuevaActividadEmpresa` depende de los siguientes elementos:

- **User**: Modelo para obtener los datos del usuario actual.
- **EmpresasModel**: Modelo para manejar los datos de las empresas.
- **UbicacionesModel**: Modelo para obtener las ubicaciones de las empresas.
- **afipController**: Controlador para verificar el CUIT de la empresa en AFIP.
- **EmpresasActividadesModel**: Modelo para crear y manejar las actividades de las empresas.
- **ActividadesEconomicasModel**: Modelo para manejar las actividades económicas disponibles.
- **EstadoActividadesEconomicasModel**: Modelo para manejar el estado de las actividades económicas.
- **MiddlewareInvoker**: Servicio para verificar los permisos del usuario.

