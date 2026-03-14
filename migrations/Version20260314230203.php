<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260314230203 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE puzzle_instance CHANGE config config JSON NOT NULL');
        $this->addSql('DROP INDEX world_calendar_month_slug_unique ON world_calendar_month');
        $this->addSql('ALTER TABLE world_calendar_month ADD abbreviation VARCHAR(3) NOT NULL, DROP slug');
        $this->addSql('CREATE UNIQUE INDEX world_calendar_month_abbreviation_unique ON world_calendar_month (abbreviation)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE puzzle_instance CHANGE config config JSON NOT NULL COMMENT \'(DC2Type:json)\'');
        $this->addSql('DROP INDEX world_calendar_month_abbreviation_unique ON world_calendar_month');
        $this->addSql('ALTER TABLE world_calendar_month ADD slug VARCHAR(255) NOT NULL, DROP abbreviation');
        $this->addSql('CREATE UNIQUE INDEX world_calendar_month_slug_unique ON world_calendar_month (slug)');
    }
}
