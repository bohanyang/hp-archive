<?php

declare(strict_types=1);

namespace App\Message;

use Manyou\BingHomepage\Image;

class DownloadImage
{
    public readonly string $id;

    public function __construct(Image $image)
    {
        $this->id = $image->id;
    }
}
