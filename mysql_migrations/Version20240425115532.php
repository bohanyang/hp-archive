<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240425115532 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\MariaDB1052Platform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\MariaDB1052Platform'."
        );

        $this->addSql('CREATE TABLE images (id BINARY(12) NOT NULL, name LONGTEXT CHARACTER SET utf8 NOT NULL COLLATE `utf8_bin`, debut_on DATE NOT NULL, urlbase LONGTEXT CHARACTER SET utf8 NOT NULL COLLATE `utf8_bin`, copyright LONGTEXT CHARACTER SET utf8 NOT NULL COLLATE `utf8_bin`, downloadable TINYINT(1) NOT NULL, video JSON DEFAULT NULL, UNIQUE INDEX images_name_uindex (name), INDEX images_debut_on_id_index (debut_on, id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_bin` ENGINE = InnoDB COMMENT = \'\' ');
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\MariaDB1052Platform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\MariaDB1052Platform'."
        );

        $this->addSql('CREATE TABLE records (id BINARY(12) NOT NULL, image_id BINARY(12) NOT NULL, date DATE NOT NULL, market VARCHAR(0) CHARACTER SET utf8 NOT NULL COLLATE `utf8_bin`, title LONGTEXT CHARACTER SET utf8 NOT NULL COLLATE `utf8_bin`, keyword LONGTEXT CHARACTER SET utf8 DEFAULT NULL COLLATE `utf8_bin`, headline LONGTEXT CHARACTER SET utf8 DEFAULT NULL COLLATE `utf8_bin`, description LONGTEXT CHARACTER SET utf8 DEFAULT NULL COLLATE `utf8_bin`, quickfact LONGTEXT CHARACTER SET utf8 DEFAULT NULL COLLATE `utf8_bin`, hotspots JSON DEFAULT NULL, messages JSON DEFAULT NULL, coverstory JSON DEFAULT NULL, INDEX IDX_9C9D58463DA5256D (image_id), UNIQUE INDEX records_date_market_uindex (date, market), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_bin` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE records ADD CONSTRAINT records_images_id_fk FOREIGN KEY (image_id) REFERENCES images (id) ON UPDATE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\MariaDB1052Platform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\MariaDB1052Platform'."
        );

        $this->addSql('DROP TABLE images');
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\MariaDB1052Platform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\MariaDB1052Platform'."
        );

        $this->addSql('DROP TABLE records');
    }

    public function isTransactional(): bool
    {
        return false;
    }
}
