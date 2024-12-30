<?php

declare(strict_types=1);

use App\Kernel;

$_SERVER['APP_RUNTIME_OPTIONS'] = [
    'settings' => [
        'enable_static_handler' => true,
        'document_root' => __DIR__,
        'static_handler_locations' => ['/build'],
        'http_index_files' => [],
        'http_compression' => true,
        'http_compression_level' => 1,
        'compression_min_length' => 128,
    ],
];

require_once dirname(__DIR__) . '/vendor/autoload_runtime.php';

return static function (array $context) {
    return new Kernel($context['APP_ENV'], (bool) $context['APP_DEBUG']);
};
