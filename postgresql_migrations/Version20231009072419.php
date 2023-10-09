<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231009072419 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs

        $this->addSql('alter table images rename constraint images_pkey to images_pk;');
        $this->addSql('alter table records rename constraint records_pkey to records_pk;');
        $this->addSql('alter table records drop constraint fk_9c9d58463da5256d;');
        $this->addSql('alter table records add constraint records_images_id_fk foreign key (image_id) references images on update cascade on delete restrict;');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs

    }

    public function isTransactional(): bool
    {
        return false;
    }
}
