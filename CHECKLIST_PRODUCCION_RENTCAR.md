# Checklist Tecnico de Produccion: RentCar

Checklist operativo para validar que la aplicacion quede correctamente publicada en produccion.

## 1. Infraestructura

- [ ] Servidor creado en Hetzner
- [ ] Ubuntu 22.04 o 24.04 instalado
- [ ] Acceso SSH con clave configurado
- [ ] Firewall configurado
- [ ] DNS del dominio apuntando al servidor
- [ ] Servidor conectado correctamente a Forge

## 2. Dominio y SSL

- [ ] Dominio principal definido
- [ ] Subdominio de API definido
- [ ] DNS del frontend resuelve correctamente
- [ ] DNS del backend resuelve correctamente
- [ ] Certificado SSL activo en frontend
- [ ] Certificado SSL activo en backend
- [ ] Redireccion HTTP -> HTTPS activa

## 3. Backend Laravel

- [ ] Sitio backend creado en Forge
- [ ] Rama `main` configurada para deploy
- [ ] `APP_ENV=production`
- [ ] `APP_DEBUG=false`
- [ ] `APP_URL` correcto
- [ ] `FRONTEND_URL` correcto
- [ ] `APP_KEY` configurada
- [ ] `composer install --no-dev` funcionando
- [ ] `php artisan migrate --force` funcionando
- [ ] `php artisan storage:link` ejecutado
- [ ] Permisos correctos en `storage/`
- [ ] Permisos correctos en `bootstrap/cache/`

## 4. Base de Datos

- [ ] Base de datos creada
- [ ] Usuario de base creado
- [ ] Permisos del usuario correctos
- [ ] Credenciales configuradas en `.env`
- [ ] Migraciones aplicadas correctamente
- [ ] Datos iniciales verificados si aplica

## 5. Frontend Vue

- [ ] Variables `VITE_...` correctas para produccion
- [ ] `npm ci` ejecuta sin errores
- [ ] `npm run build` ejecuta sin errores
- [ ] Carpeta compilada publicada correctamente
- [ ] Vue Router funciona con refresh directo
- [ ] Frontend apunta a la URL correcta del backend

## 6. Auth, Roles y Permisos

- [ ] Login funciona en produccion
- [ ] Logout funciona en produccion
- [ ] Admin ve acceso completo
- [ ] Consultor ve solo lo permitido
- [ ] Conductor queda limitado al dashboard
- [ ] El dashboard no cierra sesion por errores de metricas
- [ ] Alta de usuarios funciona para admin
- [ ] Edicion de usuarios funciona para admin
- [ ] Reset de contraseña funciona para admin

## 7. Funcionalidad de Negocio

- [ ] Alta de conductores funcionando
- [ ] Alta de vehiculos funcionando
- [ ] Alta de alquileres funcionando
- [ ] Alta de pagos funcionando
- [ ] Alta de seguros funcionando
- [ ] Alta de mantenimientos funcionando
- [ ] Infracciones funcionando
- [ ] Dashboard carga metricas correctamente

## 8. Archivos y Uploads

- [ ] Subida de documentos de conductores funciona
- [ ] Subida de documentos de vehiculos funciona
- [ ] Subida de comprobantes funciona
- [ ] Subida de polizas funciona
- [ ] Los links a archivos publicados responden bien
- [ ] No hay errores de permisos al guardar archivos

## 9. Correo

- [ ] SMTP configurado en produccion
- [ ] Remitente configurado
- [ ] Envio de comprobantes por correo probado
- [ ] Correos no caen en error por credenciales

## 10. Seguridad

- [ ] `APP_DEBUG` desactivado
- [ ] Sin credenciales hardcodeadas en frontend
- [ ] Sin credenciales expuestas en repositorio
- [ ] Firewall activo
- [ ] Root login por password deshabilitado si aplica
- [ ] Usuario de app separado del root
- [ ] Backups configurados

## 11. Performance y Operacion

- [ ] Config cache activo
- [ ] Route cache activo
- [ ] View cache activo
- [ ] Logs de Laravel revisados
- [ ] Espacio en disco revisado
- [ ] Uso de RAM revisado
- [ ] Monitoreo o health checks configurados

## 12. Verificacion Final

- [ ] Acceso desde navegador externo funcionando
- [ ] Login admin probado
- [ ] Login consultor probado
- [ ] Login conductor probado
- [ ] Todas las rutas principales responden bien
- [ ] No hay errores criticos en consola del navegador
- [ ] No hay errores criticos recientes en logs de Laravel

## Criterio de Salida a Produccion

La app deberia considerarse lista para uso real cuando:

- todos los items criticos de auth, roles, dashboard y usuarios esten validados
- uploads funcionen correctamente
- SSL este activo
- backups esten configurados
- `.env` de produccion este completo y verificado
