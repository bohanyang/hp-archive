<?php

declare(strict_types=1);

namespace App\Bundle\Message;

use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\DispatchAfterCurrentBusStamp;

#[AsMessageHandler]
class TriggerCollectRecordsHandler
{
    public function __construct(
        private MessageBusInterface $bus,
    ) {
    }

    public function __invoke(TriggerCollectRecords $message): void
    {
        $this->bus->dispatch(new CollectRecords(), [new DispatchAfterCurrentBusStamp()]);
    }
}
