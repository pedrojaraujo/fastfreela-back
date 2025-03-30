<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;

//Login do usuário
Route::post('/login', [AuthController::class, 'login']);

//Logout do usuário
Route::middleware('auth:sanctum')->post('/logout', [AuthController::class, 'logoutUser']);

//Registro do usuário
Route::post('/registerUser', [UserController::class, 'register']);

//Deletar usuário
Route::middleware('auth:sanctum')->delete('/deleteUser/{id}', [UserController::class, 'destroyUser']);

//Editar usuário
Route::middleware('auth:sanctum')->delete('/updateUser/{id}', [UserController::class, 'updateUser']);
