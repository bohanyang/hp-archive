<?php

declare(strict_types=1);

namespace App\Bundle\Message;

use App\Bundle\Repository\DoctrineRepository;
use Mango\TaskQueue\Messenger\Stamp\TaskStamp;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\DispatchAfterCurrentBusStamp;

#[AsMessageHandler]
class RetryDownloadImageHandler
{
    public function __construct(
        private DoctrineRepository $repository,
        private MessageBusInterface $messageBus,
    ) {
    }

    public function __invoke(RetryDownloadImage $command)
    {
        $image = $this->repository->getImageByOperationId($command->id);

        $this->messageBus->dispatch(new DownloadImage($image), [
            new DispatchAfterCurrentBusStamp(),
            new TaskStamp($command->id),
        ]);
    }
}
