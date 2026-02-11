<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260211135605 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE organisation ADD organisation_logo_id INT NOT NULL');
        $this->addSql('ALTER TABLE organisation ADD CONSTRAINT FK_E6E132B49A0B6349 FOREIGN KEY (organisation_logo_id) REFERENCES managed_file (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_E6E132B49A0B6349 ON organisation (organisation_logo_id)');
        $this->addSql('ALTER TABLE puzzle_instance CHANGE config config JSON NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE puzzle_instance CHANGE config config JSON NOT NULL COMMENT \'(DC2Type:json)\'');
        $this->addSql('ALTER TABLE organisation DROP FOREIGN KEY FK_E6E132B49A0B6349');
        $this->addSql('DROP INDEX UNIQ_E6E132B49A0B6349 ON organisation');
        $this->addSql('ALTER TABLE organisation DROP organisation_logo_id');
    }
}
