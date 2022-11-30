<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221130110023 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE operations (id BYTEA NOT NULL, status SMALLINT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_281453487B00651C ON operations (status)');
        $this->addSql('COMMENT ON COLUMN operations.id IS \'(DC2Type:ulid)\'');
        $this->addSql('COMMENT ON COLUMN operations.status IS \'(DC2Type:operation_status)\'');
        $this->addSql('CREATE TABLE operation_logs (id BYTEA NOT NULL, operation_id BYTEA NOT NULL, level SMALLINT NOT NULL, message TEXT NOT NULL, context JSON DEFAULT \'{}\' NOT NULL, extra JSON DEFAULT \'{}\' NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_DFFF9BCD44AC3583 ON operation_logs (operation_id)');
        $this->addSql('COMMENT ON COLUMN operation_logs.id IS \'(DC2Type:ulid)\'');
        $this->addSql('COMMENT ON COLUMN operation_logs.operation_id IS \'(DC2Type:ulid)\'');
        $this->addSql('COMMENT ON COLUMN operation_logs.level IS \'(DC2Type:log_level)\'');
        $this->addSql('CREATE TABLE images (id BYTEA NOT NULL, name VARCHAR(500) NOT NULL, debut_on DATE NOT NULL, urlbase VARCHAR(500) NOT NULL, copyright VARCHAR(500) NOT NULL, downloadable BOOLEAN NOT NULL, video VARCHAR(2000) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_E01FBE6A5E237E06 ON images (name)');
        $this->addSql('CREATE INDEX IDX_E01FBE6A57D1B6F5 ON images (debut_on)');
        $this->addSql('COMMENT ON COLUMN images.id IS \'(DC2Type:object_id)\'');
        $this->addSql('COMMENT ON COLUMN images.debut_on IS \'(DC2Type:date_immutable)\'');
        $this->addSql('COMMENT ON COLUMN images.video IS \'(DC2Type:json_text)\'');
        $this->addSql('CREATE TABLE records (id BYTEA NOT NULL, image_id BYTEA NOT NULL, date DATE NOT NULL, market SMALLINT NOT NULL, title VARCHAR(500) NOT NULL, keyword VARCHAR(500) DEFAULT NULL, headline VARCHAR(500) DEFAULT NULL, description VARCHAR(1000) DEFAULT NULL, quickfact VARCHAR(500) DEFAULT NULL, hotspots VARCHAR(2000) DEFAULT NULL, messages VARCHAR(2000) DEFAULT NULL, coverstory VARCHAR(2000) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_9C9D5846AA9E377A6BAC85CB ON records (date, market)');
        $this->addSql('CREATE INDEX IDX_9C9D58463DA5256D ON records (image_id)');
        $this->addSql('COMMENT ON COLUMN records.id IS \'(DC2Type:object_id)\'');
        $this->addSql('COMMENT ON COLUMN records.image_id IS \'(DC2Type:object_id)\'');
        $this->addSql('COMMENT ON COLUMN records.date IS \'(DC2Type:date_immutable)\'');
        $this->addSql('COMMENT ON COLUMN records.market IS \'(DC2Type:bing_market)\'');
        $this->addSql('COMMENT ON COLUMN records.hotspots IS \'(DC2Type:json_text)\'');
        $this->addSql('COMMENT ON COLUMN records.messages IS \'(DC2Type:json_text)\'');
        $this->addSql('COMMENT ON COLUMN records.coverstory IS \'(DC2Type:json_text)\'');
        $this->addSql('CREATE TABLE record_operations (id BYTEA NOT NULL, date DATE NOT NULL, market SMALLINT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_625167B9AA9E377A6BAC85CB ON record_operations (date, market)');
        $this->addSql('COMMENT ON COLUMN record_operations.id IS \'(DC2Type:ulid)\'');
        $this->addSql('COMMENT ON COLUMN record_operations.date IS \'(DC2Type:date_immutable)\'');
        $this->addSql('COMMENT ON COLUMN record_operations.market IS \'(DC2Type:bing_market)\'');
        $this->addSql('CREATE TABLE image_operations (id BYTEA NOT NULL, image_id BYTEA NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_44CA447E3DA5256D ON image_operations (image_id)');
        $this->addSql('COMMENT ON COLUMN image_operations.id IS \'(DC2Type:ulid)\'');
        $this->addSql('COMMENT ON COLUMN image_operations.image_id IS \'(DC2Type:object_id)\'');
        $this->addSql('CREATE TABLE users (id BYTEA NOT NULL, username VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN users.id IS \'(DC2Type:ulid)\'');
        $this->addSql('ALTER TABLE operation_logs ADD CONSTRAINT FK_DFFF9BCD44AC3583 FOREIGN KEY (operation_id) REFERENCES operations (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE records ADD CONSTRAINT FK_9C9D58463DA5256D FOREIGN KEY (image_id) REFERENCES images (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE record_operations ADD CONSTRAINT FK_625167B9BF396750 FOREIGN KEY (id) REFERENCES operations (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE image_operations ADD CONSTRAINT FK_44CA447EBF396750 FOREIGN KEY (id) REFERENCES operations (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE image_operations ADD CONSTRAINT FK_44CA447E3DA5256D FOREIGN KEY (image_id) REFERENCES images (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE operation_logs DROP CONSTRAINT FK_DFFF9BCD44AC3583');
        $this->addSql('ALTER TABLE records DROP CONSTRAINT FK_9C9D58463DA5256D');
        $this->addSql('ALTER TABLE record_operations DROP CONSTRAINT FK_625167B9BF396750');
        $this->addSql('ALTER TABLE image_operations DROP CONSTRAINT FK_44CA447EBF396750');
        $this->addSql('ALTER TABLE image_operations DROP CONSTRAINT FK_44CA447E3DA5256D');
        $this->addSql('DROP TABLE operations');
        $this->addSql('DROP TABLE operation_logs');
        $this->addSql('DROP TABLE images');
        $this->addSql('DROP TABLE records');
        $this->addSql('DROP TABLE record_operations');
        $this->addSql('DROP TABLE image_operations');
        $this->addSql('DROP TABLE users');
    }

    public function isTransactional(): bool
    {
        return false;
    }
}
