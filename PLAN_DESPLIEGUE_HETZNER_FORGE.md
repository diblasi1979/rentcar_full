# Plan de Despliegue: Hetzner + Forge

Documento base para desplegar RentCar en produccion usando Hetzner Cloud + Laravel Forge.

## Objetivo

Montar una infraestructura simple, estable y de costo razonable para este stack:

- Backend Laravel
- Frontend Vue + Vite
- Base de datos MySQL o MariaDB
- Autenticacion por token
- Roles de usuario
- Subida y lectura de archivos

## Arquitectura Recomendada

### Etapa Inicial

Una sola VM para toda la aplicacion.

Componentes en el mismo servidor:

- Nginx
- PHP-FPM
- Laravel
- Build del frontend Vue
- MySQL o MariaDB
- Supervisor para workers si luego hiciera falta

### Dominios recomendados

Opcion A:

- `rentcar.tudominio.com` para frontend
- `api.rentcar.tudominio.com` para backend

Opcion B:

- `tudominio.com` para frontend
- `api.tudominio.com` para backend

Recomendacion:

- usar frontend en dominio principal
- usar API en subdominio separado

Ejemplo:

- `app.rentcar.com.ar`
- `api.rentcar.com.ar`

## Infraestructura Recomendada

### Servidor

Proveedor:

- Hetzner Cloud

Tamaño sugerido para arranque serio:

- 2 vCPU
- 4 GB RAM
- 80 GB SSD

Sistema operativo:

- Ubuntu 22.04 LTS o Ubuntu 24.04 LTS

Rango de costo estimado:

- entre EUR 4.49 y EUR 11.99 por mes, segun tipo de instancia

### Forge

Plan recomendado:

- Hobby si solo lo administras vos
- Growth si queres margen operativo mayor o equipo

Costo estimado:

- USD 12 a USD 19 por mes

## Estructura Recomendada de Produccion

### Backend

- Laravel desplegado con Forge desde GitHub
- entorno `production`
- `APP_ENV=production`
- `APP_DEBUG=false`

### Frontend

Dos opciones validas:

#### Opcion 1: frontend servido por Nginx en el mismo server

- compilar `frontend` en deploy
- publicar contenido generado en directorio servido por Nginx
- apuntar el frontend a la API publica

#### Opcion 2: frontend separado en Vercel

- no es la elegida principal en esta estrategia
- solo aplica si luego queres separar responsabilidades

Recomendacion actual:

- servir el frontend compilado desde el mismo servidor para simplificar operacion y costos

### Base de datos

Etapa inicial:

- MySQL o MariaDB en el mismo servidor

Etapa posterior si crece:

- mover a base administrada

## Flujo Recomendado de Deploy

### Backend Laravel

1. conectar repositorio a Forge
2. crear sitio para API
3. configurar rama `main`
4. definir script de deploy
5. correr migraciones con cuidado
6. limpiar caches

### Frontend Vue

1. instalar dependencias del frontend en el servidor
2. compilar con `npm run build`
3. publicar `dist` en directorio web
4. definir variables `VITE_...` segun API productiva

## Script de Deploy Recomendado

### Backend

Script orientativo para Forge:

```bash
cd /home/forge/api.rentcar.com.ar
git pull origin main
composer install --no-interaction --prefer-dist --optimize-autoloader --no-dev
php artisan migrate --force
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### Frontend

Si el frontend se publica desde el mismo repo o desde deploy separado:

```bash
cd /home/forge/app.rentcar.com.ar/frontend
npm ci
npm run build
rsync -av --delete dist/ /home/forge/app.rentcar.com.ar/public/
```

Nota:

- el path exacto depende de como se organice el server en Forge
- conviene separar claramente carpeta backend y carpeta frontend

## Variables de Entorno Sugeridas

### Backend Laravel

```env
APP_NAME=RentCar
APP_ENV=production
APP_KEY=
APP_DEBUG=false
APP_URL=https://api.rentcar.com.ar

FRONTEND_URL=https://app.rentcar.com.ar

LOG_CHANNEL=stack
LOG_LEVEL=error

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=rentcar
DB_USERNAME=rentcar_user
DB_PASSWORD=super_password_segura

SESSION_DRIVER=database
CACHE_STORE=database
QUEUE_CONNECTION=database

SANCTUM_STATEFUL_DOMAINS=app.rentcar.com.ar

MAIL_MAILER=smtp
MAIL_HOST=
MAIL_PORT=
MAIL_USERNAME=
MAIL_PASSWORD=
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=no-reply@rentcar.com.ar
MAIL_FROM_NAME="RentCar"
```

### Frontend Vue

```env
VITE_API_URL=https://api.rentcar.com.ar/api
```

## Nginx: criterio general

### Frontend

- servir `index.html`
- fallback para Vue Router con `try_files`

### Backend

- document root apuntando a `public/`
- PHP-FPM activo
- limites adecuados para uploads

## Archivos y Storage

En esta app es importante porque existen documentos y comprobantes.

Configuracion inicial:

- usar storage local
- ejecutar `php artisan storage:link`
- revisar permisos de escritura para `storage/` y `bootstrap/cache/`

Cuando crezca:

- evaluar migrar archivos a S3 compatible

## SSL

Usar:

- Let's Encrypt desde Forge

Aplicar SSL a:

- frontend
- backend API

## Seguridad Basica Requerida

1. desactivar `APP_DEBUG`
2. usar contraseñas fuertes de DB y SSH
3. usar llave SSH, no password
4. restringir puertos a 22, 80 y 443
5. actualizar el servidor periodicamente
6. separar usuario de base de datos del root

## Backups

Minimo recomendado:

1. backup diario de base de datos
2. backup de archivos subidos
3. retencion minima de 7 dias

## Monitoreo Basico

Recomendado desde el inicio:

1. health check del backend
2. monitoreo de espacio en disco
3. monitoreo de RAM
4. alerta por caida del sitio

## Escalado Futuro

Cuando el sistema crezca, el orden razonable seria:

1. mover base de datos a servicio separado
2. mover archivos a object storage
3. separar frontend y backend en despliegues distintos
4. sumar Redis para cache y colas
5. agregar worker dedicado si aparecen jobs pesados

## Recomendacion Final para Este Proyecto

Implementacion sugerida de arranque:

1. Hetzner Cloud 2 vCPU / 4 GB RAM
2. Forge Hobby o Growth
3. backend en `api.tudominio.com`
4. frontend en `tudominio.com` o `app.tudominio.com`
5. MySQL en la misma VM al inicio
6. SSL con Let's Encrypt
7. backups diarios
8. deploy desde GitHub rama `main`
