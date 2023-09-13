<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::prefix("/auth")
    ->controller(\App\Http\Controllers\AuthController::class)
    ->group(function () {
        Route::get("/check", "checkToken")->middleware("auth:sanctum");
        Route::get("/", "login");
        Route::delete("/", "logout")->middleware("auth:sanctum");
    });

Route::prefix("/employee")
    ->controller(\App\Http\Controllers\EmployeeController::class)
    ->middleware("auth:sanctum")
    ->group(function () {
        Route::get("/groups", "groups")->middleware("private:admin");
        Route::delete("/{id}", "destroy")->middleware("private:admin");
        Route::put("/{id}", "update")->middleware("private:admin");
        Route::post("/", "store")->middleware("private:admin");
        Route::get("/", "index")->middleware("private:admin");
    });

Route::prefix("/task")
    ->controller(\App\Http\Controllers\TaskController::class)
    ->middleware("auth:sanctum")
    ->group(function () {
        Route::delete("/{id}", "destroy")->middleware("private:admin");
        Route::put("/{id}", "update")->middleware("private:admin");
        Route::post("/", "store")->middleware("private:admin");
        Route::get("/", "index");
    });
