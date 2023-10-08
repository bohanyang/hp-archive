<?php

declare(strict_types=1);

namespace App\Bundle\Scheduler;

use App\Bundle\Message\TriggerCollectRecords;
use Symfony\Component\Scheduler\Attribute\AsSchedule;
use Symfony\Component\Scheduler\RecurringMessage;
use Symfony\Component\Scheduler\Schedule;
use Symfony\Component\Scheduler\ScheduleProviderInterface;

#[AsSchedule]
class ScheduleProvider implements ScheduleProviderInterface
{
    public function getSchedule(): Schedule
    {
        return (new Schedule())->add(
            RecurringMessage::cron('0 * * * *', new TriggerCollectRecords())->withJitter(30),
        );
    }
}
