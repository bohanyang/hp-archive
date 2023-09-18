<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230809132143 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('DROP TABLE scheduler_triggers;');
        $this->addSql('DELETE FROM scheduled_messages;');
        $this->addSql('ALTER TABLE scheduled_messages DROP COLUMN available_at;');
        $this->addSql('ALTER TABLE scheduled_messages DROP COLUMN last_dispatched_at;');
        $this->addSql('ALTER TABLE scheduled_messages DROP COLUMN envelope;');
        $this->addSql('ALTER TABLE scheduled_messages ADD message_id BIGINT NOT NULL;');
        $this->addSql('ALTER TABLE scheduled_messages ADD CONSTRAINT UNIQ_AAD75C49537A1329 UNIQUE (message_id);');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE scheduled_messages DROP COLUMN message_id;');
        $this->addSql('ALTER TABLE scheduled_messages ADD envelope JSONB NOT NULL;');
        $this->addSql('ALTER TABLE scheduled_messages ADD last_dispatched_at TIMESTAMP WITHOUT TIME ZONE;');
        $this->addSql('ALTER TABLE scheduled_messages ADD available_at TIMESTAMP WITHOUT TIME ZONE NOT NULL;');
        $this->addSql('CREATE INDEX scheduled_messages_available_at_index ON public.scheduled_messages USING btree (available_at);');
        $this->addSql('CREATE TABLE public.scheduler_triggers (delay_until TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL);');
        $this->addSql('ALTER TABLE ONLY public.scheduler_triggers ADD CONSTRAINT scheduler_triggers_pk PRIMARY KEY (delay_until);');
    }

    public function isTransactional(): bool
    {
        return false;
    }
}
