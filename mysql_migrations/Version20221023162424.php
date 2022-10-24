<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221023162424 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE images (id BINARY(12) NOT NULL COMMENT \'(DC2Type:object_id)\', name VARCHAR(255) NOT NULL, debut_on DATE NOT NULL COMMENT \'(DC2Type:date_immutable)\', urlbase VARCHAR(255) NOT NULL, copyright VARCHAR(255) NOT NULL, downloadable TINYINT(1) NOT NULL, video VARCHAR(2000) DEFAULT NULL COMMENT \'(DC2Type:json_text)\', UNIQUE INDEX UNIQ_E01FBE6A5E237E06 (name), INDEX IDX_E01FBE6A57D1B6F5 (debut_on), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE records (id BINARY(12) NOT NULL COMMENT \'(DC2Type:object_id)\', image_id BINARY(12) NOT NULL COMMENT \'(DC2Type:object_id)\', date DATE NOT NULL COMMENT \'(DC2Type:date_immutable)\', market TINYINT UNSIGNED NOT NULL COMMENT \'(DC2Type:bing_market)\', title VARCHAR(255) NOT NULL, keyword VARCHAR(255) NOT NULL, headline VARCHAR(255) DEFAULT NULL, description VARCHAR(1000) DEFAULT NULL, quickfact VARCHAR(255) DEFAULT NULL, hotspots VARCHAR(2000) DEFAULT NULL COMMENT \'(DC2Type:json_text)\', messages VARCHAR(2000) DEFAULT NULL COMMENT \'(DC2Type:json_text)\', coverstory VARCHAR(2000) DEFAULT NULL COMMENT \'(DC2Type:json_text)\', UNIQUE INDEX UNIQ_9C9D5846AA9E377A6BAC85CB (date, market), INDEX IDX_9C9D58463DA5256D (image_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE records ADD CONSTRAINT FK_9C9D58463DA5256D FOREIGN KEY (image_id) REFERENCES images (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE records DROP FOREIGN KEY FK_9C9D58463DA5256D');
        $this->addSql('DROP TABLE images');
        $this->addSql('DROP TABLE records');
    }

    public function isTransactional(): bool
    {
        return false;
    }
}
