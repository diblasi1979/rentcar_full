
# RentCar

Sistema de gestion para alquiler de vehiculos con frontend en Vue 3 y backend en Laravel.

## Requisitos
- PHP 8.2+
- Composer
- Node 18+
- MySQL

## Puesta en marcha

### Base de datos MySQL
```bash
brew services start mysql
mysql -u root -p
```

### Backend
```bash
cd backend
composer install
cp .env.example .env
php artisan key:generate
# configurar DB en .env
php artisan migrate
php artisan storage:link
php artisan serve
```

### Frontend
```bash
cd frontend
npm install
npm run dev
```

## URLs locales
- API: http://localhost:8000/api
- Frontend: http://localhost:5173

## Autenticacion del backend
- `POST /api/login`: login de usuario.
- Todas las demas rutas requieren token Bearer con Sanctum.
- `GET /api/me`: devuelve el usuario autenticado.

Ejemplo de login:

```json
{
	"email": "admin@rentcar.com",
	"password": "1234"
}
```

## APIs funcionales del backend

### 1. Autenticacion

#### `POST /api/login`
Permite iniciar sesion y obtener token.

Body:
- `email` requerido
- `password` requerido

Respuesta:
- `user`
- `token`

#### `GET /api/me`
Devuelve el usuario autenticado por token.

### 2. Conductores

#### `GET /api/drivers`
Lista todos los conductores.

#### `POST /api/drivers`
Crea un conductor.

Campos:
- `name` requerido
- `dni` opcional
- `license_number` opcional
- `license_expiration` opcional
- `phone` opcional
- `email` opcional
- `enabled` opcional
- `documents[]` opcional, hasta 4 archivos `jpg`, `jpeg`, `png`, `gif` o `pdf`

#### `PUT /api/drivers/{id}`
Actualiza un conductor.

Campos:
- mismos campos que alta
- permite agregar mas documentos hasta un maximo de 4

#### `PATCH /api/drivers/{id}/toggle`
Activa o desactiva un conductor.

### 3. Vehiculos

#### `GET /api/vehicles`
Lista todos los vehiculos.

#### `POST /api/vehicles`
Crea un vehiculo.

Campos:
- `brand` opcional
- `model` opcional
- `plate` requerido y unico
- `year` opcional
- `has_gnc` opcional

### 4. Alquileres

#### `GET /api/rentals`
Lista alquileres con relaciones:
- conductor
- vehiculo
- pagos

#### `POST /api/rentals`
Crea un alquiler.

Campos:
- `driver_id` requerido
- `vehicle_id` requerido
- `type` requerido: `semanal`, `quincenal`, `mensual`
- `amount` requerido
- `start_date` requerido
- `contract_from` requerido
- `contract_to` requerido
- `active` opcional
- `contract_pdf` opcional, PDF

#### `PUT /api/rentals/{id}`
Actualiza un alquiler.

Campos:
- `amount` requerido
- `start_date` requerido
- `active` opcional
- `contract_pdf` opcional, PDF

#### `DELETE /api/rentals/{id}`
Elimina un alquiler y su contrato si existe.

#### `GET /api/rentals/{id}/debt`
Calcula la deuda del alquiler.

Respuesta:
- `expected`
- `paid`
- `debt`

### 5. Pagos de alquiler

#### `GET /api/payments`
Lista pagos con relaciones:
- alquiler
- conductor
- vehiculo

#### `POST /api/payments`
Registra un pago de alquiler.

Campos:
- `rental_id` requerido
- `amount` requerido
- `payment_date` opcional
- `km_reported` opcional
- `notes` opcional
- `payment_receipt` opcional, PDF o imagen

Respuesta:
- `payment`
- `km_traveled`

#### `PUT /api/payments/{id}`
Actualiza un pago.

Campos:
- `amount` requerido
- `km_reported` opcional
- `payment_receipt` opcional, PDF o imagen

#### `DELETE /api/payments/{id}`
Anula o elimina un pago.

#### `POST /api/payments/send-receipt`
Envia el comprobante del pago por email.

Campos:
- `payment_id` requerido
- `email` requerido

### 6. Coberturas de seguro

#### `GET /api/insurance-coverages`
Lista coberturas de seguro con vehiculo asociado.

#### `POST /api/insurance-coverages`
Crea una cobertura de seguro.

Campos:
- `vehicle_id` requerido
- `policy_number` requerido
- `endorsement_number` opcional
- `insured_last_name` requerido
- `insured_first_name` requerido
- `insured_document_type` requerido
- `insured_document_number` requerido
- `insurance_company` requerido
- `contact_phone` opcional
- `electronic_payment_code` opcional
- `valid_from` requerido
- `valid_to` requerido
- `policy_pdf` requerido, PDF
- `credential_image` requerido, imagen

#### `PUT /api/insurance-coverages/{id}`
Actualiza una cobertura.

Campos:
- mismos campos que alta
- `policy_pdf` y `credential_image` pasan a ser opcionales en edicion

#### `DELETE /api/insurance-coverages/{id}`
Elimina una cobertura y sus archivos asociados.

### 7. Infracciones

#### `GET /api/traffic-infractions`
Lista infracciones con vehiculo y conductor.

#### `POST /api/traffic-infractions`
Crea una infraccion.

Campos:
- `vehicle_id` requerido
- `driver_id` opcional
- `infraction_date` requerido
- `report_number` opcional
- `type` requerido
- `description` requerido
- `location` opcional
- `amount` requerido
- `status` requerido: `ADEUDADA` o `PAGADA`
- `payment_date` opcional
- `attachment` opcional, PDF o imagen
- `payment_receipt` opcional, PDF o imagen

#### `PUT /api/traffic-infractions/{id}`
Actualiza una infraccion.

Campos:
- mismos campos que alta

#### `DELETE /api/traffic-infractions/{id}`
Elimina una infraccion junto con sus archivos.

### 8. Mantenimientos de vehiculos

#### `GET /api/vehicle-maintenances`
Lista mantenimientos con vehiculo asociado.

#### `POST /api/vehicle-maintenances`
Crea un mantenimiento.

Campos:
- `vehicle_id` requerido
- `maintenance_date` requerido
- `type` requerido
- `description` requerido
- `mileage` opcional
- `next_service_mileage` opcional
- `cost` opcional
- `status` requerido: `PENDIENTE` o `REALIZADO`
- `receipt` opcional, PDF o imagen

#### `PUT /api/vehicle-maintenances/{id}`
Actualiza un mantenimiento.

Campos:
- mismos campos que alta

#### `DELETE /api/vehicle-maintenances/{id}`
Elimina un mantenimiento y su comprobante si existe.

## Archivos y storage
- El backend usa el disco `public` de Laravel para contratos, comprobantes, documentos y adjuntos.
- Para exponer URLs publicas es necesario ejecutar:

```bash
php artisan storage:link
```

