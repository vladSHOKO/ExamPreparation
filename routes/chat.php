<?php

use App\Http\Controllers\ChatMessagesController;
use App\Http\Middleware\IsUserBelongsToStudySession;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'web', IsUserBelongsToStudySession::class])->group(function () {
    Route::get('/messages/get/{taskId}', [ChatMessagesController::class, 'getMessages'])->name('getMessages');
    Route::post('/message/post/{taskId}', [ChatMessagesController::class, 'postMessage'])->name('postMessage');
});
