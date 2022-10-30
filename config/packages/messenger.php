<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use App\Messenger\Middleware\OperationMiddware;
use Symfony\Config\FrameworkConfig;

return static function (FrameworkConfig $framework): void {
    ($messenger = $framework->messenger())
        ->failureTransport('failed')
        ->transport('async', env('MESSENGER_TRANSPORT_DSN'))
        ->transport('failed', env('MESSENGER_TRANSPORT_FAILED_DSN'))
        ->transport('sync', 'sync://');

    $messenger
        ->bus('default')
        ->middleware(OperationMiddware::class);
};
