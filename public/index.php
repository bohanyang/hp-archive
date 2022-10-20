<?php

use App\Kernel;

$_SERVER['APP_RUNTIME_OPTIONS'] = [
    'workers' => $_SERVER['APP_RUNTIME_WORKERS'] ?? null,
    'socket' => $_SERVER['APP_RUNTIME_SOCKET'] ?? null,
    'pid_file' => $_SERVER['APP_RUNTIME_PID_FILE'] ?? null,
    'log_file' => $_SERVER['APP_RUNTIME_LOG_FILE'] ?? null,
];

require_once dirname(__DIR__).'/vendor/autoload_runtime.php';

return function (array $context) {
    return new Kernel($context['APP_ENV'], (bool) $context['APP_DEBUG']);
};
