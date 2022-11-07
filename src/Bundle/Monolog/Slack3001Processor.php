<?php

declare(strict_types=1);

namespace App\Bundle\Monolog;

use Monolog\LogRecord;
use Throwable;

class Slack3001Processor
{
    public function __invoke(LogRecord $record): LogRecord
    {
        if (($record->context['exception'] ?? null) instanceof Throwable) {
            $context = $record->context;
            unset($context['exception']);

            return $record->with(context: $context);
        }
    }
}
