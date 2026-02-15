<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260215202143 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE product_variant ADD product_variant_stock_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE product_variant ADD CONSTRAINT FK_209AA41D1C914464 FOREIGN KEY (product_variant_stock_id) REFERENCES product_variant_stock (id)');
        $this->addSql('CREATE INDEX IDX_209AA41D1C914464 ON product_variant (product_variant_stock_id)');
        $this->addSql('ALTER TABLE product_variant_stock DROP FOREIGN KEY FK_1151D5CFA80EF684');
        $this->addSql('DROP INDEX UNIQ_1151D5CFA80EF684 ON product_variant_stock');
        $this->addSql('ALTER TABLE product_variant_stock DROP product_variant_id');
        $this->addSql('ALTER TABLE puzzle_instance CHANGE config config JSON NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE puzzle_instance CHANGE config config JSON NOT NULL COMMENT \'(DC2Type:json)\'');
        $this->addSql('ALTER TABLE product_variant_stock ADD product_variant_id INT NOT NULL');
        $this->addSql('ALTER TABLE product_variant_stock ADD CONSTRAINT FK_1151D5CFA80EF684 FOREIGN KEY (product_variant_id) REFERENCES product_variant (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_1151D5CFA80EF684 ON product_variant_stock (product_variant_id)');
        $this->addSql('ALTER TABLE product_variant DROP FOREIGN KEY FK_209AA41D1C914464');
        $this->addSql('DROP INDEX IDX_209AA41D1C914464 ON product_variant');
        $this->addSql('ALTER TABLE product_variant DROP product_variant_stock_id');
    }
}
