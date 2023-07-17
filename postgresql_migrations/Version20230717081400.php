<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230717081400 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE scheduled_messages (key VARCHAR(255) NOT NULL, envelope JSONB NOT NULL, available_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, last_dispatched_at timestamp NOT NULL, PRIMARY KEY(key))');
        $this->addSql('CREATE INDEX IDX_AAD75C49E3BD61CE ON scheduled_messages (available_at)');
        $this->addSql('COMMENT ON COLUMN scheduled_messages.available_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN scheduled_messages.last_dispatched_at IS \'(DC2Type:us_datetime_immutable)\'');
        $this->addSql('CREATE TABLE scheduler_triggers (delay_until TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(delay_until))');
        $this->addSql('COMMENT ON COLUMN scheduler_triggers.delay_until IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('DROP TABLE message_loops');
        $this->addSql('DROP TABLE users');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('CREATE TABLE message_loops (key VARCHAR(255) NOT NULL, loop_id UUID NOT NULL)');
        $this->addSql('COMMENT ON COLUMN message_loops.loop_id IS \'(DC2Type:ulid)\'');
        $this->addSql('CREATE TABLE users (id UUID NOT NULL, username VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX uniq_1483a5e9f85e0677 ON users (username)');
        $this->addSql('COMMENT ON COLUMN users.id IS \'(DC2Type:ulid)\'');
        $this->addSql('DROP TABLE scheduled_messages');
        $this->addSql('DROP TABLE scheduler_triggers');
    }

    public function isTransactional(): bool
    {
        return false;
    }
}
