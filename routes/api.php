<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RegisterController;

//Login do usuário
Route::post('/login', [AuthController::class, 'login']);

//Logout do usuário
Route::middleware('auth:sanctum')->post('/logout', [AuthController::class, 'logout']);

//Registro do usuário
Route::post('/register', [RegisterController::class, 'register']);

//Deletar usuário
Route::middleware('auth:sanctum')->delete('/delete/{id}', [RegisterController::class, 'destroy']);
