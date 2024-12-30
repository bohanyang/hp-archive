<?php

declare(strict_types=1);

namespace App\Bundle\Message\Cron;

use App\Bundle\Message\CollectRecords;
use Manyou\BingHomepage\CurrentTime;
use Manyou\BingHomepage\Market;
use Psr\Clock\ClockInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\DispatchAfterCurrentBusStamp;

use function array_filter;
use function array_map;
use function array_values;

#[AsMessageHandler]
class HourlyCrawlHandler
{
    public function __construct(
        private MessageBusInterface $bus,
        private ClockInterface $clock,
        #[Autowire('%app.enabled_markets%')]
        private array $markets,
    ) {
    }

    public function __invoke(HourlyCrawl $message): void
    {
        $date = (new CurrentTime($this->clock->now()))->getTheLaterDate();

        // Filter out markets that are not available for the given date
        $markets = array_values(array_filter(
            array_map(static fn (string $market) => new Market($market), $this->markets),
            static function (Market $market) use ($date) {
                return $market->getDate($date) <= $market->getToday();
            },
        ));

        $this->bus->dispatch(new CollectRecords($date, $markets), [new DispatchAfterCurrentBusStamp()]);
    }
}
