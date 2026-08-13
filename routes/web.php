<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ValorantMatchController;

// Rota da tela inicial (A Lista)
Route::get('/', [ValorantMatchController::class, 'index']);

Route::get('/sync', [ValorantMatchController::class, 'syncMatches']);
