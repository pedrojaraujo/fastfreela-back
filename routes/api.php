<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ServicesController;


//    USUÁRIO        //
//Login do usuário
Route::post('/login', [UserController::class, 'login']);

//Logout do usuário
Route::middleware('auth:sanctum')->post('/logout', [UserController::class, 'logout']);

//Registro do usuário
Route::post('/registerUser', [UserController::class, 'registerUser']);

//Deletar usuário
Route::middleware('auth:sanctum')->delete('/deleteUser/{id}', [UserController::class, 'destroyUser']);

//Editar usuário
Route::middleware('auth:sanctum')->put('/updateUser/{id}', [UserController::class, 'updateUser']);

//    CATEGORIAS     //

// Endpoints:
//   GET     /api/categories        -> Listar categorias
//   POST    /api/categories        -> Criar nova categoria
//   GET     /api/categories/{id}   -> Ver detalhes de uma categoria
//   PUT     /api/categories/{id}   -> Atualizar uma categoria
//   DELETE  /api/categories/{id}   -> Deletar uma categoria

Route::middleware('auth:sanctum')->apiResource('categories', CategoryController::class);


//    Serviços     //

// Endpoints:
//   GET     /api/services        -> Listar serviços
//   POST    /api/services        -> Criar nova serviço
//   GET     /api/services/{id}   -> Ver detalhes de um serviço
//   PUT     /api/services/{id}   -> Atualizar um serviço
//   DELETE  /api/services/{id}   -> Deletar um serviço

Route::middleware('auth:sanctum')->apiResource('services', ServicesController::class);
