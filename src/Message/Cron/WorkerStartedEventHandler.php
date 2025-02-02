<?php

declare(strict_types=1);

namespace App\Message\Cron;

use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\Messenger\Event\WorkerStartedEvent;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\DispatchAfterCurrentBusStamp;

#[AsEventListener]
class WorkerStartedEventHandler
{
    public function __construct(private MessageBusInterface $bus)
    {
    }

    public function __invoke(WorkerStartedEvent $event): void
    {
        $this->bus->dispatch(new HourlyCrawl(), [
            new DispatchAfterCurrentBusStamp(),
        ]);
    }
}
