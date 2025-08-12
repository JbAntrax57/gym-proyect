<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ClientController;
use App\Http\Controllers\MembershipController;
use App\Http\Controllers\Api\ProductController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Rutas de Clientes
Route::prefix('clients')->group(function () {
    Route::get('/', [ClientController::class, 'index']);
    Route::post('/', [ClientController::class, 'store']);
    Route::get('/search', [ClientController::class, 'search']);
    Route::get('/{id}', [ClientController::class, 'show']);
    Route::put('/{id}', [ClientController::class, 'update']);
    Route::delete('/{id}', [ClientController::class, 'destroy']);
    Route::get('/{id}/stats', [ClientController::class, 'stats']);
});

// Rutas de Membresías
Route::apiResource('memberships', MembershipController::class);
Route::get('memberships/search', [MembershipController::class, 'search']);
Route::get('memberships/stats', [MembershipController::class, 'stats']);
Route::get('memberships/types', [MembershipController::class, 'types']);
Route::get('memberships/statuses', [MembershipController::class, 'statuses']);

// Rutas de Productos
Route::prefix('products')->group(function () {
    Route::get('/', [ProductController::class, 'index']);
    Route::post('/', [ProductController::class, 'store']);
    Route::get('/active', [ProductController::class, 'active']);
    Route::get('/in-stock', [ProductController::class, 'inStock']);
    Route::get('/low-stock', [ProductController::class, 'lowStock']);
    Route::get('/category/{category}', [ProductController::class, 'byCategory']);
    Route::get('/search', [ProductController::class, 'search']);
    Route::get('/{id}', [ProductController::class, 'show']);
    Route::put('/{id}', [ProductController::class, 'update']);
    Route::delete('/{id}', [ProductController::class, 'destroy']);
    Route::post('/{id}/stock', [ProductController::class, 'updateStock']);
});

// Rutas de Tipos de Membresía
Route::get('/membership-types', function () {
    return response()->json([
        'success' => true,
        'data' => \App\Models\MembershipType::active()->get(),
        'message' => 'Tipos de membresía obtenidos exitosamente'
    ]);
});

// Ruta de prueba para verificar que la API funciona
Route::get('/health', function () {
    return response()->json([
        'success' => true,
        'message' => 'API del Gym funcionando correctamente',
        'timestamp' => now()->toISOString(),
        'version' => '1.0.0'
    ]);
}); 