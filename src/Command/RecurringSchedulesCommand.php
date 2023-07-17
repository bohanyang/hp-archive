<?php

declare(strict_types=1);

namespace App\Command;

use App\Bundle\Message\TriggerCollectRecords;
use Manyou\Mango\Scheduler\Scheduler;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Scheduler\RecurringMessage;

#[AsCommand(name: 'app:recurring-schedules')]
class RecurringSchedulesCommand extends Command
{
    public function __construct(private Scheduler $scheduler)
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->scheduler->recurring(
            RecurringMessage::cron('0 * * * *', new TriggerCollectRecords())->withJitter(30),
            'collect_records',
        );

        return 0;
    }
}
