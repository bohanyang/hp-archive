<?php

declare(strict_types=1);

namespace App\Command;

use App\Bundle\Message\ImportFromLeanCloud;
use DateTimeImmutable;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Messenger\MessageBusInterface;

#[AsCommand(name: 'import:from-leancloud')]
class ImportFromLeanCloudCommand extends Command
{
    public function __construct(private MessageBusInterface $messageBus)
    {
        parent::__construct();
    }

    protected function configure()
    {
        $this->addArgument('later-than', InputArgument::OPTIONAL, 'Only import objects created later than e.g. 1970-01-01T00:00:00.000+00:00');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $laterThan = $input->getArgument('later-than');
        $this->messageBus->dispatch(new ImportFromLeanCloud($laterThan === null ? null : new DateTimeImmutable($laterThan)));

        return 0;
    }
}
