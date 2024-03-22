<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240322070612 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE images ALTER name TYPE TEXT');
        $this->addSql('ALTER TABLE images ALTER name TYPE TEXT');
        $this->addSql('ALTER TABLE images ALTER urlbase TYPE TEXT');
        $this->addSql('ALTER TABLE images ALTER urlbase TYPE TEXT');
        $this->addSql('ALTER TABLE images ALTER copyright TYPE TEXT');
        $this->addSql('ALTER TABLE images ALTER copyright TYPE TEXT');
        $this->addSql('ALTER TABLE images ALTER video DROP DEFAULT');
        $this->addSql('ALTER TABLE images ALTER video TYPE JSON USING video::json');
        $this->addSql('COMMENT ON COLUMN images.video IS NULL');
        $this->addSql('ALTER TABLE records ALTER title TYPE TEXT');
        $this->addSql('ALTER TABLE records ALTER title TYPE TEXT');
        $this->addSql('ALTER TABLE records ALTER keyword TYPE TEXT');
        $this->addSql('ALTER TABLE records ALTER keyword TYPE TEXT');
        $this->addSql('ALTER TABLE records ALTER headline TYPE TEXT');
        $this->addSql('ALTER TABLE records ALTER headline TYPE TEXT');
        $this->addSql('ALTER TABLE records ALTER description TYPE TEXT');
        $this->addSql('ALTER TABLE records ALTER description TYPE TEXT');
        $this->addSql('ALTER TABLE records ALTER quickfact TYPE TEXT');
        $this->addSql('ALTER TABLE records ALTER quickfact TYPE TEXT');
        $this->addSql('ALTER TABLE records ALTER hotspots DROP DEFAULT');
        $this->addSql('ALTER TABLE records ALTER hotspots TYPE JSON USING hotspots::json');
        $this->addSql('ALTER TABLE records ALTER messages DROP DEFAULT');
        $this->addSql('ALTER TABLE records ALTER messages TYPE JSON USING messages::json');
        $this->addSql('ALTER TABLE records ALTER coverstory DROP DEFAULT');
        $this->addSql('ALTER TABLE records ALTER coverstory TYPE JSON USING coverstory::json');
        $this->addSql('COMMENT ON COLUMN records.hotspots IS NULL');
        $this->addSql('COMMENT ON COLUMN records.messages IS NULL');
        $this->addSql('COMMENT ON COLUMN records.coverstory IS NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE records ALTER title TYPE VARCHAR(500)');
        $this->addSql('ALTER TABLE records ALTER keyword TYPE VARCHAR(500)');
        $this->addSql('ALTER TABLE records ALTER headline TYPE VARCHAR(500)');
        $this->addSql('ALTER TABLE records ALTER description TYPE VARCHAR(1000)');
        $this->addSql('ALTER TABLE records ALTER quickfact TYPE VARCHAR(500)');
        $this->addSql('ALTER TABLE records ALTER hotspots TYPE VARCHAR(2000)');
        $this->addSql('ALTER TABLE records ALTER messages TYPE VARCHAR(2000)');
        $this->addSql('ALTER TABLE records ALTER coverstory TYPE VARCHAR(2000)');
        $this->addSql('COMMENT ON COLUMN records.hotspots IS \'(DC2Type:json_text)\'');
        $this->addSql('COMMENT ON COLUMN records.messages IS \'(DC2Type:json_text)\'');
        $this->addSql('COMMENT ON COLUMN records.coverstory IS \'(DC2Type:json_text)\'');
        $this->addSql('ALTER TABLE images ALTER name TYPE VARCHAR(500)');
        $this->addSql('ALTER TABLE images ALTER urlbase TYPE VARCHAR(500)');
        $this->addSql('ALTER TABLE images ALTER copyright TYPE VARCHAR(500)');
        $this->addSql('ALTER TABLE images ALTER video TYPE VARCHAR(2000)');
        $this->addSql('COMMENT ON COLUMN images.video IS \'(DC2Type:json_text)\'');
    }

    public function isTransactional(): bool
    {
        return false;
    }
}
