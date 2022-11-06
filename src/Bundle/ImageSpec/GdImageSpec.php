<?php

declare(strict_types=1);

namespace App\Bundle\ImageSpec;

use InvalidArgumentException;
use Safe\Exceptions\ImageException;

use function error_clear_last;
use function getimagesizefromstring;
use function image_type_to_extension;
use function image_type_to_mime_type;
use function Safe\stream_get_contents;

use const IMAGETYPE_JPEG;

abstract class GdImageSpec implements ImageSpec
{
    public function __construct(protected int $type = IMAGETYPE_JPEG)
    {
        if (image_type_to_extension($type) === false) {
            throw new InvalidArgumentException("Invalid image type: $type.");
        }
    }

    public function getMimeType(): string
    {
        return image_type_to_mime_type($this->type);
    }

    abstract protected function assertSize(int $width, int $height, int $type): void;

    public function assert($stream): void
    {
        $data = stream_get_contents($stream, null, 0);

        [$width, $height, $type] = self::parseImage($data);

        $this->assertSize($width, $height, $type);
    }

    private static function parseImage(string $string): array
    {
        error_clear_last();
        $safeResult = getimagesizefromstring($string);
        if ($safeResult === false) {
            throw ImageException::createFromPhpError();
        }

        return $safeResult;
    }
}
