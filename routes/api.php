<?php

use DavidvanSchaik\FilamentAiDashboard\Http\Controllers\ApiController;
use Illuminate\Support\Facades\Route;

Route::get('ai-dashboard/v1/organization/usage/completions', [ApiController::class, 'getModelsData']);
Route::get('ai-dashboard/v1/vector_stores', [ApiController::class, 'getVectorStores']);
Route::get('ai-dashboard/v1/files', [ApiController::class, 'getFiles']);
