<?php

use App\Http\Controllers\TaskController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function () {
    Route::get('/', function () {
        return view('welcome');
    })->name('welcome');
});

Route::middleware('auth')->group(function () {
    Route::get('/tasks/{id}', [TaskController::class, 'showTask'])->name('task');

    Route::get('/tasks', [TaskController::class, 'showTaskCollection'])->name('tasks');

    Route::post('/tasks/{id}', [TaskController::class, 'checkAnswer'])->name('checkAnswer');
});



