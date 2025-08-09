<?php

use App\Http\Controllers\TaskController;
use App\Http\Middleware\HasRoleStudentChecker;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function () {
    Route::get('/', function () {
        return view('welcome');
    })->name('welcome');
});

Route::middleware('auth')->group(function () {
    Route::middleware(HasRoleStudentChecker::class)->group(function () {
        Route::get('/tasks/{id}', [TaskController::class, 'showTask'])->name('task');
        Route::get('/tasks', [TaskController::class, 'showTaskCollection'])->name('tasks');
        Route::post('/tasks/{id}', [TaskController::class, 'checkAnswer'])->name('checkAnswer');
    });

});



