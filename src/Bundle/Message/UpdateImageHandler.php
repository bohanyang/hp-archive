<?php

declare(strict_types=1);

namespace App\Bundle\Message;

use App\Bundle\Repository\DoctrineRepository;
use App\Bundle\Repository\LeanCloudRepository;
use Mango\Doctrine\SchemaProvider;
use Manyou\BingHomepage\Client\ClientInterface;
use Manyou\BingHomepage\Market;
use Manyou\BingHomepage\Record;
use Manyou\BingHomepage\RequestParams;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Messenger\Exception\UnrecoverableMessageHandlingException;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\DispatchAfterCurrentBusStamp;

use function iterator_to_array;
use function sprintf;

#[AsMessageHandler]
class UpdateImageHandler
{
    public function __construct(
        private ClientInterface $client,
        private DoctrineRepository $doctrine,
        private LeanCloudRepository $leanCloud,
        private SchemaProvider $schema,
        private MessageBusInterface $messageBus,
    ) {
    }

    public function __invoke(UpdateImage $command): void
    {
        $existing = $this->doctrine->getImage($command->name);

        $request = RequestParams::create(new Market($command->market), $command->date);

        /** @var Record */
        $record = iterator_to_array($this->client->request($request))[0];

        if ($record->image->name !== $command->name) {
            throw new UnrecoverableMessageHandlingException(
                sprintf('Expected image name "%s", got "%s"', $command->name, $record->image->name),
            );
        }

        $input   = $record->image;
        $updated = $input->with(id: $existing->id);

        $this->schema->transactional(function () use ($updated) {
            $this->doctrine->updateImage($updated, true);

            $this->leanCloud->getClient()->request(
                $this->leanCloud->updateImageRequest($updated, true),
            );
        });

        $this->messageBus->dispatch(new DownloadImage($input), [
            new DispatchAfterCurrentBusStamp(),
        ]);
    }
}
