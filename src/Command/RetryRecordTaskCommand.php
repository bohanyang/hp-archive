<?php

declare(strict_types=1);

namespace App\Command;

use App\Bundle\Message\RetryCollectRecord;
use App\Bundle\Repository\DoctrineRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Uid\Ulid;

#[AsCommand(name: 'app:retry-record-task')]
class RetryRecordTaskCommand extends Command
{
    public function __construct(private MessageBusInterface $messageBus, private DoctrineRepository $repository)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('id', InputOption::VALUE_REQUIRED, 'Record Task ID');
        $this->addOption('policy', 'p', InputOption::VALUE_OPTIONAL, 'Policy', 'error');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        if ($id = $input->getArgument('id')) {
            $id = Ulid::fromString($id);
        }

        $message = new RetryCollectRecord($this->repository->getRecordTask($id));
        $message->setPolicy($input->getOption('policy'));
        $this->messageBus->dispatch($message);

        return 0;
    }
}
