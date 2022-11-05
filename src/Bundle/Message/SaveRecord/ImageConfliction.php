<?php

declare(strict_types=1);

namespace App\Bundle\Message\SaveRecord;

use Manyou\BingHomepage\Image;
use Manyou\BingHomepage\Record;
use RuntimeException;
use Throwable;

use function json_encode;
use function sprintf;

use const JSON_THROW_ON_ERROR;
use const JSON_UNESCAPED_SLASHES;
use const JSON_UNESCAPED_UNICODE;

class ImageConfliction extends RuntimeException
{
    public static function create(Record $input, Image $existing, ?Throwable $previous)
    {
        $message  = sprintf('Image confliction when saving %s - ', $input->image->name);
        $message .= sprintf("%s/%s:\n", $input->market, $input->date->format('Y-m-d'));
        $message .= sprintf("Existing: %s\n", self::formatImage($existing));
        $message .= sprintf("Input: %s\n", self::formatImage($input->image));

        return new self($message, 0, $previous);
    }

    private static function formatImage(Image $image): string
    {
        return json_encode([
            'copyright' => $image->copyright,
            'downloadable' => $image->downloadable,
        ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);
    }
}
