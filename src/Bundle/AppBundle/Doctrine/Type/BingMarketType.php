<?php

declare(strict_types=1);

namespace App\Bundle\AppBundle\Doctrine\Type;

use App\Bundle\CoreBundle\Doctrine\Type\ArrayTinyIntEnum;
use App\Bundle\CoreBundle\Doctrine\Type\TinyIntEnumType;

class BingMarketType extends TinyIntEnumType
{
    use ArrayTinyIntEnum;

    public const NAME = 'bing_market';

    public const ROW = 'ROW';
    public const US  = 'en-US';
    public const CA  = 'en-CA';
    public const QC  = 'fr-CA';
    public const UK  = 'en-GB';
    public const CN  = 'zh-CN';
    public const JP  = 'ja-JP';
    public const FR  = 'fr-FR';
    public const DE  = 'de-DE';
    public const IN  = 'en-IN';
    public const BR  = 'pt-BR';
    public const AU  = 'en-AU';
    public const IT  = 'it-IT';
    public const ES  = 'es-ES';

    public function getName(): string
    {
        return self::NAME;
    }

    private function getEnums(): array
    {
        return [
            self::ROW,
            self::US,
            self::AU,
            self::BR,
            self::CA,
            self::QC,
            self::UK,
            self::FR,
            self::IT,
            self::ES,
            self::DE,
            self::IN,
            self::CN,
            self::JP,
        ];
    }
}
