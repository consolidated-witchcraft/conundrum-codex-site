<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260214224034 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE ticket_type_format (id INT AUTO_INCREMENT NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE puzzle_instance CHANGE config config JSON NOT NULL');
        $this->addSql('ALTER TABLE ticket_type DROP FOREIGN KEY FK_BE0542114584665A');
        $this->addSql('DROP INDEX UNIQ_BE0542114584665A ON ticket_type');
        $this->addSql('ALTER TABLE ticket_type DROP product_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE ticket_type_format');
        $this->addSql('ALTER TABLE puzzle_instance CHANGE config config JSON NOT NULL COMMENT \'(DC2Type:json)\'');
        $this->addSql('ALTER TABLE ticket_type ADD product_id INT NOT NULL');
        $this->addSql('ALTER TABLE ticket_type ADD CONSTRAINT FK_BE0542114584665A FOREIGN KEY (product_id) REFERENCES product (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_BE0542114584665A ON ticket_type (product_id)');
    }
}
