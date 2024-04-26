<?php

declare(strict_types=1);

namespace App\Bundle\Doctrine\Type;

use Doctrine\DBAL\Types\Type;
use Mango\Doctrine\Type\EnumType;
use Manyou\BingHomepage\Market;

class BingMarketType extends Type
{
    use EnumType;

    public const NAME = 'bing_market';

    public function getName(): string
    {
        return self::NAME;
    }

    private function getEnums(): array
    {
        return [
            Market::ROW,
            Market::US,
            Market::AU,
            Market::BR,
            Market::CA,
            Market::QC,
            Market::UK,
            Market::FR,
            Market::IT,
            Market::ES,
            Market::DE,
            Market::IN,
            Market::CN,
            Market::JP,
        ];
    }
}
