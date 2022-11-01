<?php

declare(strict_types=1);

namespace App\Bundle\Doctrine\Type;

use Doctrine\DBAL\ParameterType;
use Doctrine\DBAL\Platforms\AbstractMySQLPlatform;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Platforms\OraclePlatform;
use Doctrine\DBAL\Platforms\PostgreSQLPlatform;
use Doctrine\DBAL\Platforms\SqlitePlatform;
use Doctrine\DBAL\Types\ConversionException;
use Doctrine\DBAL\Types\Type;

use function get_debug_type;
use function hex2bin;

class ObjectIdType extends Type
{
    public const DEFAULT_OPTIONS = [
        'length' => 12,
        'fixed' => true,
    ];

    public const NAME = 'object_id';

    public function getName(): string
    {
        return self::NAME;
    }

    public function getBindingType(): int
    {
        return ParameterType::BINARY;
    }

    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        return $platform->getBinaryTypeDeclarationSQL($column);
    }

    public function convertToPHPValueSQL($sqlExpr, $platform): string
    {
        if ($platform instanceof OraclePlatform) {
            return 'LOWER(RAWTOHEX(' . $sqlExpr . '))';
        }

        if (
            $platform instanceof AbstractMySQLPlatform
            || $platform instanceof SqlitePlatform
        ) {
            return 'LOWER(HEX(' . $sqlExpr . '))';
        }

        if ($platform instanceof PostgreSQLPlatform) {
            return 'encode(' . $sqlExpr . ", 'hex');";
        }

        throw new ConversionException('Unsupported database platform: ' . get_debug_type($platform));
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform): mixed
    {
        if ($value === null) {
            return null;
        }

        return hex2bin($value);
    }

    public function requiresSQLCommentHint(AbstractPlatform $platform): bool
    {
        return true;
    }
}
