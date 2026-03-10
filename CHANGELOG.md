# Changelog - Depromos ERP Backend

Todas las versiones notables de este proyecto se documentan en este archivo.
Formato basado en [Keep a Changelog](https://keepachangelog.com/es-ES/1.1.0/) y [Semantic Versioning](https://semver.org/lang/es/).

## [1.3.0] - 2026-03-10

Infraestructura de contenedores reescrita para stack completo de producción.

### Dockerfile Backend
- Multi-stage build con PHP 8.3-cli optimizado
- OPcache habilitado para producción
- Non-root user (`appuser`) para seguridad
- Healthcheck integrado contra endpoint `/api/login`
- Timezone America/Santiago configurado
- Wait-for-DB antes de migraciones y seeder

### Docker Compose (Stack Completo)
- 3 servicios: `db` (MySQL 8.0), `api` (Laravel), `web` (Angular + Nginx)
- Healthchecks en los 3 servicios con `depends_on: condition: service_healthy`
- Red bridge dedicada `depromos-net`
- MySQL con utf8mb4, slow query log (>2s), buffer pool 256M
- Variables obligatorias: `JWT_SECRET`, `DB_PASSWORD`, `DB_ROOT_PASSWORD`
- `FRONTEND_PATH` configurable para ubicación flexible del frontend

### Documentación
- `.env.example` completo con todas las variables del stack
- `README.md` reescrito con diagrama de arquitectura, instrucciones de despliegue y desarrollo
- `.dockerignore` para excluir archivos innecesarios del build context

---

## [1.2.0] - 2026-03-10

Consolidación del modelo de datos: enum de estados, integridad referencial y auditoría global.

### OrderStatus Enum (H8)
- Enum PHP 8.1 `App\Domain\Orders\OrderStatus` con 6 estados y máquina de transiciones
- Cast nativo en modelo Order, labels en español
- 3 UseCases migrados, 0 strings hardcoded de status fuera del enum
- OrderController valida filtro status con `Rule::in(OrderStatus::values())`

### Integridad Referencial (H10, H13)
- **H10:** `customers.email` ahora tiene constraint `UNIQUE`
- **H13:** `commune_tariffs.starts_at` corregido: `DB::raw()` → `useCurrent()`

### Audit Log Global (H16)
- Tabla `audit_logs` con 4 índices optimizados
- Trait `Auditable` con boot automático (created/updated/deleted/restored)
- Excluye campos sensibles, captura user_id + IP + user_agent
- Aplicado a 6 modelos: User, Product, Order, Banner, Courier, Customer

### Migraciones Nuevas
- `000012` - `fix_customers_email_unique_and_tariffs_default`
- `000013` - `create_audit_logs_table`

---

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
