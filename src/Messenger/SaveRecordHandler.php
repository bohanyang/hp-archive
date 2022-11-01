<?php

declare(strict_types=1);

namespace App\Messenger;

use App\Bundle\Repository\DoctrineRepository;
use App\LeanCloud;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Manyou\BingHomepage\Image;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class SaveRecordHandler
{
    public function __construct(
        private DoctrineRepository $repository,
        private LeanCloud $leanCloud,
    ) {
    }

    public function __invoke(SaveRecord $command): void
    {
        $this->repository
            ->getSchemaProvider()
            ->getConnection()
            ->transactional(function () use ($command) {
                $record        = $command->record;
                $record->image = $this->saveImage($command);

                $this->repository->createRecord($record);
                $this->leanCloud->insert('Archive', $record->toLeanCloud());
            });
    }

    private function saveImage(SaveRecord $command): Image
    {
        $input = $command->record->image;

        try {
            $this->repository->createImage($input);
            $this->leanCloud->insert('Image', $input->toLeanCloud());
        } catch (UniqueConstraintViolationException $e) {
            $image = $this->repository->getImage($input->name);

            if ($command->throwIfDiffer() && ! $image->equalsTo($input)) {
                throw $e;
            }

            if ($command->updateExisting()) {
                $this->repository->updateImage($input);
                $this->leanCloud->update('Image', $image->id, $input->getDataForUpdateLeanCloud());
                $input->id = $image->id;

                // Updated
                return $input;
            }

            // Existing
            return $image;
        }

        // Created
        return $input;
    }
}
