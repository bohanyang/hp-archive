<?php

declare(strict_types=1);

namespace App\Doctrine\Type;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\ConversionException;
use Doctrine\DBAL\Types\Type;
use JsonException;

use function is_resource;
use function json_decode;
use function json_encode;
use function stream_get_contents;

use const JSON_PRESERVE_ZERO_FRACTION;
use const JSON_THROW_ON_ERROR;
use const JSON_UNESCAPED_SLASHES;
use const JSON_UNESCAPED_UNICODE;

class JsonTextType extends Type
{
    public const NAME = 'json_text';

    public function getName()
    {
        return self::NAME;
    }

    public function getSQLDeclaration(array $column, AbstractPlatform $platform)
    {
        return $platform->getStringTypeDeclarationSQL($column);
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        if ($value === null) {
            return null;
        }

        try {
            return json_encode($value, JSON_THROW_ON_ERROR | JSON_PRESERVE_ZERO_FRACTION | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        } catch (JsonException $e) {
            throw ConversionException::conversionFailedSerialization($value, 'json', $e->getMessage(), $e);
        }
    }

    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        if ($value === null || $value === '') {
            return null;
        }

        if (is_resource($value)) {
            $value = stream_get_contents($value);
        }

        try {
            return json_decode($value, true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException $e) {
            throw ConversionException::conversionFailed($value, $this->getName(), $e);
        }
    }

    public function requiresSQLCommentHint(AbstractPlatform $platform)
    {
        return true;
    }
}
