<?php

declare(strict_types=1);

namespace App\Bundle\Message;

use App\Bundle\Repository\DoctrineRepository;
use Manyou\BingHomepage\Client\ClientInterface;
use Manyou\BingHomepage\Market;
use Manyou\BingHomepage\RequestParams;
use Manyou\Mango\Operation\Messenger\Stamp\OperationStamp;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\DispatchAfterCurrentBusStamp;

#[AsMessageHandler]
class RetryCollectRecordHandler
{
    public function __construct(
        private DoctrineRepository $repository,
        private MessageBusInterface $messageBus,
        private ClientInterface $client,
    ) {
    }

    public function __invoke(RetryCollectRecord $command): void
    {
        $operation = $this->repository->getRecordOperation($command->id);

        $response = $this->client->request(RequestParams::create(new Market($operation->market), $operation->date));

        foreach ($response as $record) {
            $this->messageBus->dispatch(new SaveRecord($record, $command->policy), [
                new DispatchAfterCurrentBusStamp(),
                new OperationStamp($command->id),
            ]);
        }
    }
}
