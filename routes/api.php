<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CatalogController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::group([
    'prefix' => 'auth'
], function () {
    Route::post('login', [AuthController::class, 'login']);

    Route::group([
        'middleware' => 'auth:api'
    ], function () {
        Route::get('logout', [AuthController::class, 'logout']);
    });
});

Route::group(["middleware" => "auth:api"], function () {
    Route::apiResource("product", ProductController::class);
    Route::apiResource("user", UserController::class);
    Route::get("catalog",  [CatalogController::class, 'index']);
});
