<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221106130129 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE image_operations (id RAW(16) NOT NULL, image_id RAW(12) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_44CA447E3DA5256D ON image_operations (image_id)');
        $this->addSql('COMMENT ON COLUMN image_operations.id IS \'(DC2Type:ulid)\'');
        $this->addSql('COMMENT ON COLUMN image_operations.image_id IS \'(DC2Type:object_id)\'');
        $this->addSql('ALTER TABLE image_operations ADD CONSTRAINT FK_44CA447EBF396750 FOREIGN KEY (id) REFERENCES operations (id)');
        $this->addSql('ALTER TABLE image_operations ADD CONSTRAINT FK_44CA447E3DA5256D FOREIGN KEY (image_id) REFERENCES images (id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE image_operations DROP CONSTRAINT FK_44CA447EBF396750');
        $this->addSql('ALTER TABLE image_operations DROP CONSTRAINT FK_44CA447E3DA5256D');
        $this->addSql('DROP TABLE image_operations');
    }

    public function isTransactional(): bool
    {
        return false;
    }
}
