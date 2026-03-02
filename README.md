# CRUD Facturas (Laravel 12 + Breeze + Inertia React)

Proyecto de facturación con:
- Laravel 12
- Breeze con Inertia + React
- API REST versionada (`/api/v1`)
- Docker con Laravel Sail

## Requisitos

- Docker Desktop (o Docker Engine + Compose plugin)
- Git

## Configuración Rápida (Sail)

1. Clonar e ingresar al proyecto.
2. Instalar dependencias de PHP:

```bash
composer install
```

3. Copiar entorno:

```bash
cp .env.example .env
```

4. Verificar puertos en `.env` (ya configurados para evitar conflictos comunes):
- `APP_PORT=9001`
- `VITE_PORT=5175`
- `FORWARD_DB_PORT=3308`
- `DB_HOST=mysql`
- `DB_PORT=3306`
- `WWWUSER=1000`
- `WWWGROUP=1000`

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
- API base: `http://localhost:9001/api/v1`

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

Ver logs:

```bash
./vendor/bin/sail logs -f
```

## API (resumen)

Login (sin token):
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

## Troubleshooting

1. Puerto ocupado:
- Cambia en `.env`: `APP_PORT`, `VITE_PORT`, `FORWARD_DB_PORT`.
- Reinicia: `./vendor/bin/sail down && ./vendor/bin/sail up -d`.

2. Error de permisos (`EACCES` en npm o `public/hot`):
- No uses `sudo` con comandos de Sail.
- Verifica `WWWUSER=1000` y `WWWGROUP=1000`.
- Si hace falta:

```bash
sudo chown -R $USER:$USER .
./vendor/bin/sail down
./vendor/bin/sail up -d
```

3. Docker apagado:
- Inicia Docker Desktop y vuelve a ejecutar `./vendor/bin/sail up -d`.
