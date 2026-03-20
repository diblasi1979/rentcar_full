
# RentCar - Full Stack Starter

## Requisitos
- PHP 8.2+
- Composer
- Node 18+
- MySQL

## Backend (Laravel)
```bash
composer create-project laravel/laravel backend
cd backend
cp ../backend_stub/.env.example .env
php artisan key:generate
# configurar DB en .env
php artisan migrate
php artisan serve
```

## Frontend (Vue + Vite)
```bash
cd ../frontend
npm install
npm run dev
```

API: http://localhost:8000/api
Frontend: http://localhost:5173

## base de datos de Mysql

#levantar los servicio
brew services start mysql

# Iniciar secion
mysql -u root -p

