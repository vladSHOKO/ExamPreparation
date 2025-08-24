<?php

use App\Http\Controllers\ResultsController;
use App\Http\Controllers\TaskController;
use App\Http\Middleware\HasRoleStudentChecker;
use App\Http\Middleware\HasRoleTeacherChecker;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'web'])->group(function () {
    Route::get('/', function () {
        return view('welcome');
    })->name('welcome');
    Route::post('/feedback', function (Request $request) {
        $request->validate([
            'message' => 'required|string|max:1000',
        ]);

        $user = $request->user();
        $comment = $request->get('message');

        DB::table('feedbacks')->insert([
            'sender' => $user->login,
            'comment' => $comment,
            'created_at' => date('Y-m-d H:i:s', time()),
        ]);

        return redirect()->route('welcome')->with('thanks', 'Спасибо Вам за обратную связь!');
    })->name('feedback');
});

Route::middleware(['auth', 'web'])->group(function () {
    Route::middleware(HasRoleTeacherChecker::class)->group(function () {
        Route::get('task/load', [TaskController::class, 'showLoadForm'])->name('showLoadForm');
        Route::post('task/load', [TaskController::class, 'loadTask'])->name('loadTask');
        Route::get('results', [ResultsController::class, 'showResults'])->name('showResults');
        Route::get('results/{taskSessionId}', [ResultsController::class, 'showDetail'])->name('showDetail');
    });
    Route::middleware(HasRoleStudentChecker::class)->group(function () {
        Route::get('task/{id}', [TaskController::class, 'showTask'])->name('task');
        Route::get('tasks/collection', [TaskController::class, 'showTaskCollection'])->name('tasks');
        Route::post('task/{id}', [TaskController::class, 'checkAnswer'])->name('checkAnswer');
    });
});
