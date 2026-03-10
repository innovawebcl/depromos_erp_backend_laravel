# Depromos ERP - Stack Completo

Backend Laravel 12 (PHP 8.3, Arquitectura Hexagonal) + Frontend Angular 17 (Clean Architecture) + MySQL 8.0 + Nginx.

## Arquitectura de Contenedores

```
┌─────────────────────────────────────────────────────┐
│                    Docker Network                    │
│                                                     │
│  ┌──────────┐    ┌──────────┐    ┌──────────────┐  │
│  │  web      │    │  api     │    │  db           │  │
│  │  Nginx    │───▶│  PHP 8.3 │───▶│  MySQL 8.0   │  │
│  │  :80      │    │  :8000   │    │  :3306        │  │
│  │  Angular  │    │  Laravel │    │  backoffice   │  │
│  └──────────┘    └──────────┘    └──────────────┘  │
│       ▲                                             │
│       │ Puerto 80                                   │
└───────┼─────────────────────────────────────────────┘
        │
    Usuario
```

## Requisitos

- Docker Engine 24+
- Docker Compose v2+
- Ambos repositorios clonados al mismo nivel:
  ```
  ~/projects/
  ├── depromos_erp_backend_laravel/    ← este repo
  └── depromos_erp_frontend_angular/
  ```

## Levantar (Produccion)

```bash
# 1. Clonar ambos repos
gh repo clone innovawebcl/depromos_erp_backend_laravel
gh repo clone innovawebcl/depromos_erp_frontend_angular

# 2. Configurar variables
cd depromos_erp_backend_laravel
cp .env.example .env

# 3. Generar JWT secret y passwords seguros
JWT_SECRET=$(php -r "echo bin2hex(random_bytes(32));")
DB_PASS=$(openssl rand -base64 16)
ROOT_PASS=$(openssl rand -base64 16)

# 4. Editar .env con los valores generados
sed -i "s/^JWT_SECRET=.*/JWT_SECRET=$JWT_SECRET/" .env
sed -i "s/^DB_PASSWORD=.*/DB_PASSWORD=$DB_PASS/" .env
sed -i "s/^DB_ROOT_PASSWORD=.*/DB_ROOT_PASSWORD=$ROOT_PASS/" .env

# 5. Levantar stack completo
docker compose up --build -d

# 6. Ver logs (la contrasena del admin aparece aqui)
docker compose logs -f api
```

## Acceso

| Servicio | URL | Notas |
|---|---|---|
| Frontend | http://localhost | Angular SPA servida por Nginx |
| API | http://localhost/api | Proxy reverso via Nginx a Laravel |
| MySQL | localhost:33066 | Solo para debug, no exponer en produccion |

## Credenciales Iniciales

El seeder genera un usuario admin con contrasena aleatoria que se muestra en los logs del contenedor `api`:

```bash
docker compose logs api | grep "Admin password"
```

Para definir una contrasena especifica, agregar `ADMIN_DEFAULT_PASSWORD=tu_password` en `.env`.

## Desarrollo Local

### Frontend standalone (hot-reload)
```bash
cd depromos_erp_frontend_angular
docker compose up --build
# -> http://localhost:4200
```

### Backend standalone
```bash
cd depromos_erp_backend_laravel
docker compose up db api --build
# -> http://localhost:8000/api
```

## Estructura del Stack

| Contenedor | Imagen | Puerto | Funcion |
|---|---|---|---|
| `depromos-web` | nginx:1.25-alpine | 80 | Sirve Angular SPA + proxy API |
| `depromos-api` | php:8.3-cli | 8000 (interno) | Laravel API REST |
| `depromos-db` | mysql:8.0 | 3306 (interno) | Base de datos |

## Variables de Entorno

| Variable | Requerida | Default | Descripcion |
|---|---|---|---|
| `JWT_SECRET` | **Si** | - | Secret para firmar JWT tokens |
| `DB_PASSWORD` | **Si** | - | Password del usuario MySQL |
| `DB_ROOT_PASSWORD` | **Si** | - | Password de root MySQL |
| `DB_DATABASE` | No | backoffice | Nombre de la base de datos |
| `DB_USERNAME` | No | backoffice | Usuario MySQL |
| `APP_ENV` | No | production | Entorno (local/production) |
| `APP_DEBUG` | No | false | Debug mode |
| `WEB_PORT` | No | 80 | Puerto publico del frontend |
| `FRONTEND_PATH` | No | ../depromos_erp_frontend_angular/frontend | Ruta al frontend |

## Versiones

- **Backend:** v1.2.0 - 19 tablas, 21 modelos, audit log, OrderStatus enum
- **Frontend:** v1.1.0 - Alineado con backend, 0 codigo muerto, build 196 KB
