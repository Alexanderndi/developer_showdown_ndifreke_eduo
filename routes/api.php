<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;

Route::middleware(['throttle:api-updates'])->post('/update-user', [UserController::class, 'update']);
