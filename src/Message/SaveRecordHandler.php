<?php

declare(strict_types=1);

namespace App\Message;

use App\Repository\DoctrineRepository;
use App\Repository\LeanCloudRepository;
use ArrayObject;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Mango\Doctrine\SchemaProvider;
use Manyou\BingHomepage\Image;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\DispatchAfterCurrentBusStamp;

#[AsMessageHandler]
class SaveRecordHandler
{
    public function __construct(
        private DoctrineRepository $doctrine,
        private LeanCloudRepository $leanCloud,
        private MessageBusInterface $messageBus,
        private SchemaProvider $schema,
        private LoggerInterface $logger,
    ) {
    }

    public function __invoke(SaveRecord $command): void
    {
        // LeanCloud request objects
        $requests = new ArrayObject();

        $this->schema->transactional(function () use ($command, $requests) {
            $record = $command->record->with(image: $this->saveImage($command, $requests));

            $this->doctrine->createRecord($record);
            $requests[] = $this->leanCloud->createRecordRequest($record);
        });

        foreach ($requests as $request) {
            $this->messageBus->dispatch($request);
        }
    }

    private function imageEquals(Image $a, Image $b): bool
    {
        return $a->copyright === $b->copyright
            && $a->downloadable === $b->downloadable
            && $a->name === $b->name;
    }

    private function saveImage(SaveRecord $command, ArrayObject $requests): Image
    {
        $record = $command->record;
        $image  = $record->image;

        try {
            $this->schema->transactional(function () use ($image, $requests) {
                $this->doctrine->createImage($image);
                $requests[] = $this->leanCloud->createImageRequest($image);
            });
        } catch (UniqueConstraintViolationException) {
            $existing = $this->doctrine->getImage($image->name);

            if (! $this->imageEquals($image, $existing)) {
                $this->logger->error('Image differs from the existing one.', [
                    'market' => $record->market,
                    'date' => $record->date,
                    'image' => (array) $image,
                    'existing' => (array) $existing,
                ]);
            }

            if ($command->updateExisting()) {
                $updated = $image->with(id: $existing->id);
                $this->doctrine->updateImage($updated);
                $requests[] = $this->leanCloud->updateImageRequest($updated);

                return $updated;
            }

            return $existing;
        }

        $this->messageBus->dispatch(new DownloadImage($image), [new DispatchAfterCurrentBusStamp()]);

        return $image;
    }
}
