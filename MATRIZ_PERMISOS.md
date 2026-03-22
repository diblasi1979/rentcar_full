# Matriz de Permisos

Este documento resume el alcance operativo de cada rol disponible en RentCar.

## Roles

- `admin`: acceso total a todas las vistas, acciones y operaciones del sistema.
- `consultor`: acceso a todas las vistas del sistema. Puede crear y editar datos, pero no puede eliminar ni anular registros.
- `conductor`: acceso limitado al dashboard. No tiene acceso a los modulos de gestion.

## Matriz por modulo

| Modulo | Admin | Consultor | Conductor |
| --- | --- | --- | --- |
| Dashboard | Ver | Ver | Ver |
| Usuarios del sistema | Ver / Crear / Editar / Resetear contraseña | Sin acceso | Sin acceso |
| Conductores | Ver / Crear / Editar / Habilitar / Deshabilitar | Ver / Crear / Editar / Habilitar / Deshabilitar | Sin acceso |
| Vehiculos | Ver / Crear | Ver / Crear | Sin acceso |
| Alquileres | Ver / Crear / Editar / Eliminar | Ver / Crear / Editar | Sin acceso |
| Pagos | Ver / Crear / Editar / Anular / Enviar comprobante | Ver / Crear / Editar / Enviar comprobante | Sin acceso |
| Seguros | Ver / Crear / Editar / Eliminar | Ver / Crear / Editar | Sin acceso |
| Infracciones | Ver / Crear / Editar / Eliminar | Ver / Crear / Editar | Sin acceso |
| Mantenimientos | Ver / Crear / Editar / Eliminar | Ver / Crear / Editar | Sin acceso |

## Reglas generales

### Admin
- Puede acceder a cualquier vista protegida del sistema.
- Puede ejecutar cualquier accion de alta, modificacion, activacion, desactivacion, anulacion o eliminacion.
- Puede dar de alta usuarios del sistema y asignar roles.
- En backend funciona como superusuario dentro del middleware de roles.

### Consultor
- Puede ingresar a todas las vistas administrativas.
- Puede usar formularios de alta y edicion.
- No puede eliminar registros.
- No puede anular pagos.

### Conductor
- Solo puede ingresar al dashboard.
- No visualiza modulos administrativos ni metricas globales de gestion que requieran consultas internas adicionales.

## Aplicacion tecnica actual

### Backend
- Las rutas de lectura estan habilitadas para `admin` y `consultor`.
- Las rutas de creacion y edicion estan habilitadas para `admin` y `consultor`.
- Las rutas `DELETE` estan restringidas a `admin`.
- El middleware de roles permite siempre el acceso al rol `admin` aunque no se lo liste de forma explicita.

### Frontend
- El router valida acceso por permiso de vista.
- La interfaz diferencia entre permisos de acceso, gestion y eliminacion.
- Los botones de eliminar o anular solo se muestran para `admin`.
- Las acciones de crear y editar se mantienen visibles para `consultor`.

## Nota de mantenimiento

Si se agregan nuevos modulos o endpoints, esta matriz debe actualizarse junto con:

- la configuracion de permisos del frontend
- las restricciones de rutas del backend
- la documentacion funcional del sistema