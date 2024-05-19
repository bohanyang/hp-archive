<?php

use App\Kernel;

$_SERVER['APP_RUNTIME_OPTIONS'] = [
    'settings' => [
        'enable_static_handler' => true,
        'document_root' => __DIR__,
        'static_handler_locations' => ['/build'],
        'http_index_files' => [],
    ],
];

require_once dirname(__DIR__).'/vendor/autoload_runtime.php';

return function (array $context) {
    return new Kernel($context['APP_ENV'], (bool) $context['APP_DEBUG']);
};
