<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260214225626 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE puzzle_instance CHANGE config config JSON NOT NULL');
        $this->addSql('ALTER TABLE ticket_type_format ADD ticket_type_id INT DEFAULT NULL, ADD product_id INT DEFAULT NULL, ADD name VARCHAR(255) NOT NULL, ADD slug VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE ticket_type_format ADD CONSTRAINT FK_91FD1F98C980D5C1 FOREIGN KEY (ticket_type_id) REFERENCES ticket_type (id)');
        $this->addSql('ALTER TABLE ticket_type_format ADD CONSTRAINT FK_91FD1F984584665A FOREIGN KEY (product_id) REFERENCES product (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_91FD1F98989D9B62 ON ticket_type_format (slug)');
        $this->addSql('CREATE INDEX IDX_91FD1F98C980D5C1 ON ticket_type_format (ticket_type_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_91FD1F984584665A ON ticket_type_format (product_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE puzzle_instance CHANGE config config JSON NOT NULL COMMENT \'(DC2Type:json)\'');
        $this->addSql('ALTER TABLE ticket_type_format DROP FOREIGN KEY FK_91FD1F98C980D5C1');
        $this->addSql('ALTER TABLE ticket_type_format DROP FOREIGN KEY FK_91FD1F984584665A');
        $this->addSql('DROP INDEX UNIQ_91FD1F98989D9B62 ON ticket_type_format');
        $this->addSql('DROP INDEX IDX_91FD1F98C980D5C1 ON ticket_type_format');
        $this->addSql('DROP INDEX UNIQ_91FD1F984584665A ON ticket_type_format');
        $this->addSql('ALTER TABLE ticket_type_format DROP ticket_type_id, DROP product_id, DROP name, DROP slug');
    }
}
