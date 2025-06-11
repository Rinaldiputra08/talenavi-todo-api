<?php

use App\Http\Controllers\TodoController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/todos', [TodoController::class, 'store']);
Route::get('/todos/export', [TodoController::class, 'exportExcel']);
Route::get('/chart', [TodoController::class, 'getChartData']);
