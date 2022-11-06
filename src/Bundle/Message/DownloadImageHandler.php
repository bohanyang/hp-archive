<?php

declare(strict_types=1);

namespace App\Bundle\Message;

use App\Bundle\Downloader\ImageDownloader;
use App\Bundle\Downloader\VideoDownloader;
use App\Bundle\ImageSpec\ImageSpecCollection;
use App\Bundle\Repository\DoctrineRepository;
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
