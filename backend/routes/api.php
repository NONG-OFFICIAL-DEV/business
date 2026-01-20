<?php

use App\Http\Controllers\AdjustmentController;
use App\Http\Controllers\AuditLogController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\KitchenOrderController;
use App\Http\Controllers\LossController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\PurchaseReportController;
use App\Http\Controllers\ReturnController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\StockController;
use App\Http\Controllers\StockMovementController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\UnitController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\TelegramController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');


Route::get('/test', function () {
    return response()->json([
        'message' => 'API is working'
    ]);
});

Route::post('/login', [AuthController::class, 'login']);
Route::middleware('auth:api')->get('/me', [AuthController::class, 'me']);
Route::middleware('auth:api')->post('/logout', [AuthController::class, 'logout']);

Route::middleware('jwt.auth')->group(function () {
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/refresh', [AuthController::class, 'refresh']);
    Route::get('/purchase-statuses', [PurchaseController::class, 'statuses']);
    Route::put('/purchases/{purchase}/status', [PurchaseController::class, 'updateStatus']);
    Route::get('/notifications', [NotificationController::class, 'index']);
    Route::post('/notifications/{id}/read', [NotificationController::class, 'markAsRead']);
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllAsRead']);
    Route::delete('/notifications/{id}', [NotificationController::class, 'destroy']);

    Route::get('audit-logs', [AuditLogController::class, 'index']);
    Route::get('audit-logs/{id}', [AuditLogController::class, 'show']);


    Route::apiResource('suppliers', SupplierController::class);
    Route::apiResource('categories', CategoryController::class);
    Route::apiResource('products', ProductController::class);
    Route::apiResource('stocks', StockController::class);
    Route::apiResource('purchases', PurchaseController::class);
    Route::apiResource('roles', RoleController::class);
    Route::get('/dashboard', [DashboardController::class, 'stats']);
    Route::get('/monthly-purchases', [DashboardController::class, 'getMonthlyPurchases']);
    Route::apiResource('sales', SaleController::class);
    Route::apiResource('/stock-movements', StockMovementController::class);

    Route::prefix('stock')->group(function () {
        Route::post('return', [ReturnController::class, 'returnStock']);
        Route::post('adjust', [AdjustmentController::class, 'adjustStock']);
        Route::post('loss', [LossController::class, 'reportLoss']);
    });
    Route::get('/units', [UnitController::class, 'index']);
    Route::post('/units', [UnitController::class, 'store']);
    Route::put('/units/{id}', [UnitController::class, 'update']);
    Route::delete('/units/{id}', [UnitController::class, 'destroy']);

    Route::get('/reports/purchases', [PurchaseReportController::class, 'index']);
    Route::get('/reports/inventory', [PurchaseReportController::class, 'inventoryReport']);
    Route::get('/reports/sales', [SaleController::class, 'getReport']);

    Route::apiResource('employees', EmployeeController::class);

    Route::prefix('users')->group(function () {
        Route::get('/', [UserController::class, 'index']);
        Route::post('/', [UserController::class, 'store']);
        Route::get('/{id}', [UserController::class, 'show']);
        Route::put('/{id}', [UserController::class, 'update']);
        Route::delete('/{id}', [UserController::class, 'destroy']);
    });
});


// Webhook from Telegram (no auth needed)
Route::post('/telegram/webhook', [TelegramController::class, 'webhook']);

// Link Telegram to logged-in user (JWT auth required)
Route::middleware('jwt.auth')->post('/telegram/link', [TelegramController::class, 'linkTelegram']);

Route::prefix('kitchen')->group(function () {
    Route::get('/orders', [KitchenOrderController::class, 'index']);
    Route::patch('/orders/{order}/start', [KitchenOrderController::class, 'start']);
    Route::patch('/orders/{order}/ready', [KitchenOrderController::class, 'ready']);
});

Route::post('/orders', [OrderController::class, 'store']);
Route::apiResource('menus', MenuController::class);
