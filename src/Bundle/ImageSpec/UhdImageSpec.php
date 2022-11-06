<?php

declare(strict_types=1);

namespace App\Bundle\ImageSpec;

use UnexpectedValueException;

use function image_type_to_mime_type;
use function sprintf;

final class UhdImageSpec extends GdImageSpec
{
    public function __construct(
        private int $maxWidth = 1366,
        private int $maxHeight = 768,
    ) {
        parent::__construct();
    }

    protected function assertSize(int $width, int $height, int $type): void
    {
        if ($width < $this->maxWidth || $this->maxHeight < 768 || $type !== $this->type) {
            throw new UnexpectedValueException(sprintf(
                'Got %s (%dx%d) while %s (width >= %d, height >= %d) is excepted.',
                image_type_to_mime_type($type),
                $width,
                $height,
                $this->maxWidth,
                $this->maxHeight,
                image_type_to_mime_type($this->type),
            ));
        }
    }

    public function generateUrl(string $urlBase): string
    {
        return $urlBase . '_UHD.jpg';
    }
}
