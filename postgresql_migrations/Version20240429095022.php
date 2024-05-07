<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240429095022 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE images (id BYTEA NOT NULL, name TEXT NOT NULL, debut_on DATE NOT NULL, urlbase TEXT NOT NULL, copyright TEXT NOT NULL, downloadable BOOLEAN NOT NULL, video JSON DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX images_name_uindex ON images (name)');
        $this->addSql('CREATE INDEX images_debut_on_id_index ON images (debut_on, id)');
        $this->addSql('CREATE TABLE records (id BYTEA NOT NULL, image_id BYTEA NOT NULL, date DATE NOT NULL, market TEXT NOT NULL, title TEXT NOT NULL, keyword TEXT DEFAULT NULL, headline TEXT DEFAULT NULL, description TEXT DEFAULT NULL, quickfact TEXT DEFAULT NULL, hotspots JSON DEFAULT NULL, messages JSON DEFAULT NULL, coverstory JSON DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX records_date_market_uindex ON records (date, market)');
        $this->addSql('CREATE INDEX IDX_9C9D58463DA5256D ON records (image_id)');
        $this->addSql('ALTER TABLE records ADD CONSTRAINT records_images_id_fk FOREIGN KEY (image_id) REFERENCES images (id) ON UPDATE CASCADE ON DELETE RESTRICT NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE records DROP CONSTRAINT records_images_id_fk');
        $this->addSql('DROP TABLE images');
        $this->addSql('DROP TABLE records');
    }

    public function isTransactional(): bool
    {
        return false;
    }
}
