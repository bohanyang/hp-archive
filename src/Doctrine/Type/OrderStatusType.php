<?php

declare(strict_types=1);

namespace App\Doctrine\Type;

class OrderStatusType extends TinyIntEnumType
{
    use ArrayTinyIntEnum;

    public const NAME = 'order_status';

    public const NEW       = 'new';
    public const PENDING   = 'pending';
    public const COMPLETED = 'completed';

    public function getName(): string
    {
        return self::NAME;
    }

    private function getEnums(): array
    {
        return [
            self::NEW,
            self::PENDING,
            self::COMPLETED,
        ];
    }
}
