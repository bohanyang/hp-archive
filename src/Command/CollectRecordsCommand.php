<?php

declare(strict_types=1);

namespace App\Command;

use App\Bundle\Message\CollectRecords;
use DateTimeImmutable;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Messenger\MessageBusInterface;

#[AsCommand(name: 'app:collect-records')]
class CollectRecordsCommand extends Command
{
    public function __construct(private MessageBusInterface $messageBus)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addOption('date', 'd', InputOption::VALUE_OPTIONAL, 'Date', null);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        if ($date = $input->getOption('date')) {
            $date = new DateTimeImmutable($date);
        }

        $this->messageBus->dispatch(new CollectRecords($date));

        return 0;
    }
}
