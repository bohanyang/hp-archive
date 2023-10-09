<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231009071628 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE record_tasks DROP CONSTRAINT fk_625167b9bf396750');
        $this->addSql('ALTER TABLE image_tasks DROP CONSTRAINT fk_44ca447e3da5256d');
        $this->addSql('ALTER TABLE image_tasks DROP CONSTRAINT fk_44ca447ebf396750');
        $this->addSql('DROP TABLE record_tasks');
        $this->addSql('DROP TABLE image_tasks');
        $this->addSql('ALTER INDEX uniq_e01fbe6a5e237e06 RENAME TO images_name_uindex');
        $this->addSql('ALTER INDEX idx_e01fbe6a57d1b6f5bf396750 RENAME TO images_debut_on_id_index');
        $this->addSql('ALTER INDEX uniq_9c9d5846aa9e377a6bac85cb RENAME TO records_date_market_uindex');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE record_tasks (id UUID NOT NULL, date DATE NOT NULL, market TEXT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX uniq_d103c4cbaa9e377a6bac85cb ON record_tasks (date, market)');
        $this->addSql('COMMENT ON COLUMN record_tasks.id IS \'(DC2Type:ulid)\'');
        $this->addSql('COMMENT ON COLUMN record_tasks.date IS \'(DC2Type:date_immutable)\'');
        $this->addSql('COMMENT ON COLUMN record_tasks.market IS \'(DC2Type:bing_market)\'');
        $this->addSql('CREATE TABLE image_tasks (id UUID NOT NULL, image_id BYTEA NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX uniq_471de13e3da5256d ON image_tasks (image_id)');
        $this->addSql('COMMENT ON COLUMN image_tasks.id IS \'(DC2Type:ulid)\'');
        $this->addSql('COMMENT ON COLUMN image_tasks.image_id IS \'(DC2Type:object_id)\'');
        $this->addSql('ALTER TABLE record_tasks ADD CONSTRAINT fk_625167b9bf396750 FOREIGN KEY (id) REFERENCES tasks (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE image_tasks ADD CONSTRAINT fk_44ca447e3da5256d FOREIGN KEY (image_id) REFERENCES images (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE image_tasks ADD CONSTRAINT fk_44ca447ebf396750 FOREIGN KEY (id) REFERENCES tasks (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER INDEX images_name_uindex RENAME TO uniq_e01fbe6a5e237e06');
        $this->addSql('ALTER INDEX images_debut_on_id_index RENAME TO idx_e01fbe6a57d1b6f5bf396750');
        $this->addSql('ALTER INDEX records_date_market_uindex RENAME TO uniq_9c9d5846aa9e377a6bac85cb');
    }

    public function isTransactional(): bool
    {
        return false;
    }
}
