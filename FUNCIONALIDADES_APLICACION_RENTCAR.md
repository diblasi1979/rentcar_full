# Funcionalidades de la Aplicacion RentCar

## 1. Descripcion general

RentCar es una aplicacion web para la gestion integral del alquiler de vehiculos.
La solucion esta compuesta por:

- Frontend en Vue 3.
- Backend API en Laravel.
- Autenticacion con token mediante Sanctum.
- Control de permisos por rol.

La aplicacion permite administrar conductores, vehiculos, alquileres, pagos, seguros, infracciones, mantenimientos y usuarios del sistema. Ademas incluye un portal especifico para usuarios con rol conductor.

## 2. Roles del sistema

### Admin

- Acceso total a todos los modulos.
- Puede crear, editar, eliminar y administrar la operacion completa.
- Puede dar de alta usuarios del sistema.
- Puede resetear contraseñas de otros usuarios.

### Consultor

- Acceso a los modulos administrativos.
- Puede crear y editar informacion operativa.
- No puede eliminar registros.
- No puede ejecutar acciones reservadas a administradores.

### Conductor

- Acceso a un portal exclusivo.
- Puede consultar su informacion operativa.
- Puede ver deuda, infracciones y documentacion asociada.
- Puede cambiar su contraseña.
- Puede generar solicitudes de servicio.

## 3. Modulos principales

## 3.1 Dashboard

El dashboard central muestra el acceso a los modulos habilitados segun el rol autenticado.

### Funcionalidades del dashboard administrativo

- Acceso rapido a usuarios, conductores, vehiculos, alquileres, pagos, seguros, infracciones y mantenimientos.
- Indicadores generales del sistema.
- Alertas de alquileres proximos a vencer.
- Alertas de alquileres vencidos con deuda.
- Alertas de vehiculos con poliza vencida.
- Alertas de vehiculos sin seguro cargado.
- Alertas de mantenimientos pendientes.

### Comportamiento para conductores

- Los usuarios con rol conductor son redirigidos automaticamente al portal del conductor.

## 3.2 Gestion de usuarios del sistema

Modulo exclusivo para administradores.

### Funcionalidades

- Alta de usuarios del sistema.
- Asignacion de rol: admin, consultor o conductor.
- Vinculacion entre usuario con rol conductor y su registro de conductor.
- Edicion de usuarios existentes.
- Reseteo de contraseña de cualquier usuario.
- Visualizacion de usuarios registrados con datos basicos.

### Reglas importantes

- Un usuario conductor debe estar vinculado a un conductor.
- Un mismo conductor no puede quedar vinculado a dos usuarios diferentes.
- Un administrador no puede quitarse a si mismo el rol admin desde la interfaz.

## 3.3 Gestion de conductores

Permite administrar las personas que conducen los vehiculos alquilados.

### Datos administrados

- Nombre.
- DNI.
- Numero de licencia.
- Vencimiento de licencia.
- Telefono.
- Email.
- Estado habilitado o deshabilitado.
- Documentacion adjunta.
- Vehiculo asignado en forma directa.

### Funcionalidades

- Alta de conductores.
- Edicion de conductores.
- Habilitacion y deshabilitacion.
- Carga de hasta cuatro documentos por conductor.
- Visualizacion del vehiculo asignado.
- Consulta de deuda consolidada por conductor.

### Informacion financiera mostrada

- Deuda por alquileres.
- Deuda por infracciones.
- Total adeudado.

## 3.4 Gestion de vehiculos

Permite administrar la flota operativa.

### Datos administrados

- Marca.
- Modelo.
- Patente.
- Año.
- Indicacion de GNC.

### Funcionalidades

- Alta de vehiculos.
- Visualizacion del listado general.
- Uso del vehiculo en alquileres, seguros, infracciones, mantenimientos y solicitudes de servicio.

## 3.5 Gestion de alquileres

Modulo para registrar y administrar contratos de alquiler.

### Datos administrados

- Conductor asociado.
- Vehiculo asociado.
- Tipo de alquiler: semanal, quincenal o mensual.
- Monto.
- Fecha de inicio.
- Vigencia contractual.
- Estado activo.
- Contrato en PDF.

### Funcionalidades

- Alta de alquileres.
- Edicion de alquileres.
- Eliminacion de alquileres por admin.
- Consulta de deuda del alquiler segun pagos realizados.
- Descarga y visualizacion del contrato cargado.

## 3.6 Gestion de pagos

Modulo para registrar y consultar pagos de alquileres e infracciones.

### Funcionalidades

- Registro de pagos de alquiler.
- Carga de comprobantes de pago.
- Registro de kilometraje informado.
- Visualizacion del historial de pagos.
- Filtros por vehiculo, conductor y rango de fechas.
- Impresion de comprobantes.
- Envio de comprobantes por email.
- Abono de infracciones adeudadas.
- Edicion de pagos.
- Anulacion o eliminacion de pagos segun permisos.

## 3.7 Gestion de seguros

Modulo para administrar coberturas de seguro por vehiculo.

### Datos administrados

- Numero de poliza.
- Numero de endoso.
- Datos del asegurado.
- Compania de seguros.
- Telefono de contacto.
- Codigo de pago electronico.
- Fecha de vigencia desde y hasta.
- PDF de poliza.
- Imagen de credencial.

### Funcionalidades

- Alta de coberturas.
- Edicion de coberturas.
- Eliminacion por admin.
- Consulta de polizas vigentes o vencidas.
- Descarga de poliza y credencial.

## 3.8 Gestion de infracciones

