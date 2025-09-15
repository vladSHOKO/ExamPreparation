<?php

use App\Http\Controllers\ApiController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->get('/marks/{classNumber}', [ApiController::class, 'getMarks'])->name('api.marks');
