<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221028074543 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE operations (id BINARY(16) NOT NULL COMMENT \'(DC2Type:ulid)\', status TINYINT UNSIGNED NOT NULL COMMENT \'(DC2Type:operation_status)\', INDEX IDX_281453487B00651C (status), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE record_operations (id BINARY(16) NOT NULL COMMENT \'(DC2Type:ulid)\', date DATE NOT NULL COMMENT \'(DC2Type:date_immutable)\', market TINYINT UNSIGNED NOT NULL COMMENT \'(DC2Type:bing_market)\', UNIQUE INDEX UNIQ_625167B9AA9E377A6BAC85CB (date, market), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE record_operations ADD CONSTRAINT FK_625167B9BF396750 FOREIGN KEY (id) REFERENCES operations (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE record_operations DROP FOREIGN KEY FK_625167B9BF396750');
        $this->addSql('DROP TABLE operations');
        $this->addSql('DROP TABLE record_operations');
    }

    public function isTransactional(): bool
    {
        return false;
    }
}
