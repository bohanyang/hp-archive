<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221026151135 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql("ALTER SESSION SET NLS_LENGTH_SEMANTICS = 'CHAR'");
        $this->addSql('ALTER TABLE RECORDS MODIFY (title VARCHAR2(500), keyword VARCHAR2(255), quickfact VARCHAR2(500))');
    }

    public function down(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql("ALTER SESSION SET NLS_LENGTH_SEMANTICS = 'BYTE'");
        $this->addSql('ALTER TABLE RECORDS MODIFY (title VARCHAR2(500), keyword VARCHAR2(255), quickfact VARCHAR2(500))');
    }

    public function isTransactional(): bool
    {
        return false;
    }
}
