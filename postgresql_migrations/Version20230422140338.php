<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230422140338 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql("comment on column tasks.status is '(DC2Type:task_status)';");
    }

    public function down(Schema $schema): void
    {
        $this->addSql("comment on column tasks.status is '(DC2Type:operation_status)';");
    }

    public function isTransactional(): bool
    {
        return false;
    }
}
