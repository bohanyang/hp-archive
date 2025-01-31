<?php

declare(strict_types=1);

namespace App\Message\SaveRecord;

enum OnDuplicateImage: string
{
    case THROW_IF_DIFFER = 'error';
    case UPDATE_EXISTING = 'update';
    case REFER_EXISTING  = 'ignore';
}
