<?php

declare(strict_types=1);

namespace App\ApiResource;

use Symfony\Component\Validator\Constraints as Assert;

class NewOrder
{
    #[Assert\NotBlank()]
    public ?string $name = null;
}
