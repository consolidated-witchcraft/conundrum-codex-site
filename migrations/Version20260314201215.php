<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260314201215 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE world_calendar_day_of_week (id INT AUTO_INCREMENT NOT NULL, world_calendar_settings_id INT NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', name VARCHAR(255) NOT NULL, abbreviation VARCHAR(3) NOT NULL, INDEX IDX_9254F5A4CD67FC5D (world_calendar_settings_id), UNIQUE INDEX world_calendar_day_of_week_settings_name_unique (world_calendar_settings_id, name), UNIQUE INDEX world_calendar_day_of_week_settings_abbreviation_unique (world_calendar_settings_id, abbreviation), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE world_calendar_month (id INT AUTO_INCREMENT NOT NULL, world_calendar_settings_id INT NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', name VARCHAR(255) NOT NULL, slug VARCHAR(255) NOT NULL, delta INT NOT NULL, number_of_days INT NOT NULL, INDEX IDX_27392640CD67FC5D (world_calendar_settings_id), UNIQUE INDEX world_calendar_month_slug_unique (slug), UNIQUE INDEX world_calendar_month_settings_delta_unique (world_calendar_settings_id, delta), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE world_calendar_settings (id INT AUTO_INCREMENT NOT NULL, world_id INT NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', hours_in_day INT DEFAULT 24 NOT NULL, UNIQUE INDEX UNIQ_65495EDA8925311C (world_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE world_event (id INT AUTO_INCREMENT NOT NULL, world_id INT NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', timestamp BIGINT NOT NULL, title VARCHAR(255) NOT NULL, description VARCHAR(5000) NOT NULL, INDEX IDX_BACED8A88925311C (world_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE world_calendar_day_of_week ADD CONSTRAINT FK_9254F5A4CD67FC5D FOREIGN KEY (world_calendar_settings_id) REFERENCES world_calendar_settings (id)');
        $this->addSql('ALTER TABLE world_calendar_month ADD CONSTRAINT FK_27392640CD67FC5D FOREIGN KEY (world_calendar_settings_id) REFERENCES world_calendar_settings (id)');
        $this->addSql('ALTER TABLE world_calendar_settings ADD CONSTRAINT FK_65495EDA8925311C FOREIGN KEY (world_id) REFERENCES world (id)');
        $this->addSql('ALTER TABLE world_event ADD CONSTRAINT FK_BACED8A88925311C FOREIGN KEY (world_id) REFERENCES world (id)');
        $this->addSql('ALTER TABLE puzzle_instance CHANGE config config JSON NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE world_calendar_day_of_week DROP FOREIGN KEY FK_9254F5A4CD67FC5D');
        $this->addSql('ALTER TABLE world_calendar_month DROP FOREIGN KEY FK_27392640CD67FC5D');
        $this->addSql('ALTER TABLE world_calendar_settings DROP FOREIGN KEY FK_65495EDA8925311C');
        $this->addSql('ALTER TABLE world_event DROP FOREIGN KEY FK_BACED8A88925311C');
        $this->addSql('DROP TABLE world_calendar_day_of_week');
        $this->addSql('DROP TABLE world_calendar_month');
        $this->addSql('DROP TABLE world_calendar_settings');
        $this->addSql('DROP TABLE world_event');
        $this->addSql('ALTER TABLE puzzle_instance CHANGE config config JSON NOT NULL COMMENT \'(DC2Type:json)\'');
    }
}
