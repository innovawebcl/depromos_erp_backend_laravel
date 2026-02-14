
# Backoffice Web Administrativo (Laravel 12) – Hexagonal

Este paquete agrega el **backend** (API) para tu backoffice, pensado para ser consumido por tu front Angular (`environment.apiUrl = http://localhost:8000/api`).

Incluye:
- Arquitectura Hexagonal (Domain / Application / Infrastructure / Interfaces)
- Migraciones (productos, tallas, banners, inventario/stock, pedidos, picking, comunas/tarifas, clientes, repartidores, roles/módulos)
- Endpoints API (REST) para los módulos solicitados
- JWT (compatible con el front: responde `{ data: { token } }`)
- Middleware de permisos por módulo (prender/apagar por rol)
- Tests de core (picking / auth)

---

## 1) Dependencias (JWT)
Este backend usa **firebase/php-jwt**.

```bash
composer require firebase/php-jwt
```

---

## 2) Copiar archivos al proyecto Laravel 12
Copia estas carpetas a tu proyecto:

- `app/`
- `config/backoffice.php`
- `database/migrations/`
- `routes/api.php` (merge si ya lo tienes)

---

## 3) Registrar Service Provider
En `config/app.php` agrega:

```php
App\Providers\HexagonalServiceProvider::class,
```

---

## 4) Registrar middlewares
En `app/Http/Kernel.php` agrega aliases:

```php
protected $middlewareAliases = [
    // ...
    'jwt' => \App\Interfaces\Http\Middleware\JwtAuth::class,
    'can.use' => \App\Interfaces\Http\Middleware\CanUseModule::class,
];
```

---

## 5) Variables de entorno
En tu `.env`:

```env
JWT_SECRET=tu_clave_larga_y_random
JWT_TTL_SECONDS=28800
```

---

## 6) Migrar y seed inicial (roles y módulos)
Ejecuta:

```bash
php artisan migrate
```

Luego crea módulos/roles/permisos (ejemplo rápido en tinker):

```php
\App\Models\Module::insert([
  ['key'=>'products','name'=>'Gestión de Productos','active'=>true,'created_at'=>now(),'updated_at'=>now()],
  ['key'=>'banners','name'=>'Banners promocionales','active'=>true,'created_at'=>now(),'updated_at'=>now()],
  ['key'=>'inventory','name'=>'Gestión de Inventario','active'=>true,'created_at'=>now(),'updated_at'=>now()],
  ['key'=>'couriers','name'=>'Gestión de Repartidores','active'=>true,'created_at'=>now(),'updated_at'=>now()],
  ['key'=>'orders','name'=>'Gestión de Pedidos','active'=>true,'created_at'=>now(),'updated_at'=>now()],
  ['key'=>'picking','name'=>'Módulo de picking','active'=>true,'created_at'=>now(),'updated_at'=>now()],
  ['key'=>'tariffs','name'=>'Tarifas por comuna','active'=>true,'created_at'=>now(),'updated_at'=>now()],
  ['key'=>'customers','name'=>'Gestión de clientes','active'=>true,'created_at'=>now(),'updated_at'=>now()],
]);

$role = \App\Models\Role::create(['name'=>'SuperAdmin']);
foreach (\App\Models\Module::all() as $m) {
  \App\Models\RoleModulePermission::create(['role_id'=>$role->id,'module_id'=>$m->id,'enabled'=>true]);
}

// asigna role_id al usuario admin
$user = \App\Models\User::first();
$user->role_id = $role->id;
$user->save();
```

---

## 7) Endpoints principales
- `POST /api/login`
- `GET/POST/PUT /api/products`
- `PATCH /api/products/{id}/sizes/{size}/toggle`
- `GET/POST/PUT/DELETE /api/banners`
- `GET/POST/PUT /api/couriers`
- `GET /api/orders?status=pending|en_route|delivered...`
- `PATCH /api/orders/{id}/assign-courier`
- `PATCH /api/orders/{id}/en-route` (envía push vía adapter; por defecto log)
- `PATCH /api/orders/{id}/delivered` (RUT/foto opcional)
- `POST /api/orders/{id}/picking/scan`
- `POST /api/orders/{id}/picking/close`
- `GET /api/communes` y `POST /api/communes/{id}/tariffs` (histórico)
- `GET /api/customers` + blacklist + purchase-goal

---

## Nota sobre el “escaneo por talla”
El modelo incluye `product_sizes.barcode` (opcional). En el scan se acepta:
- `barcode` de la talla, o
- el `code` del producto

Con esto el módulo de picking puede impedir ítems/tallas incorrectas.
