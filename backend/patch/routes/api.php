
<?php

use Illuminate\Support\Facades\Route;
use App\Interfaces\Http\Controllers\{
    AuthController,
    ProductController,
    BannerController,
    CourierController,
    OrderController,
    CustomerController,
    TariffController,
    PickingController,
    RoleController,
    UserController,
    ModuleController
};

Route::post('/login', [AuthController::class, 'login']);

Route::middleware(['jwt'])->group(function () {

    // Administración (Roles/Usuarios/Módulos)
    Route::middleware(['can.use:roles'])->group(function () {
        Route::get('/modules', [ModuleController::class, 'index']);
        Route::get('/roles', [RoleController::class, 'index']);
        Route::post('/roles', [RoleController::class, 'store']);
        Route::get('/roles/{id}', [RoleController::class, 'show']);
        Route::put('/roles/{id}', [RoleController::class, 'update']);
        Route::put('/roles/{id}/modules', [RoleController::class, 'setModules']);
    });

    Route::middleware(['can.use:users'])->group(function () {
        Route::get('/users', [UserController::class, 'index']);
        Route::post('/users', [UserController::class, 'store']);
        Route::get('/users/{id}', [UserController::class, 'show']);
        Route::put('/users/{id}', [UserController::class, 'update']);
        Route::patch('/users/{id}/toggle-active', [UserController::class, 'toggleActive']);
    });

    // Productos
    Route::middleware(['can.use:products'])->group(function () {
        Route::get('/products', [ProductController::class, 'index']);
        Route::get('/products/{id}', [ProductController::class, 'show']);
        Route::post('/products', [ProductController::class, 'store']);
        Route::put('/products/{id}', [ProductController::class, 'update']);
        Route::patch('/products/{id}/sizes/{size}/toggle', [ProductController::class, 'toggleSize']);
    });

    // Banners
    Route::middleware(['can.use:banners'])->group(function () {
        Route::get('/banners', [BannerController::class, 'index']);
        Route::get('/banners/{id}', [BannerController::class, 'show']);
        Route::post('/banners', [BannerController::class, 'store']);
        Route::put('/banners/{id}', [BannerController::class, 'update']);
        Route::delete('/banners/{id}', [BannerController::class, 'destroy']);
    });

    // Inventario (stock está en product_sizes.stock; módulo se protege igual)
    Route::middleware(['can.use:inventory'])->group(function () {
        // inventario se gestiona vía update de tallas en productos por ahora
    });

    // Repartidores
    Route::middleware(['can.use:couriers'])->group(function () {
        Route::get('/couriers', [CourierController::class, 'index']);
        Route::get('/couriers/{id}', [CourierController::class, 'show']);
        Route::post('/couriers', [CourierController::class, 'store']);
        Route::put('/couriers/{id}', [CourierController::class, 'update']);
    });

    // Pedidos
    Route::middleware(['can.use:orders'])->group(function () {
        Route::get('/orders', [OrderController::class, 'index']);
        Route::get('/orders/{orderId}', [OrderController::class, 'show']);
        Route::patch('/orders/{orderId}/assign-courier', [OrderController::class, 'assignCourier']);
        Route::patch('/orders/{orderId}/en-route', [OrderController::class, 'enRoute']);
        Route::patch('/orders/{orderId}/delivered', [OrderController::class, 'delivered']);
    });

    // Picking
    Route::middleware(['can.use:picking'])->group(function () {
        Route::post('/orders/{orderId}/picking/scan', [PickingController::class, 'scan']);
        Route::post('/orders/{orderId}/picking/close', [PickingController::class, 'close']);
    });

    // Tarifas
    Route::middleware(['can.use:communes'])->group(function () {
        Route::get('/communes', [TariffController::class, 'communes']);
        Route::patch('/communes/{communeId}/toggle', [TariffController::class, 'toggleCommune']);
        Route::get('/communes/{communeId}/tariffs', [TariffController::class, 'tariffHistory']);
        Route::post('/communes/{communeId}/tariffs', [TariffController::class, 'setCommuneTariff']);
    });

    // Clientes
    Route::middleware(['can.use:customers'])->group(function () {
        Route::get('/customers', [CustomerController::class, 'index']);
        Route::get('/customers/{id}', [CustomerController::class, 'show']);
        Route::patch('/customers/{id}/blacklist', [CustomerController::class, 'blacklist']);
        Route::patch('/customers/{id}/purchase-goal', [CustomerController::class, 'purchaseGoal']);
    });
});
