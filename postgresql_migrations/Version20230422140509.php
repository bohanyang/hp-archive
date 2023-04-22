<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230422140509 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE message_loops (key VARCHAR(255) NOT NULL, loop_id UUID NOT NULL)');
        $this->addSql('COMMENT ON COLUMN message_loops.loop_id IS \'(DC2Type:ulid)\'');
        $this->addSql('ALTER TABLE task_logs ALTER id TYPE UUID');
        $this->addSql('ALTER TABLE task_logs ALTER task_id TYPE UUID');
        $this->addSql('COMMENT ON COLUMN task_logs.id IS \'(DC2Type:ulid)\'');
        $this->addSql('COMMENT ON COLUMN task_logs.task_id IS \'(DC2Type:ulid)\'');
        $this->addSql('ALTER INDEX idx_dfff9bcd44ac3583 RENAME TO IDX_833C52278DB60186');
        $this->addSql('ALTER TABLE tasks ALTER id TYPE UUID');
        $this->addSql('COMMENT ON COLUMN tasks.id IS \'(DC2Type:ulid)\'');
        $this->addSql('ALTER INDEX idx_281453487b00651c RENAME TO IDX_505865977B00651C');
        $this->addSql('ALTER TABLE record_tasks ALTER id TYPE UUID');
        $this->addSql('COMMENT ON COLUMN record_tasks.id IS \'(DC2Type:ulid)\'');
        $this->addSql('ALTER INDEX uniq_625167b9aa9e377a6bac85cb RENAME TO UNIQ_D103C4CBAA9E377A6BAC85CB');
        $this->addSql('ALTER TABLE image_tasks ALTER id TYPE UUID');
        $this->addSql('COMMENT ON COLUMN image_tasks.id IS \'(DC2Type:ulid)\'');
        $this->addSql('ALTER INDEX uniq_44ca447e3da5256d RENAME TO UNIQ_471DE13E3DA5256D');
        $this->addSql('ALTER TABLE users ALTER id TYPE UUID');
        $this->addSql('COMMENT ON COLUMN users.id IS \'(DC2Type:ulid)\'');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP TABLE message_loops');
        $this->addSql('ALTER TABLE image_tasks ALTER id TYPE UUID');
        $this->addSql('COMMENT ON COLUMN image_tasks.id IS NULL');
        $this->addSql('ALTER INDEX uniq_471de13e3da5256d RENAME TO uniq_44ca447e3da5256d');
        $this->addSql('ALTER TABLE tasks ALTER id TYPE UUID');
        $this->addSql('COMMENT ON COLUMN tasks.id IS NULL');
        $this->addSql('ALTER INDEX idx_505865977b00651c RENAME TO idx_281453487b00651c');
        $this->addSql('ALTER TABLE record_tasks ALTER id TYPE UUID');
        $this->addSql('COMMENT ON COLUMN record_tasks.id IS NULL');
        $this->addSql('ALTER INDEX uniq_d103c4cbaa9e377a6bac85cb RENAME TO uniq_625167b9aa9e377a6bac85cb');
        $this->addSql('ALTER TABLE users ALTER id TYPE UUID');
        $this->addSql('COMMENT ON COLUMN users.id IS NULL');
        $this->addSql('ALTER TABLE task_logs ALTER id TYPE UUID');
        $this->addSql('ALTER TABLE task_logs ALTER task_id TYPE UUID');
        $this->addSql('COMMENT ON COLUMN task_logs.id IS NULL');
        $this->addSql('COMMENT ON COLUMN task_logs.task_id IS NULL');
        $this->addSql('ALTER INDEX idx_833c52278db60186 RENAME TO idx_dfff9bcd44ac3583');
    }

    public function isTransactional(): bool
    {
        return false;
    }
}
