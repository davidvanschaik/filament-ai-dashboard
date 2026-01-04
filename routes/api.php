<?php

use DavidvanSchaik\FilamentAiDashboard\Http\Controllers\ApiController;
use Illuminate\Support\Facades\Route;

Route::prefix('ai-dashboard/v1')->group(function () {
    Route::get('/organization/usage/completions', [ApiController::class, 'getModelsData']);
    Route::get('/vector_stores', [ApiController::class, 'getVectorStores']);
    Route::get('/files', [ApiController::class, 'getFiles']);
});
