<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


use \App\Http\Controllers\CategoryController;
use \App\Http\Controllers\ProviderController;
use \App\Http\Controllers\UnityController;
use \App\Http\Controllers\ProductController;
use \App\Http\Controllers\MovementController;
use \App\Http\Controllers\AuthController;
use \App\Http\Controllers\UserController;
use \App\Http\Controllers\DashboardControlller;

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

Route::group(['middleware' => ['auth:sanctum', 'cors']], function(){
    
    Route::get('/movements/entry',  [MovementController::class, 'showEntry']);
    Route::get('/movements/out',    [MovementController::class, 'showOut']);

    Route::apiResources([
        'categories'      => CategoryController ::  class,
        'providers'       => ProviderController ::  class,
        'units'           => UnityController    ::  class,
        'products'        => ProductController  ::  class,
        'movements'       => MovementController ::  class,
        'users'           => UserController     ::  class,
    ]);    

    // Searchs
    Route::post('/users/all',           [UserController::class, 'deleteAll']);
    Route::post('/users/search',        [UserController::class, 'searchItems']);
    Route::post('/providers/all',       [ProviderController::class, 'deleteAll']);
    Route::post('/providers/search',    [ProviderController::class, 'searchItems']);
    Route::post('/products/all',        [ProductController::class, 'deleteAll']);
    
    // Dashboard
    Route::post('/dashboard',        [DashboardControlller::class, 'index']);

    Route::post('/products/all',     [ProductController::class, 'deleteAll']);
    Route::post('/movements/all',     [MovementController::class, 'deleteAll']);
});

Route::post('/login',       [AuthController::class, 'login']);
Route::post('/register',    [AuthController::class, 'register']);
Route::post('/logout',      [AuthController::class, 'logout']);

Route::get('/error', function(){
    return response()->json([
        'status' => false,
        'message' => 'Não autenticado!' 
    ],401);

})->name('error');


// Test route
Route::get('/', function () {
    return response()->json([
        'message' => 'Welcome!'
    ],200);
})->middleware('cors');
