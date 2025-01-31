<?php

declare(strict_types=1);

namespace App\Message;

use App\Repository\DoctrineRepository;
use Manyou\BingHomepage\Client\ClientInterface;
use Manyou\BingHomepage\RequestParams;
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
        if ([] === $markets = $command->markets) {
            return;
        }

        $markets = array_diff($markets, $this->repository->getMarketsPendingOrExisting($date = $command->date));

        if ($markets === []) {
            return;
        }

        /** @var RequestParams[] */
        $requests = array_map(static fn ($market) => RequestParams::create($market, $date), $markets);

        foreach ($this->client->request(...$requests) as $record) {
            $this->messageBus->dispatch(new SaveRecord($record), [new DispatchAfterCurrentBusStamp()]);
        }
    }
}
