<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Manyou\LeanStorage\Request\Request;
use Symfony\Config\FrameworkConfig;

return static function (FrameworkConfig $framework): void {
    $messenger = $framework->messenger();
    $messenger->transport('sync')->dsn('sync://');
    $messenger->transport('failed')->dsn('doctrine://default?queue_name=failed');
    $messenger->transport('async')->dsn(env('MESSENGER_TRANSPORT_DSN'))->failureTransport('failed');
    $messenger->routing(Request::class)->senders(['async']);
};
