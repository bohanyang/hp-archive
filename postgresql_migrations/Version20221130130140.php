<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221130130140 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX idx_e01fbe6a57d1b6f5');
        $this->addSql('CREATE INDEX IDX_E01FBE6A57D1B6F5BF396750 ON images (debut_on, id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP INDEX IDX_E01FBE6A57D1B6F5BF396750');
        $this->addSql('CREATE INDEX idx_e01fbe6a57d1b6f5 ON images (debut_on)');
    }

    public function isTransactional(): bool
    {
        return false;
    }
}
