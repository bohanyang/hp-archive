<?php

declare(strict_types=1);

namespace App\Bundle\Message;

use InvalidArgumentException;

class Utils
{
    public static function iterate(iterable $iterator, callable $commit, int $limit = 100)
    {
        if ($limit <= 0) {
            throw new InvalidArgumentException('Limit should be larger than 0.');
        }

        $count   = 0;
        $records = [];
        foreach ($iterator as $record) {
            $records[] = $record;

            if (++$count === $limit) {
                $commit($records);

                $count   = 0;
                $records = [];
            }
        }

        if ($records !== []) {
            $commit($records);
        }
    }
}
