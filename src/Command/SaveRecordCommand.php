<?php

declare(strict_types=1);

namespace App\Command;

use App\Bundle\Repository\DoctrineRepository;
use App\Bundle\Repository\LeanCloudRepository;
use DateTimeImmutable;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Messenger\MessageBusInterface;

#[AsCommand(name: 'app:save-record')]
class SaveRecordCommand extends Command
{
    public function __construct(
        private MessageBusInterface $messageBus,
        private DoctrineRepository $repository,
        private LeanCloudRepository $leancloud,
        private LoggerInterface $logger,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        // $image = $this->repository->getImage('Rothenburg');
        // $this->messageBus->dispatch(new SaveRecord(
        //     new Record(
        //         ObjectId::create(),
        //         $image,
        //         new DateTimeImmutable('2023-08-24'),
        //         'de-DE',
        //         'Rothenburg ob der Tauber, Bayern',
        //         'Rothenburg ob der Tauber',
        //     ),
        //     OnDuplicateImage::REFER_EXISTING,
        // ));
        // $this->messageBus->dispatch(new UpdateImage(
        //     'NuitBlanche',
        //     new DateTimeImmutable('2023-09-23'),
        //     'fr-CA',
        // ));
        // $this->messageBus->dispatch(new DownloadImage(
        //     $this->repository->getImageById('5d983fad12215f00720d4c5f'),
        // ));

        // $this->logger->error('TEST ERROR 3');
        // $image  = $this->repository->getImage('Rothenburg');
        // $date   = new DateTimeImmutable('2023-08-24');
        // $market = 'de-DE';
        // $this->logger->error('TEST ERROR', [
        //     'market' => $market,
        //     'date' => $date,
        //     'image' => (array) $image,
        // ]);
        
        // $this->logger->error('TEST ERROR 2', [
        //     'market' => $market,
        //     'date' => $date,
        //     'image' => (array) $image,
        // ]);

        // $this->logger->error('TEST ERROR 3');

        $record = $this->repository->getRecord('en-AU', new DateTimeImmutable('2024-03-19'));
        $request = $this->leancloud->createRecordRequest($record);
        $this->messageBus->dispatch($request);

        return 0;
    }
}
