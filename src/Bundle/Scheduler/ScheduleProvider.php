<?php

declare(strict_types=1);

namespace App\Bundle\Scheduler;

use App\Bundle\Message\TriggerCollectRecords;
use Symfony\Component\Lock\LockFactory;
use Symfony\Component\Scheduler\Attribute\AsSchedule;
use Symfony\Component\Scheduler\RecurringMessage;
use Symfony\Component\Scheduler\Schedule;
use Symfony\Component\Scheduler\ScheduleProviderInterface;

use function hash;

#[AsSchedule]
class ScheduleProvider implements ScheduleProviderInterface
{
    public function __construct(
        private readonly LockFactory $lockFactory,
    ) {
    }

    public function getSchedule(): Schedule
    {
        return (new Schedule())
            ->add(
                RecurringMessage::cron('0 * * * *', new TriggerCollectRecords())->withJitter(30),
            )
            ->lock(
                $this->lockFactory->createLock(
                    hash('xxh128', 'scheduler:trigger_collect_records'),
                ),
            );
    }
}
