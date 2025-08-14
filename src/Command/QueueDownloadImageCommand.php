<?php

declare(strict_types=1);

namespace App\Command;

use App\Message\DownloadImage;
use App\Repository\DoctrineRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Messenger\MessageBusInterface;

use function sprintf;

#[AsCommand(
    name: 'app:queue-download-image',
    description: 'Queue a DownloadImage message for the specified image name',
)]
class QueueDownloadImageCommand extends Command
{
    public function __construct(
        private MessageBusInterface $messageBus,
        private DoctrineRepository $repository,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('image-name', InputArgument::REQUIRED, 'The name of the image to download');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io        = new SymfonyStyle($input, $output);
        $imageName = $input->getArgument('image-name');

        $io->info(sprintf('Looking up image: %s', $imageName));

        $image = $this->repository->getImage($imageName);

        if ($image === null) {
            $io->error(sprintf('Image "%s" not found in database', $imageName));

            return Command::FAILURE;
        }

        $io->info(sprintf('Found image: %s (ID: %s)', $image->name, $image->id));

        $this->messageBus->dispatch(new DownloadImage($image));

        $io->success(sprintf('DownloadImage message queued for image: %s', $imageName));

        return Command::SUCCESS;
    }
}
