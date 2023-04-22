<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230422135218 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }
    public function up(Schema $schema): void
    {
        $this->addSql('alter table image_operations rename constraint image_operations_pkey to image_tasks_pkey;');
        $this->addSql('alter table image_operations rename to image_tasks;');
        $this->addSql('alter table record_operations rename constraint record_operations_pkey to record_tasks_pkey;');
        $this->addSql('alter table record_operations rename to record_tasks;');
        $this->addSql('alter table operation_logs rename column operation_id to task_id;');
        $this->addSql('alter table operation_logs rename constraint operation_logs_pkey to task_logs_pkey;');
        $this->addSql('alter table operation_logs rename to task_logs;');
        $this->addSql('alter table operations rename constraint operations_pkey to tasks_pkey;');
        $this->addSql('alter table operations rename to tasks;');
    }
    
    public function down(Schema $schema): void
    {
        $this->addSql('alter table image_tasks rename constraint image_tasks_pkey to image_operations_pkey;');
        $this->addSql('alter table image_tasks rename to image_operations;');
        $this->addSql('alter table record_tasks rename constraint record_tasks_pkey to record_operations_pkey;');
        $this->addSql('alter table record_tasks rename to record_operations;');
        $this->addSql('alter table task_logs rename column task_id to operation_id;');
        $this->addSql('alter table task_logs rename constraint task_logs_pkey to operation_logs_pkey;');
        $this->addSql('alter table task_logs rename to operation_logs;');
        $this->addSql('alter table tasks rename constraint tasks_pkey to operations_pkey;');
        $this->addSql('alter table tasks rename to operations;');
    }
    
    public function isTransactional(): bool
    {
        return false;
    }
}
