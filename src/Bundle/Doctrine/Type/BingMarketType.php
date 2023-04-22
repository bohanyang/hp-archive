<?php

declare(strict_types=1);

namespace App\Bundle\Doctrine\Type;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Manyou\BingHomepage\Market;
use Manyou\Mango\Doctrine\Type\TinyIntArrayEnum;
use Manyou\Mango\Doctrine\Type\TinyIntType;

class BingMarketType extends TinyIntType
{
    use TinyIntArrayEnum;

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

    public function convertToPHPValue($value, AbstractPlatform $platform): mixed
    {
        return $value;
    }
}
