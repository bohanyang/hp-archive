<?php

declare(strict_types=1);

namespace App\AmphpRuntimeBundle\PsrHttpBridge;

use Amp\Http\Server\FormParser\BufferedFile;
use Psr\Http\Message\UploadedFileInterface as PsrUploadedFile;
use RuntimeException;

use function file_put_contents;
use function strlen;

use const UPLOAD_ERR_OK;

final class BufferedUploadedFile implements PsrUploadedFile
{
    /** @var File */
    private $file;

    /** @var bool */
    private $moved = false;

    public function __construct(BufferedFile $file)
    {
        $this->file = $file;
    }

    public function getStream(): BufferedBody
    {
        if ($this->moved) {
            throw new RuntimeException('The uploaded file has been moved');
        }

        return new BufferedBody($this->file->getContents());
    }

    public function moveTo($targetPath): void
    {
        if ($this->moved) {
            throw new RuntimeException('The uploaded file has already been moved');
        }

        $this->moved = true;
        file_put_contents($targetPath, $this->file->getContents());
    }

    public function getSize(): int
    {
        return strlen($this->file->getContents());
    }

    public function getError(): int
    {
        return UPLOAD_ERR_OK;
    }

    public function getClientFilename(): ?string
    {
        return $this->file->getName();
    }

    public function getClientMediaType(): ?string
    {
        return $this->file->getMimeType();
    }
}
