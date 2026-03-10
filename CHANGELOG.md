# Changelog - Depromos ERP Backend

Todas las versiones notables de este proyecto se documentan en este archivo.
Formato basado en [Keep a Changelog](https://keepachangelog.com/es-ES/1.1.0/) y [Semantic Versioning](https://semver.org/lang/es/).

## [1.1.0] - 2026-03-10

Corrección de modelo de datos, índices de performance y modelo funcional completo.

### Correcciones Críticas (Fase 1)
- **H1:** Columnas `first_name`, `last_name`, `first_login` agregadas a tabla `users` (migración 000008)
- **H1:** `EloquentUserRepository` y `LoginUseCase` ahora mapean correctamente los campos al JWT
- **H1:** `UserController` actualizado para manejar `first_name`, `last_name` en CRUD
- **H1:** Modelo `User` con accessor `full_name` para compatibilidad
- **H9:** `SoftDeletes` agregado a modelos: User, Product, Banner, Courier, Customer
- **H9:** FK `orders.customer_id` cambiada de `cascadeOnDelete` a `restrictOnDelete`

### Índices de Performance (Fase 2)
- **H5:** Índice en `order_status_history.order_id`
- **H6:** Índice compuesto en `picking_scans(picking_session_id, order_item_id)`
- **H7:** Índice en `courier_ratings.courier_id`
- **H11:** Índice compuesto en `banners(active, starts_at, ends_at)`
- **H12:** Índice compuesto en `order_items(order_id, product_id)`

### Modelo Funcional (Fase 3)
- **H14:** Nueva tabla `delivery_addresses` con modelo `DeliveryAddress`
- **H15:** Nueva tabla `payments` con modelo `Payment` (scopes: pending, completed, failed)
- Nuevo modelo `OrderStatusHistory` (faltante en v1.0.0)
- Relaciones `deliveryAddress()`, `payments()`, `statusHistory()` agregadas a `Order`

### Migraciones Nuevas
- `000008` - `add_first_last_name_and_first_login_to_users`
- `000009` - `add_soft_deletes_and_fix_cascade`
- `000010` - `add_performance_indexes`
- `000011` - `create_delivery_addresses_and_payments_tables`

---

## [1.0.0] - 2026-03-10

Primera versión estable post-refactorización QA.

### Arquitectura
- Arquitectura hexagonal: Domain (Ports/Entities) → Application (UseCases) → Infrastructure (Adapters) → Interfaces (Controllers/Middleware)
- Patrón Result para manejo de errores en UseCases
- Inyección de dependencias vía HexagonalServiceProvider
- JWT stateless authentication con middleware custom

### Módulos
- **Auth:** Login con rate limiting, endpoint `/api/me` para perfil autenticado
- **Productos:** CRUD completo con tallas, stock y barcode
- **Banners:** CRUD con gestión de imágenes y ordenamiento
- **Pedidos:** Flujo completo (pending → picking → ready → en_route → delivered)
- **Picking:** Escaneo de ítems por barcode con validación de talla/cantidad
- **Repartidores:** CRUD con sistema de calificaciones
- **Tarifas por Comuna:** Gestión de tarifas con histórico
- **Clientes:** Listado, blacklist y meta de compras
- **Usuarios:** CRUD con activación/desactivación
- **Roles y Permisos:** RBAC dinámico por módulo

### Seguridad
- Rate limiting en `/api/login` (10 intentos/min por IP)
- JWT_SECRET obligatorio (aplicación no arranca sin definirlo)
- Contraseña de admin inicial generada aleatoriamente
- Trazabilidad de `changed_by_user_id` en historial de pedidos
- Middleware `can.use:{module}` para autorización por módulo

### Performance
- Fix N+1 en ClosePickingUseCase (query agrupada vs N queries individuales)
- Eager loading en endpoints de pedidos y productos

### Infraestructura
- Docker Compose con healthchecks para MySQL y API
- Variables de entorno parametrizadas (no hardcoded)
- Dockerfile con timezone America/Santiago y wait-for-DB
- `.env.example` documentado con todas las variables requeridas

### Limpieza
- Eliminada columna `is_blacklisted` de tabla users (pertenece a customers)
- Eliminado grupo de rutas vacío de inventario
- Corregidos 65 archivos PHP con newline antes de `<?php`
- README.md actualizado con documentación correcta del proyecto
