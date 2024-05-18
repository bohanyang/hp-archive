<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240518064844 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE images (id BINARY(12) NOT NULL, name VARCHAR(255) CHARACTER SET latin1 NOT NULL COLLATE `latin1_bin`, debut_on DATE NOT NULL, urlbase VARCHAR(255) CHARACTER SET latin1 NOT NULL COLLATE `latin1_bin`, copyright TEXT NOT NULL, downloadable TINYINT(1) NOT NULL, video JSON DEFAULT NULL, UNIQUE INDEX images_name_uindex (name), INDEX images_debut_on_id_index (debut_on, id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE records (id BINARY(12) NOT NULL, image_id BINARY(12) NOT NULL, date DATE NOT NULL, market ENUM(\'ROW\', \'en-US\', \'en-AU\', \'pt-BR\', \'en-CA\', \'fr-CA\', \'en-GB\', \'fr-FR\', \'it-IT\', \'es-ES\', \'de-DE\', \'en-IN\', \'zh-CN\', \'ja-JP\') NOT NULL, title TEXT NOT NULL, keyword TEXT DEFAULT NULL, headline TEXT DEFAULT NULL, description TEXT DEFAULT NULL, quickfact TEXT DEFAULT NULL, hotspots JSON DEFAULT NULL, messages JSON DEFAULT NULL, coverstory JSON DEFAULT NULL, UNIQUE INDEX records_date_market_uindex (date, market), INDEX records_image_id_index (image_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE records ADD CONSTRAINT records_images_id_fk FOREIGN KEY (image_id) REFERENCES images (id) ON UPDATE RESTRICT ON DELETE RESTRICT');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE records DROP FOREIGN KEY records_images_id_fk');
        $this->addSql('DROP TABLE images');
        $this->addSql('DROP TABLE records');
    }

    public function isTransactional(): bool
    {
        return false;
    }
}
