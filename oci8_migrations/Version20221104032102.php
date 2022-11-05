<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221104032102 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql("ALTER SESSION SET NLS_LENGTH_SEMANTICS = 'CHAR'");
        $this->addSql('CREATE TABLE operations (id RAW(16) NOT NULL, status NUMBER(3) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_281453487B00651C ON operations (status)');
        $this->addSql('COMMENT ON COLUMN operations.id IS \'(DC2Type:ulid)\'');
        $this->addSql('COMMENT ON COLUMN operations.status IS \'(DC2Type:operation_status)\'');
        $this->addSql('CREATE TABLE operation_logs (id RAW(16) NOT NULL, operation_id RAW(16) NOT NULL, "level" NUMBER(3) NOT NULL, message CLOB NOT NULL, context CLOB DEFAULT \'{}\' NOT NULL, extra CLOB DEFAULT \'{}\' NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_DFFF9BCD44AC3583 ON operation_logs (operation_id)');
        $this->addSql('COMMENT ON COLUMN operation_logs.id IS \'(DC2Type:ulid)\'');
        $this->addSql('COMMENT ON COLUMN operation_logs.operation_id IS \'(DC2Type:ulid)\'');
        $this->addSql('COMMENT ON COLUMN operation_logs."level" IS \'(DC2Type:log_level)\'');
        $this->addSql('COMMENT ON COLUMN operation_logs.context IS \'(DC2Type:json)\'');
        $this->addSql('COMMENT ON COLUMN operation_logs.extra IS \'(DC2Type:json)\'');
        $this->addSql('CREATE TABLE record_operations (id RAW(16) NOT NULL, "date" DATE NOT NULL, market NUMBER(3) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_625167B9AA9E377A6BAC85CB ON record_operations ("date", market)');
        $this->addSql('COMMENT ON COLUMN record_operations.id IS \'(DC2Type:ulid)\'');
        $this->addSql('COMMENT ON COLUMN record_operations."date" IS \'(DC2Type:date_immutable)\'');
        $this->addSql('COMMENT ON COLUMN record_operations.market IS \'(DC2Type:bing_market)\'');
        $this->addSql('ALTER TABLE operation_logs ADD CONSTRAINT FK_DFFF9BCD44AC3583 FOREIGN KEY (operation_id) REFERENCES operations (id)');
        $this->addSql('ALTER TABLE record_operations ADD CONSTRAINT FK_625167B9BF396750 FOREIGN KEY (id) REFERENCES operations (id)');
        $this->addSql('ALTER TABLE IMAGES MODIFY (name VARCHAR2(500), urlbase VARCHAR2(500), copyright VARCHAR2(500))');
        $this->addSql('ALTER TABLE RECORDS MODIFY (keyword VARCHAR2(500), headline VARCHAR2(500))');
    }

    public function down(Schema $schema): void
    {
        $this->addSql("ALTER SESSION SET NLS_LENGTH_SEMANTICS = 'CHAR'");
        $this->addSql('ALTER TABLE operation_logs DROP CONSTRAINT FK_DFFF9BCD44AC3583');
        $this->addSql('ALTER TABLE record_operations DROP CONSTRAINT FK_625167B9BF396750');
        $this->addSql('DROP TABLE operations');
        $this->addSql('DROP TABLE operation_logs');
        $this->addSql('DROP TABLE record_operations');
        $this->addSql('ALTER TABLE records MODIFY (KEYWORD VARCHAR2(255), HEADLINE VARCHAR2(255))');
        $this->addSql('ALTER TABLE images MODIFY (NAME VARCHAR2(255), URLBASE VARCHAR2(255), COPYRIGHT VARCHAR2(255))');
    }

    public function isTransactional(): bool
    {
        return false;
    }
}
