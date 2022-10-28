<?php

declare(strict_types=1);

namespace App\Doctrine\Contract;

use App\Doctrine\Table;
use Doctrine\DBAL\Schema\Schema;

interface TableProvider
{
    public function __invoke(Schema $schema): Table;
}
