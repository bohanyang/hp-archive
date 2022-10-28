<?php

declare(strict_types=1);

namespace App\Messenger;

enum OnDuplicateImage
{
    case THROW_IF_DIFFER;
    case UPDATE_EXISTING;
    case REFER_EXISTING;
}
