<?php

/**
 * Configure OpenAI endpoints and API key. Retrieving them from the .env.
 */
return [
    'usage' => [
        'models' => env('FILAMENT_AI_DASHBOARD_API_MODELS_ENDPOINT'),
        'key' => env('FILAMENT_AI_DASHBOARD_OPENAI_ADMIN_API_KEY'),
    ],
    'storage' => [
        'vector_store' => env('FILAMENT_AI_DASHBOARD_API_VECTOR_STORE_ENDPOINT'),
        'files' => env('FILAMENT_AI_DASHBOARD_API_FILES_ENDPOINT'),
        'key' => env('FILAMENT_AI_DASHBOARD_OPENAI_API_KEY'),
    ],
];
