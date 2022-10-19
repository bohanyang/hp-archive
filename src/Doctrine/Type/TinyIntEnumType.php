<?php

declare(strict_types=1);

namespace App\Doctrine\Type;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\ConversionException;

use function is_int;
use function sprintf;

abstract class TinyIntEnumType extends TinyIntType
{
    public const DEFAULT_OPTIONS = ['unsigned' => true];

    abstract protected function valueToId($value): ?int;

    abstract protected function idToValue(int $id);

    public function convertToPHPValue($value, AbstractPlatform $platform): mixed
    {
        if ($value === null) {
            return null;
        }

        if (is_int($value) && null !== $phpValue = $this->idToValue($value)) {
            return $phpValue;
        }

        throw ConversionException::conversionFailed($value, $this->getName());
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform): mixed
    {
        if ($value === null) {
            return null;
        }

        if (null !== $id = $this->valueToId($value)) {
            return $id;
        }

        throw new ConversionException(sprintf("Could not convert PHP value '%s' of Doctrine Type %s to database value", $value, $this->getName()));
    }
}
