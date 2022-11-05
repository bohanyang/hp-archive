<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221104031607 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE operation_logs (id BINARY(16) NOT NULL COMMENT \'(DC2Type:ulid)\', operation_id BINARY(16) NOT NULL COMMENT \'(DC2Type:ulid)\', level TINYINT UNSIGNED NOT NULL COMMENT \'(DC2Type:log_level)\', message LONGTEXT NOT NULL, context LONGTEXT DEFAULT \'{}\' NOT NULL COMMENT \'(DC2Type:json)\', extra LONGTEXT DEFAULT \'{}\' NOT NULL COMMENT \'(DC2Type:json)\', INDEX IDX_DFFF9BCD44AC3583 (operation_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE operation_logs ADD CONSTRAINT FK_DFFF9BCD44AC3583 FOREIGN KEY (operation_id) REFERENCES operations (id)');
        $this->addSql('ALTER TABLE images CHANGE name name VARCHAR(500) NOT NULL, CHANGE urlbase urlbase VARCHAR(500) NOT NULL, CHANGE copyright copyright VARCHAR(500) NOT NULL');
        $this->addSql('ALTER TABLE records CHANGE keyword keyword VARCHAR(500) DEFAULT NULL, CHANGE headline headline VARCHAR(500) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE operation_logs DROP FOREIGN KEY FK_DFFF9BCD44AC3583');
        $this->addSql('DROP TABLE operation_logs');
        $this->addSql('ALTER TABLE images CHANGE name name VARCHAR(255) NOT NULL, CHANGE urlbase urlbase VARCHAR(255) NOT NULL, CHANGE copyright copyright VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE records CHANGE keyword keyword VARCHAR(255) DEFAULT NULL, CHANGE headline headline VARCHAR(255) DEFAULT NULL');
    }

    public function isTransactional(): bool
    {
        return false;
    }
}