Modulo para registrar infracciones asociadas a vehiculos y conductores.

### Datos administrados

- Vehiculo.
- Conductor.
- Fecha de infraccion.
- Numero de acta.
- Tipo.
- Descripcion.
- Lugar.
- Monto.
- Estado: ADEUDADA o PAGADA.
- Fecha de pago.
- Adjuntos y comprobantes.

### Funcionalidades

- Alta de infracciones.
- Edicion de infracciones.
- Eliminacion por admin.
- Consulta del historial de infracciones.
- Integracion con el modulo de pagos para registrar abonos.

## 3.9 Gestion de mantenimientos

Modulo para llevar control de mantenimientos de vehiculos.

### Datos administrados

- Vehiculo.
- Fecha de mantenimiento.
- Tipo.
- Descripcion.
- Kilometraje.
- Proximo service.
- Costo.
- Estado: PENDIENTE o REALIZADO.
- Comprobante.

### Funcionalidades

- Alta de mantenimientos.
- Edicion de mantenimientos.
- Eliminacion por admin.
- Visualizacion de pendientes en dashboard.

## 3.10 Portal del conductor

Vista exclusiva para usuarios con rol conductor.

### Objetivo

Permitir que el conductor consulte y gestione su informacion personal y operativa sin acceder a los modulos administrativos.

### Informacion disponible

- Nombre y datos del conductor vinculado.
- Vehiculo asociado por alquiler activo o por asignacion directa.
- Seguro vigente del vehiculo asociado.
- Historial de solicitudes de servicio.
- Historial de infracciones.
- Saldo pendiente total.

### Funcionalidades del portal

- Ver saldo pendiente en un modal.
- Ver deuda discriminada entre alquiler e infracciones.
- Ver historial de infracciones y su estado.
- Descargar poliza del seguro.
- Descargar credencial del seguro.
- Descargar contrato si existe un alquiler activo.
- Cambiar la contraseña de acceso.
- Cargar una nueva solicitud de servicio en estado PENDIENTE.
- Visualizar el historial de solicitudes ya generadas.

### Reglas del portal

- La informacion del portal se trae a partir de la relacion entre el usuario autenticado y su conductor vinculado.
- Si el usuario conductor no estaba vinculado pero coincide por email con un conductor libre, el sistema intenta resolver esa relacion automaticamente al iniciar sesion o al consultar el perfil.
- Si no hay alquiler activo, el portal puede usar el vehiculo asignado directamente al conductor como respaldo para mostrar datos del vehiculo y del seguro.

## 3.11 Solicitudes de servicio

Modulo funcional soportado desde el portal del conductor.

### Datos administrados

- Conductor solicitante.
- Alquiler asociado si existe.
- Vehiculo asociado.
- Titulo.
- Descripcion.
- Estado.
- Fecha de creacion.

### Funcionalidades

- Alta de solicitudes desde el portal del conductor.
- Creacion inicial en estado PENDIENTE.
- Relacion con conductor, vehiculo y alquiler.
- Consulta del historial en el portal.

## 4. Relaciones funcionales clave

La aplicacion trabaja con varias relaciones entre entidades para consolidar informacion.

### Relaciones principales

- Usuario puede estar vinculado a un conductor.
- Conductor puede tener un vehiculo asignado.
- Conductor puede tener multiples alquileres.
- Alquiler vincula conductor y vehiculo.
- Pago pertenece a un alquiler.
- Infraccion puede vincular conductor y vehiculo.
- Seguro pertenece a un vehiculo.
- Mantenimiento pertenece a un vehiculo.
- Solicitud de servicio pertenece a un conductor y puede vincular alquiler y vehiculo.

## 5. Documentacion y archivos adjuntos

La aplicacion permite almacenar y exponer archivos operativos.

### Tipos de archivos utilizados

- Contratos de alquiler en PDF.
- Documentos de conductores.
- Comprobantes de pagos.
- Adjuntos y comprobantes de infracciones.
- Polizas de seguro.
- Credenciales de seguro.
- Comprobantes de mantenimiento.

### Consideraciones

- Los archivos se almacenan en el disco publico de Laravel.
- Es necesario tener activo el enlace de storage para acceder a las URLs publicas.

## 6. Seguridad y permisos

### Seguridad de acceso

- Autenticacion por token.
- Rutas protegidas por Sanctum.
- Middleware de roles en backend.
- Control de permisos y visibilidad en frontend.

### Logica de permisos

- Admin puede acceder a todo.
- Consultor puede operar la gestion diaria sin eliminar.
- Conductor solo accede a su portal.

## 7. Beneficios operativos de la solucion

- Centraliza la operacion de alquiler de vehiculos.
- Mejora el control sobre deuda e infracciones.
- Organiza documentacion contractual y de seguros.
- Permite control de mantenimiento preventivo y correctivo.
- Da autonomia a los conductores mediante un portal dedicado.
- Mantiene separacion clara entre perfiles administrativos y operativos.

## 8. Resumen ejecutivo

RentCar es una plataforma de gestion orientada a alquiler vehicular con enfoque administrativo y operativo. Los perfiles de administracion gestionan usuarios, conductores, vehiculos, alquileres, pagos, seguros, infracciones y mantenimientos. Los conductores disponen de un portal propio para consultar su informacion, descargar documentacion, ver deuda, revisar infracciones, cambiar contraseña y generar solicitudes de servicio.

La aplicacion esta preparada para operar con trazabilidad documental, control por roles y relaciones entre entidades que permiten consolidar la informacion relevante para cada usuario.