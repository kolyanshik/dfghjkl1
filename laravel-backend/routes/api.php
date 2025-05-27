<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ContactController;

// Маршрут для обработки отправки формы
Route::post('/contact', [ContactController::class, 'submit']);
