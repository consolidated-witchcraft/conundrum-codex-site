<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260228150414 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE organisation ADD invoice_address_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE organisation ADD CONSTRAINT FK_E6E132B4C6BDFEB FOREIGN KEY (invoice_address_id) REFERENCES postal_address (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_E6E132B4C6BDFEB ON organisation (invoice_address_id)');
        $this->addSql('ALTER TABLE puzzle_instance CHANGE config config JSON NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE puzzle_instance CHANGE config config JSON NOT NULL COMMENT \'(DC2Type:json)\'');
        $this->addSql('ALTER TABLE organisation DROP FOREIGN KEY FK_E6E132B4C6BDFEB');
        $this->addSql('DROP INDEX UNIQ_E6E132B4C6BDFEB ON organisation');
        $this->addSql('ALTER TABLE organisation DROP invoice_address_id');
    }
}
