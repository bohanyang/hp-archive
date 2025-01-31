<?php

declare(strict_types=1);

namespace App\Command;

use App\Repository\DoctrineRepository;
use App\Repository\LeanCloudRepository;
use DateTimeImmutable;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Messenger\MessageBusInterface;

#[AsCommand(name: 'app:add-record')]
class AddDbRecordToLeanCloudCommand extends Command
{
    public function __construct(
        private MessageBusInterface $messageBus,
        private LeanCloudRepository $leanCloud,
        private DoctrineRepository $doctrine,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('market', InputArgument::REQUIRED, 'Record Market');
        $this->addArgument('date', InputArgument::REQUIRED, 'Record Date');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $record = $this->doctrine->getRecord($input->getArgument('market'), new DateTimeImmutable($input->getArgument('date')));
        $this->messageBus->dispatch($this->leanCloud->createRecordRequest($record));

        return 0;
    }
}
