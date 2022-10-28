<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221026082904 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE images (id BLOB NOT NULL --(DC2Type:object_id)
        , name VARCHAR(255) NOT NULL, debut_on DATE NOT NULL --(DC2Type:date_immutable)
        , urlbase VARCHAR(255) NOT NULL, copyright VARCHAR(255) NOT NULL, downloadable BOOLEAN NOT NULL, video VARCHAR(2000) DEFAULT NULL --(DC2Type:json_text)
        , PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_E01FBE6A5E237E06 ON images (name)');
        $this->addSql('CREATE INDEX IDX_E01FBE6A57D1B6F5 ON images (debut_on)');
        $this->addSql('CREATE TABLE records (id BLOB NOT NULL --(DC2Type:object_id)
        , image_id BLOB NOT NULL --(DC2Type:object_id)
        , date DATE NOT NULL --(DC2Type:date_immutable)
        , market SMALLINT UNSIGNED NOT NULL --(DC2Type:bing_market)
        , title VARCHAR(500) NOT NULL, keyword VARCHAR(255) DEFAULT NULL, headline VARCHAR(255) DEFAULT NULL, description VARCHAR(1000) DEFAULT NULL, quickfact VARCHAR(500) DEFAULT NULL, hotspots VARCHAR(2000) DEFAULT NULL --(DC2Type:json_text)
        , messages VARCHAR(2000) DEFAULT NULL --(DC2Type:json_text)
        , coverstory VARCHAR(2000) DEFAULT NULL --(DC2Type:json_text)
        , PRIMARY KEY(id), CONSTRAINT FK_9C9D58463DA5256D FOREIGN KEY (image_id) REFERENCES images (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_9C9D5846AA9E377A6BAC85CB ON records (date, market)');
        $this->addSql('CREATE INDEX IDX_9C9D58463DA5256D ON records (image_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE images');
        $this->addSql('DROP TABLE records');
    }

    public function isTransactional(): bool
    {
        return false;
    }
}
