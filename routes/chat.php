<?php

use App\Http\Controllers\ChatMessagesController;
use App\Http\Middleware\IsUserBelongsToStudySession;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', IsUserBelongsToStudySession::class])->group(function () {
    Route::get('/messages/get/{sessionId}', [ChatMessagesController::class, 'getMessages'])->name('getMessages');
    Route::post('/message/post/{sessionId}', [ChatMessagesController::class, 'postMessage'])->name('postMessage');
});
