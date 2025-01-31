<?php

declare(strict_types=1);

namespace App\Message;

use App\Downloader\ImageDownloader;
use App\Downloader\VideoDownloader;
use App\ImageSpec\ImageSpecCollection;
use App\Repository\DoctrineRepository;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class DownloadImageHandler
{
    public function __construct(
        private DoctrineRepository $repository,
        private ImageSpecCollection $specs,
        private ImageDownloader $imageDownloader,
        private VideoDownloader $videoDownloader,
    ) {
    }

    public function __invoke(DownloadImage $command): void
    {
        if (null === $image = $this->repository->getImageById($command->id)) {
            return;
        }

        ($this->imageDownloader)($image->urlbase, $this->specs->for($image));

        if ($image->video) {
            ($this->videoDownloader)($image->video);
        }
    }
}
