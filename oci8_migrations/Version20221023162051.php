<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221023162051 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql("ALTER SESSION SET NLS_LENGTH_SEMANTICS = 'CHAR'");
        $this->addSql('CREATE TABLE images (id RAW(12) NOT NULL, name VARCHAR2(255) NOT NULL, debut_on DATE NOT NULL, urlbase VARCHAR2(255) NOT NULL, copyright VARCHAR2(255) NOT NULL, downloadable NUMBER(1) NOT NULL, video VARCHAR2(2000) DEFAULT NULL NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_E01FBE6A5E237E06 ON images (name)');
        $this->addSql('CREATE INDEX IDX_E01FBE6A57D1B6F5 ON images (debut_on)');
        $this->addSql('COMMENT ON COLUMN images.id IS \'(DC2Type:object_id)\'');
        $this->addSql('COMMENT ON COLUMN images.debut_on IS \'(DC2Type:date_immutable)\'');
        $this->addSql('COMMENT ON COLUMN images.video IS \'(DC2Type:json_text)\'');
        $this->addSql('CREATE TABLE records (id RAW(12) NOT NULL, image_id RAW(12) NOT NULL, "date" DATE NOT NULL, market NUMBER(3) NOT NULL, title VARCHAR2(255) NOT NULL, keyword VARCHAR2(255) NOT NULL, headline VARCHAR2(255) DEFAULT NULL NULL, description VARCHAR2(1000) DEFAULT NULL NULL, quickfact VARCHAR2(255) DEFAULT NULL NULL, hotspots VARCHAR2(2000) DEFAULT NULL NULL, messages VARCHAR2(2000) DEFAULT NULL NULL, coverstory VARCHAR2(2000) DEFAULT NULL NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_9C9D5846AA9E377A6BAC85CB ON records ("date", market)');
        $this->addSql('CREATE INDEX IDX_9C9D58463DA5256D ON records (image_id)');
        $this->addSql('COMMENT ON COLUMN records.id IS \'(DC2Type:object_id)\'');
        $this->addSql('COMMENT ON COLUMN records.image_id IS \'(DC2Type:object_id)\'');
        $this->addSql('COMMENT ON COLUMN records."date" IS \'(DC2Type:date_immutable)\'');
        $this->addSql('COMMENT ON COLUMN records.market IS \'(DC2Type:bing_market)\'');
        $this->addSql('COMMENT ON COLUMN records.hotspots IS \'(DC2Type:json_text)\'');
        $this->addSql('COMMENT ON COLUMN records.messages IS \'(DC2Type:json_text)\'');
        $this->addSql('COMMENT ON COLUMN records.coverstory IS \'(DC2Type:json_text)\'');
        $this->addSql('ALTER TABLE records ADD CONSTRAINT FK_9C9D58463DA5256D FOREIGN KEY (image_id) REFERENCES images (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE records DROP CONSTRAINT FK_9C9D58463DA5256D');
        $this->addSql('DROP TABLE images');
        $this->addSql('DROP TABLE records');
    }

    public function isTransactional(): bool
    {
        return false;
    }
}
