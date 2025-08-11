<?php

use App\Http\Controllers\TaskController;
use App\Http\Middleware\HasRoleStudentChecker;
use App\Http\Middleware\HasRoleTeacherChecker;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function () {
    Route::get('/', function () {
        return view('welcome');
    })->name('welcome');
});

Route::middleware('auth')->group(function () {
    Route::middleware(HasRoleTeacherChecker::class)->group(function () {
        Route::get('task/load', [TaskController::class, 'showLoadForm'])->name('showLoadForm');
        Route::post('task/load', [TaskController::class, 'loadTask'])->name('loadTask');
    });
    Route::middleware(HasRoleStudentChecker::class)->group(function () {
        Route::get('task/{id}', [TaskController::class, 'showTask'])->name('task');
        Route::get('tasks/collection', [TaskController::class, 'showTaskCollection'])->name('tasks');
        Route::post('task/{id}', [TaskController::class, 'checkAnswer'])->name('checkAnswer');
    });
});
