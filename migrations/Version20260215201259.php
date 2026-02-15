<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260215201259 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE product DROP FOREIGN KEY FK_D34A04ADC67D0B87');
        $this->addSql('CREATE TABLE product_variant (id INT AUTO_INCREMENT NOT NULL, created_by_id INT DEFAULT NULL, product_id INT NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', sku VARCHAR(255) NOT NULL, product_variant_name VARCHAR(255) NOT NULL, type VARCHAR(255) DEFAULT \'e_ticket\' NOT NULL, delta INT NOT NULL, pre_tax_price_centessimal INT NOT NULL, tax_exempt TINYINT(1) NOT NULL, requires_postage TINYINT(1) NOT NULL, publication_date DATETIME DEFAULT NULL, retired_date DATETIME DEFAULT NULL, UNIQUE INDEX UNIQ_209AA41DF9038C4 (sku), INDEX IDX_209AA41DB03A8386 (created_by_id), INDEX IDX_209AA41D4584665A (product_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE product_variant_managed_file (product_variant_id INT NOT NULL, managed_file_id INT NOT NULL, INDEX IDX_F97CF485A80EF684 (product_variant_id), INDEX IDX_F97CF485F2DC7781 (managed_file_id), PRIMARY KEY(product_variant_id, managed_file_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE product_variant_stock (id INT AUTO_INCREMENT NOT NULL, product_variant_id INT NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', stock_level INT NOT NULL, UNIQUE INDEX UNIQ_1151D5CFA80EF684 (product_variant_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE product_variant ADD CONSTRAINT FK_209AA41DB03A8386 FOREIGN KEY (created_by_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE product_variant ADD CONSTRAINT FK_209AA41D4584665A FOREIGN KEY (product_id) REFERENCES product (id)');
        $this->addSql('ALTER TABLE product_variant_managed_file ADD CONSTRAINT FK_F97CF485A80EF684 FOREIGN KEY (product_variant_id) REFERENCES product_variant (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE product_variant_managed_file ADD CONSTRAINT FK_F97CF485F2DC7781 FOREIGN KEY (managed_file_id) REFERENCES managed_file (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE product_variant_stock ADD CONSTRAINT FK_1151D5CFA80EF684 FOREIGN KEY (product_variant_id) REFERENCES product_variant (id)');
        $this->addSql('ALTER TABLE product_managed_file DROP FOREIGN KEY FK_D2BD57844584665A');
        $this->addSql('ALTER TABLE product_managed_file DROP FOREIGN KEY FK_D2BD5784F2DC7781');
        $this->addSql('DROP TABLE product_stock');
        $this->addSql('DROP TABLE product_managed_file');
        $this->addSql('ALTER TABLE order_product_line DROP FOREIGN KEY FK_583C023D4584665A');
        $this->addSql('DROP INDEX IDX_583C023D4584665A ON order_product_line');
        $this->addSql('ALTER TABLE order_product_line CHANGE product_id product_variant_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE order_product_line ADD CONSTRAINT FK_583C023DA80EF684 FOREIGN KEY (product_variant_id) REFERENCES product_variant (id)');
        $this->addSql('CREATE INDEX IDX_583C023DA80EF684 ON order_product_line (product_variant_id)');
        $this->addSql('ALTER TABLE product DROP FOREIGN KEY FK_D34A04ADB03A8386');
        $this->addSql('DROP INDEX UNIQ_D34A04ADF9038C4 ON product');
        $this->addSql('DROP INDEX IDX_D34A04ADB03A8386 ON product');
        $this->addSql('DROP INDEX IDX_D34A04ADC67D0B87 ON product');
        $this->addSql('ALTER TABLE product DROP created_by_id, DROP product_stock_id, DROP pre_tax_price_centessimal, DROP tax_exempt, DROP publication_date, DROP retired_date, DROP type, CHANGE sku base_sku VARCHAR(255) NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_D34A04AD65F6468E ON product (base_sku)');
        $this->addSql('ALTER TABLE puzzle_instance CHANGE config config JSON NOT NULL');
        $this->addSql('ALTER TABLE user_cart_line DROP FOREIGN KEY FK_3E11392B4584665A');
        $this->addSql('DROP INDEX IDX_3E11392B4584665A ON user_cart_line');
        $this->addSql('ALTER TABLE user_cart_line CHANGE product_id product_variant_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE user_cart_line ADD CONSTRAINT FK_3E11392BA80EF684 FOREIGN KEY (product_variant_id) REFERENCES product_variant (id)');
        $this->addSql('CREATE INDEX IDX_3E11392BA80EF684 ON user_cart_line (product_variant_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE order_product_line DROP FOREIGN KEY FK_583C023DA80EF684');
        $this->addSql('ALTER TABLE user_cart_line DROP FOREIGN KEY FK_3E11392BA80EF684');
        $this->addSql('CREATE TABLE product_stock (id INT AUTO_INCREMENT NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', initial_stock_level INT NOT NULL, current_stock_level INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE product_managed_file (product_id INT NOT NULL, managed_file_id INT NOT NULL, INDEX IDX_D2BD5784F2DC7781 (managed_file_id), INDEX IDX_D2BD57844584665A (product_id), PRIMARY KEY(product_id, managed_file_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE product_managed_file ADD CONSTRAINT FK_D2BD57844584665A FOREIGN KEY (product_id) REFERENCES product (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE product_managed_file ADD CONSTRAINT FK_D2BD5784F2DC7781 FOREIGN KEY (managed_file_id) REFERENCES managed_file (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE product_variant DROP FOREIGN KEY FK_209AA41DB03A8386');
        $this->addSql('ALTER TABLE product_variant DROP FOREIGN KEY FK_209AA41D4584665A');
        $this->addSql('ALTER TABLE product_variant_managed_file DROP FOREIGN KEY FK_F97CF485A80EF684');
        $this->addSql('ALTER TABLE product_variant_managed_file DROP FOREIGN KEY FK_F97CF485F2DC7781');
        $this->addSql('ALTER TABLE product_variant_stock DROP FOREIGN KEY FK_1151D5CFA80EF684');
        $this->addSql('DROP TABLE product_variant');
        $this->addSql('DROP TABLE product_variant_managed_file');
        $this->addSql('DROP TABLE product_variant_stock');
        $this->addSql('ALTER TABLE puzzle_instance CHANGE config config JSON NOT NULL COMMENT \'(DC2Type:json)\'');
        $this->addSql('DROP INDEX UNIQ_D34A04AD65F6468E ON product');
        $this->addSql('ALTER TABLE product ADD created_by_id INT DEFAULT NULL, ADD product_stock_id INT NOT NULL, ADD pre_tax_price_centessimal INT NOT NULL, ADD tax_exempt TINYINT(1) NOT NULL, ADD publication_date DATETIME DEFAULT NULL, ADD retired_date DATETIME DEFAULT NULL, ADD type VARCHAR(255) DEFAULT \'e_ticket\' NOT NULL, CHANGE base_sku sku VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE product ADD CONSTRAINT FK_D34A04ADB03A8386 FOREIGN KEY (created_by_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE product ADD CONSTRAINT FK_D34A04ADC67D0B87 FOREIGN KEY (product_stock_id) REFERENCES product_stock (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_D34A04ADF9038C4 ON product (sku)');
        $this->addSql('CREATE INDEX IDX_D34A04ADB03A8386 ON product (created_by_id)');
        $this->addSql('CREATE INDEX IDX_D34A04ADC67D0B87 ON product (product_stock_id)');
        $this->addSql('DROP INDEX IDX_583C023DA80EF684 ON order_product_line');
        $this->addSql('ALTER TABLE order_product_line CHANGE product_variant_id product_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE order_product_line ADD CONSTRAINT FK_583C023D4584665A FOREIGN KEY (product_id) REFERENCES product (id)');
        $this->addSql('CREATE INDEX IDX_583C023D4584665A ON order_product_line (product_id)');
        $this->addSql('DROP INDEX IDX_3E11392BA80EF684 ON user_cart_line');
        $this->addSql('ALTER TABLE user_cart_line CHANGE product_variant_id product_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE user_cart_line ADD CONSTRAINT FK_3E11392B4584665A FOREIGN KEY (product_id) REFERENCES product (id)');
        $this->addSql('CREATE INDEX IDX_3E11392B4584665A ON user_cart_line (product_id)');
    }
}
