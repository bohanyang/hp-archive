<?php

declare(strict_types=1);

namespace App\ImageSpec;

interface ImageSpec
{
    public function getMimeType(): string;

    /** @param resource $stream */
    public function assert($stream): void;

    public function generateUrl(string $urlBase): string;
}
