<?php

declare(strict_types=1);

namespace App\Bundle\Message;

use DateTimeImmutable;
use InvalidArgumentException;
use Manyou\BingHomepage\CurrentTime;
use Manyou\BingHomepage\Market;
use Manyou\BingHomepage\Utils;

use function array_filter;
use function array_map;
use function array_values;
use function sprintf;

class CollectRecords
{
    public readonly DateTimeImmutable $date;

    /** @var Market[] */
    public readonly array $markets;

    private const DEFAULT_MARKETS = [
        Market::US,
        Market::BR,
        Market::CA,
        Market::QC,
        Market::UK,
        Market::FR,
        Market::DE,
        Market::IN,
        Market::CN,
        Market::JP,
        Market::ES,
        Market::IT,
        Market::AU,
    ];

    public function __construct(
        ?DateTimeImmutable $date = null,
        ?Market $market = null,
    ) {
        $today = $market === null
            ? (new CurrentTime())->getTheLaterDate()
            : $market->getToday();

        $date ??= $today;

        if ($market !== null) {
            $date = $market->getDate($date);
        }

        self::assertNotFuture($date, $today);

        $this->date    = $date;
        $this->markets = $market === null ? self::getAvailableMarkets($date) : [$market];
    }

    private static function getAvailableMarkets(DateTimeImmutable $date): array
    {
        return array_values(array_filter(
            array_map(static fn (string $market) => new Market($market), self::DEFAULT_MARKETS),
            static function (Market $market) use ($date) {
                return $market->getDate($date) <= $market->getToday();
            },
        ));
    }

    private static function assertNotFuture(DateTimeImmutable $date, DateTimeImmutable $today): void
    {
        if ($date > $today) {
            throw new InvalidArgumentException(sprintf(
                'The date "%s" is in the future. Today is "%s".',
                Utils::formatDateTime($date),
                Utils::formatDateTime($today),
            ));
        }
    }
}
