# CRUD Facturas (Laravel 12 + Breeze + Inertia React)

Proyecto de facturación con:
- Laravel 12
- Breeze con Inertia + React
- API REST versionada (`/api/v1`)
- Docker con Laravel Sail

## Requisitos

- Docker Engine
- Git
- Composer
- PHP 8.4+

## Configuración Rápida (Sail)

1. Clonar e ingresar al proyecto:

```bash
git clone https://github.com/dasaam/crud-facturas.git
cd crud-facturas
```

2. Instalar dependencias de PHP:

```bash
composer install
```

3. Copiar entorno:

```bash
cp .env.example .env
```

4. Agrega esta configuración en `.env`:

```env
APP_PORT=9001

VITE_PORT=5175

FORWARD_DB_PORT=3308

DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=crud_facturas
DB_USERNAME=sail
DB_PASSWORD=password

WWWUSER=1000
WWWGROUP=1000
```


5. Levantar contenedores:

```bash
./vendor/bin/sail up -d
```

6. Generar key, migrar y sembrar datos:

```bash
./vendor/bin/sail artisan key:generate
./vendor/bin/sail artisan migrate --seed
```

7. Instalar frontend y ejecutar Vite:

```bash
./vendor/bin/sail npm install
./vendor/bin/sail npm run dev
```

## Accesos

- Web: `http://localhost:9001`
- Login: `http://localhost:9001/login`
- API base: `http://localhost:9001/api/v1`

![Pantalla de login](./login.png)

Usuario administrador por defecto:
- Email: `admin@facturas.local`
- Password: `admin12345`

## Comandos Útiles Sail

Arrancar:

```bash
./vendor/bin/sail up -d
```

Detener (sin borrar contenedores):

```bash
./vendor/bin/sail stop
```

Detener y remover contenedores/red:

```bash
./vendor/bin/sail down
```

Reset completo (incluye volumen de MySQL):

```bash
./vendor/bin/sail down -v --remove-orphans
./vendor/bin/sail up -d --build
./vendor/bin/sail artisan migrate:fresh --seed
```

## API (resumen)

Login:
- `POST /api/v1/login`

Con `Bearer token`:
- `POST /api/v1/logout`
- `GET /api/v1/clientes`
- `GET /api/v1/productos`
- `GET /api/v1/facturas`
- `POST /api/v1/facturas`
- `GET /api/v1/facturas/{id}`
- `PUT /api/v1/facturas/{id}`
- `PATCH /api/v1/facturas/{id}/facturar`
- `PATCH /api/v1/facturas/{id}/cancelar`
