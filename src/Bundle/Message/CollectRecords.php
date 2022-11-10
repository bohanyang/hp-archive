<?php

declare(strict_types=1);

namespace App\Bundle\Message;

use DateTimeImmutable;
use Manyou\BingHomepage\CurrentTime;
use Manyou\BingHomepage\Market;

use function array_filter;
use function array_map;
use function array_values;

class CollectRecords
{
    public readonly DateTimeImmutable $date;

    /** @var Market[] */
    public readonly array $markets;

    public const DEFAULT_MARKETS = [
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

    /** @param string[] $markets */
    public function __construct(
        ?DateTimeImmutable $date = null,
        array $markets = self::DEFAULT_MARKETS,
    ) {
        $this->date  ??= $date = (new CurrentTime())->getTheLaterDate();
        $this->markets = array_values(array_filter(
            array_map(static fn (string $market) => new Market($market), $markets),
            static function (Market $market) use ($date) {
                return $market->getDate($date) <= $market->getToday();
            },
        ));
    }
}
