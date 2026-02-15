<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260215221323 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE ticket_type_product_variant_map (id INT AUTO_INCREMENT NOT NULL, ticket_type_id INT DEFAULT NULL, product_variant_id INT DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_F4C521B3C980D5C1 (ticket_type_id), UNIQUE INDEX UNIQ_F4C521B3A80EF684 (product_variant_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE ticket_type_product_variant_map ADD CONSTRAINT FK_F4C521B3C980D5C1 FOREIGN KEY (ticket_type_id) REFERENCES ticket_type (id)');
        $this->addSql('ALTER TABLE ticket_type_product_variant_map ADD CONSTRAINT FK_F4C521B3A80EF684 FOREIGN KEY (product_variant_id) REFERENCES product_variant (id)');
        $this->addSql('ALTER TABLE ticket_type_format DROP FOREIGN KEY FK_91FD1F98C980D5C1');
        $this->addSql('ALTER TABLE ticket_type_format DROP FOREIGN KEY FK_91FD1F984584665A');
        $this->addSql('DROP TABLE ticket_type_format');
        $this->addSql('ALTER TABLE puzzle_instance CHANGE config config JSON NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE ticket_type_format (id INT AUTO_INCREMENT NOT NULL, ticket_type_id INT DEFAULT NULL, product_id INT DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', ticket_format VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, INDEX IDX_91FD1F98C980D5C1 (ticket_type_id), UNIQUE INDEX UNIQ_91FD1F984584665A (product_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE ticket_type_format ADD CONSTRAINT FK_91FD1F98C980D5C1 FOREIGN KEY (ticket_type_id) REFERENCES ticket_type (id)');
        $this->addSql('ALTER TABLE ticket_type_format ADD CONSTRAINT FK_91FD1F984584665A FOREIGN KEY (product_id) REFERENCES product (id)');
        $this->addSql('ALTER TABLE ticket_type_product_variant_map DROP FOREIGN KEY FK_F4C521B3C980D5C1');
        $this->addSql('ALTER TABLE ticket_type_product_variant_map DROP FOREIGN KEY FK_F4C521B3A80EF684');
        $this->addSql('DROP TABLE ticket_type_product_variant_map');
        $this->addSql('ALTER TABLE puzzle_instance CHANGE config config JSON NOT NULL COMMENT \'(DC2Type:json)\'');
    }
}
