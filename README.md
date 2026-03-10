# Depromos ERP - Backend (Laravel 12, Arquitectura Hexagonal)

## Requisitos

- Docker + Docker Compose
- Variable de entorno `JWT_SECRET` (obligatoria)

## Levantar

```bash
# Generar JWT secret
export JWT_SECRET=$(php -r "echo bin2hex(random_bytes(32));")

# Levantar servicios
docker compose up --build
```

- API: http://localhost:8000/api

## Credenciales

El seeder genera una contraseña aleatoria que se muestra en la consola al ejecutar `db:seed`.
Para definir una contraseña específica, use la variable de entorno `ADMIN_DEFAULT_PASSWORD`.

## Módulos

Productos, Inventario, Banners, Pedidos, Picking, Repartidores, Tarifas por Comuna, Clientes, Usuarios, Roles y Permisos.
