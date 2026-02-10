<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260210191911 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE event ADD hero_image_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE event ADD CONSTRAINT FK_3BAE0AA798BB94C5 FOREIGN KEY (hero_image_id) REFERENCES managed_file (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_3BAE0AA798BB94C5 ON event (hero_image_id)');
        $this->addSql('ALTER TABLE puzzle_instance CHANGE config config JSON NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE puzzle_instance CHANGE config config JSON NOT NULL COMMENT \'(DC2Type:json)\'');
        $this->addSql('ALTER TABLE event DROP FOREIGN KEY FK_3BAE0AA798BB94C5');
        $this->addSql('DROP INDEX UNIQ_3BAE0AA798BB94C5 ON event');
        $this->addSql('ALTER TABLE event DROP hero_image_id');
    }
}
