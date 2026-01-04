<?php

/**
 * Core classes to be registered in the application's service container
 */
return [
    /**
     * OpenAI Client to communicatie with the API. Build the requests body for each endpoint
     * and returns the API response
     */
    DavidvanSchaik\FilamentAiDashboard\Clients\OpenAiClient::class,

    /**
     * Services classes that hold all the core logic for this application
     */
    DavidvanSchaik\FilamentAiDashboard\Services\AiModelService::class,
    DavidvanSchaik\FilamentAiDashboard\Services\TimeRangeService::class,
    DavidvanSchaik\FilamentAiDashboard\Services\StorageService::class,
    DavidvanSchaik\FilamentAiDashboard\Services\JobService::class,
    DavidvanSchaik\FilamentAiDashboard\Services\TokenService::class,

    /**
     * Repositories that communicate with the Database
     */
    DavidvanSchaik\FilamentAiDashboard\Repositories\TaskRunRepository::class,
    DavidvanSchaik\FilamentAiDashboard\Repositories\MessageRepository::class,
];
