<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260210210533 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE event DROP FOREIGN KEY FK_3BAE0AA7BDBCFD6');
        $this->addSql('DROP INDEX UNIQ_3BAE0AA7BDBCFD6 ON event');
        $this->addSql('ALTER TABLE event DROP ticket_types_id');
        $this->addSql('ALTER TABLE puzzle_instance CHANGE config config JSON NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE puzzle_instance CHANGE config config JSON NOT NULL COMMENT \'(DC2Type:json)\'');
        $this->addSql('ALTER TABLE event ADD ticket_types_id INT NOT NULL');
        $this->addSql('ALTER TABLE event ADD CONSTRAINT FK_3BAE0AA7BDBCFD6 FOREIGN KEY (ticket_types_id) REFERENCES ticket_type (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_3BAE0AA7BDBCFD6 ON event (ticket_types_id)');
    }
}
