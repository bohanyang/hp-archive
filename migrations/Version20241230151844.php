<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241230151844 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE images (id BLOB NOT NULL, name VARCHAR(255) NOT NULL, debut_on DATE NOT NULL, urlbase VARCHAR(255) NOT NULL, copyright CLOB NOT NULL, downloadable BOOLEAN NOT NULL, video CLOB DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX images_name_uindex ON images (name)');
        $this->addSql('CREATE INDEX images_debut_on_id_index ON images (debut_on, id)');
        $this->addSql('CREATE TABLE records (id BLOB NOT NULL, image_id BLOB NOT NULL, date DATE NOT NULL, market INTEGER NOT NULL, title CLOB NOT NULL, keyword CLOB DEFAULT NULL, headline CLOB DEFAULT NULL, description CLOB DEFAULT NULL, quickfact CLOB DEFAULT NULL, hotspots CLOB DEFAULT NULL, messages CLOB DEFAULT NULL, coverstory CLOB DEFAULT NULL, PRIMARY KEY(id), CONSTRAINT records_images_id_fk FOREIGN KEY (image_id) REFERENCES images (id) ON UPDATE CASCADE ON DELETE RESTRICT NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE UNIQUE INDEX records_date_market_uindex ON records (date, market)');
        $this->addSql('CREATE INDEX records_image_id_index ON records (image_id)');
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
