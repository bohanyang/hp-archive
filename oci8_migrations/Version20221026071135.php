<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221026071135 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql("ALTER SESSION SET NLS_LENGTH_SEMANTICS = 'BYTE'");
        $this->addSql('ALTER TABLE RECORDS MODIFY (title VARCHAR2(500), keyword VARCHAR2(255) DEFAULT NULL NULL, quickfact VARCHAR2(500))');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql("ALTER SESSION SET NLS_LENGTH_SEMANTICS = 'BYTE'");
        $this->addSql('ALTER TABLE records MODIFY (TITLE VARCHAR2(255), KEYWORD VARCHAR2(255) NOT NULL, QUICKFACT VARCHAR2(255))');
    }

    public function isTransactional(): bool
    {
        return false;
    }
}
