<?php

declare(strict_types=1);

namespace App\Bundle\ImageSpec;

use UnexpectedValueException;

use function image_type_to_mime_type;
use function sprintf;

final class GenericImageSpec extends GdImageSpec
{
    public function __construct(
        private int $width,
        private int $height,
    ) {
        parent::__construct();
    }

    protected function assertSize(int $width, int $height, int $type): void
    {
        if ([$width, $height, $type] !== [$this->width, $this->height, $this->type]) {
            throw new UnexpectedValueException(sprintf(
                'Got %s (%dx%d) rather than %s (%dx%d).',
                image_type_to_mime_type($type),
                $width,
                $height,
                image_type_to_mime_type($this->type),
                $this->width,
                $this->height,
            ));
        }
    }

    public function generateUrl(string $urlBase): string
    {
        return $urlBase . "_{$this->width}x{$this->height}.jpg";
    }
}
