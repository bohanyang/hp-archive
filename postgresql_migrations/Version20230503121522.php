<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230503121522 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('alter table task_logs alter column context type jsonb using context::jsonb');
        
        $this->addSql('alter table task_logs alter column context drop default');
        
        $this->addSql('alter table task_logs alter column extra type jsonb using extra::jsonb');
        
        $this->addSql('alter table task_logs alter column extra drop default');

        $this->addSql("ALTER TABLE tasks ALTER COLUMN status TYPE SMALLINT USING CASE status WHEN 'queueing' THEN 1 WHEN 'processing' THEN 2 WHEN 'completed' THEN 3 WHEN 'failed' THEN 4 WHEN 'cancelled' THEN 5 END");
    }

    public function down(Schema $schema): void
    {
        $this->addSql('alter table task_logs alter column context type json using context::json');
        
        $this->addSql("ALTER TABLE task_logs ALTER COLUMN context SET DEFAULT '{}'");
        
        $this->addSql('alter table task_logs alter column extra type json using extra::json');
        
        $this->addSql("ALTER TABLE task_logs ALTER COLUMN extra SET DEFAULT '{}'");

        $this->addSql("ALTER TABLE tasks ALTER COLUMN status TYPE TEXT USING CASE status WHEN 1 THEN 'queueing' WHEN 2 THEN 'processing' WHEN 3 THEN 'completed' WHEN 4 THEN 'failed' WHEN 5 THEN 'cancelled' END");
    }

    public function isTransactional(): bool
    {
        return false;
    }
}
