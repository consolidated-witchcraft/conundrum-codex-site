<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260210194447 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE event ADD event_slug VARCHAR(255) NOT NULL, CHANGE invitation_only prebook_only TINYINT(1) NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_3BAE0AA78756D7C9 ON event (event_slug)');
        $this->addSql('ALTER TABLE puzzle_instance CHANGE config config JSON NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE puzzle_instance CHANGE config config JSON NOT NULL COMMENT \'(DC2Type:json)\'');
        $this->addSql('DROP INDEX UNIQ_3BAE0AA78756D7C9 ON event');
        $this->addSql('ALTER TABLE event DROP event_slug, CHANGE prebook_only invitation_only TINYINT(1) NOT NULL');
    }
}
