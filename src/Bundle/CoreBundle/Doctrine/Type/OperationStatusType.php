<?php

declare(strict_types=1);

namespace App\Bundle\CoreBundle\Doctrine\Type;

class OperationStatusType extends TinyIntEnumType
{
    use ArrayTinyIntEnum;

    public const NAME = 'operation_status';

    public const QUEUEING   = 'queueing';
    public const PROCESSING = 'processing';
    public const COMPLETED  = 'completed';
    public const FAILED     = 'failed';
    public const CANCELLED  = 'cancelled';

    public function getName(): string
    {
        return self::NAME;
    }

    private function getEnums(): array
    {
        return [
            self::QUEUEING,
            self::PROCESSING,
            self::COMPLETED,
            self::FAILED,
            self::CANCELLED,
        ];
    }
}
