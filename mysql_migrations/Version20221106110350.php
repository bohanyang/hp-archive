<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221106110350 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE image_operations (id BINARY(16) NOT NULL COMMENT \'(DC2Type:ulid)\', image_id BINARY(12) NOT NULL COMMENT \'(DC2Type:object_id)\', UNIQUE INDEX UNIQ_44CA447E3DA5256D (image_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE image_operations ADD CONSTRAINT FK_44CA447EBF396750 FOREIGN KEY (id) REFERENCES operations (id)');
        $this->addSql('ALTER TABLE image_operations ADD CONSTRAINT FK_44CA447E3DA5256D FOREIGN KEY (image_id) REFERENCES images (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE image_operations DROP FOREIGN KEY FK_44CA447EBF396750');
        $this->addSql('ALTER TABLE image_operations DROP FOREIGN KEY FK_44CA447E3DA5256D');
        $this->addSql('DROP TABLE image_operations');
    }

    public function isTransactional(): bool
    {
        return false;
    }
}
