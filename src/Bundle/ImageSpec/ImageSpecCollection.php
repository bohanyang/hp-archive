<?php

declare(strict_types=1);

namespace App\Bundle\ImageSpec;

use Manyou\BingHomepage\Image;

use function array_flip;

class ImageSpecCollection
{
    private const UHD_EXCEPTIONS = [
        'ChipmunkCheeks',
        'BauhausArchive',
        'MiracleGarden',
        'HopeValley',
        'RielBridge',
        'TwrMawr',
        'Paepalanthus',
        'CoveSpires',
        'Mokuren',
        'KiteFestivalChatelaillon',
        'AutumnTreesNewEngland',
        'HidingEggs',
        'LaysanAlbatross',
        'EasterFountain',
        'CasaBatllo',
        'StGeorgePainting',
        'RainforestMoss',
        'MaharashtraParagliding',
        'Flatterulme',
        'AustralianNationalWarMemorial',
        'DowntownToronto',
        'GlenfinnanViaduct',
        'may1',
        'GoliSoda',
        'RamadanCharminar',
        'SaintKabir',
        'SalcombeDevon',
        'GujaratStepWell',
        'KagamiMirror',
        'Mouse2020',
        'AlpsWinter',
    ];

    /** @var ImageSpec[] */
    private array $generalSpecs;

    private ImageSpec $downloadableSpec;

    private ImageSpec $uhdSpec;

    private array $uhdExceptions;

    public function __construct()
    {
        $this->generalSpecs = [
            new GenericImageSpec(800, 480),
            new GenericImageSpec(480, 800),
            new GenericImageSpec(1366, 768),
            new GenericImageSpec(768, 1280),
            new GenericImageSpec(1920, 1080),
            new GenericImageSpec(1080, 1920),
        ];

        $this->downloadableSpec = new GenericImageSpec(1920, 1200);

        $this->uhdSpec = new UhdImageSpec();

        $this->uhdExceptions = array_flip(self::UHD_EXCEPTIONS);
    }

    public function for(Image $image): array
    {
        $specs = $this->generalSpecs;

        if ($image->downloadable) {
            $specs[] = $this->downloadableSpec;
        }

        if (! isset($this->uhdExceptions[$image->name])) {
            $specs[] = $this->uhdSpec;
        }

        return $specs;
    }
}
