<?php

declare(strict_types=1);

namespace App\Bundle\Message;

use App\Bundle\Repository\DoctrineRepository;
use Manyou\BingHomepage\Client\ClientInterface;
use Manyou\BingHomepage\RequestParams;
use Manyou\Mango\Operation\Messenger\Stamp\CreateOperationStamp;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\DispatchAfterCurrentBusStamp;

use function array_diff;
use function array_map;

#[AsMessageHandler]
class CollectRecordsHandler
{
    public function __construct(
        private ClientInterface $client,
        private MessageBusInterface $messageBus,
        private DoctrineRepository $repository,
    ) {
    }

    public function __invoke(CollectRecords $command): void
    {
        $date    = $command->date;
        $markets = array_diff($command->markets, $this->repository->getMarketsPendingOrExisting($date));

        if ($markets === []) {
            return;
        }

        /** @var RequestParams[] */
        $requests = array_map(static fn ($market) => RequestParams::create($market, $date), $markets);

        foreach ($this->client->request(...$requests) as $record) {
            $this->messageBus->dispatch(new SaveRecord($record), [
                new DispatchAfterCurrentBusStamp(),
                new CreateOperationStamp(function ($id) use ($record) {
                    $this->repository->createRecordOperation($id, $record);
                }),
            ]);
        }
    }
}
