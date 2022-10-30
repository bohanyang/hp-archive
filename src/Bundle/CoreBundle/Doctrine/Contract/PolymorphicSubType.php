<?php

declare(strict_types=1);

namespace App\Bundle\CoreBundle\Doctrine\Contract;

interface PolymorphicSubType
{
    public function getTableName(): string;
}
