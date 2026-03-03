<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260303110709 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE product DROP FOREIGN KEY FK_D34A04ADC67D0B87');
        $this->addSql('CREATE TABLE order_product_line (id INT AUTO_INCREMENT NOT NULL, order_id INT DEFAULT NULL, product_variant_id INT DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', quantity INT NOT NULL, INDEX IDX_583C023D8D9F6D38 (order_id), INDEX IDX_583C023DA80EF684 (product_variant_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE order_tax (id INT AUTO_INCREMENT NOT NULL, order_id INT DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', tax_rate_public_reference VARCHAR(255) NOT NULL, tax_rate_name VARCHAR(255) NOT NULL, tax_rate_handle VARCHAR(255) NOT NULL, amount_centessimal INT NOT NULL, INDEX IDX_CDDAF5168D9F6D38 (order_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE payment (id INT AUTO_INCREMENT NOT NULL, order_id INT DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', name VARCHAR(255) NOT NULL, payment_amount_centessimal INT NOT NULL, payment_reference VARCHAR(255) NOT NULL, payment_status VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_6D28840DED8A663B (payment_reference), INDEX IDX_6D28840D8D9F6D38 (order_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE payment_receipt (id INT AUTO_INCREMENT NOT NULL, payment_id INT DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', receipt_reference VARCHAR(255) NOT NULL, amount_centessimal INT NOT NULL, receipt_date DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', UNIQUE INDEX UNIQ_4AD0B8AF8220C10D (receipt_reference), INDEX IDX_4AD0B8AF4C3A3BB (payment_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE product_variant (id INT AUTO_INCREMENT NOT NULL, created_by_id INT DEFAULT NULL, product_variant_stock_id INT DEFAULT NULL, product_id INT NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', sku VARCHAR(255) NOT NULL, product_variant_name VARCHAR(255) NOT NULL, type VARCHAR(255) DEFAULT \'e_ticket\' NOT NULL, delta INT NOT NULL, pre_tax_price_centessimal INT NOT NULL, tax_exempt TINYINT(1) NOT NULL, requires_postage TINYINT(1) NOT NULL, publication_date DATETIME DEFAULT NULL, retired_date DATETIME DEFAULT NULL, UNIQUE INDEX UNIQ_209AA41DF9038C4 (sku), INDEX IDX_209AA41DB03A8386 (created_by_id), INDEX IDX_209AA41D1C914464 (product_variant_stock_id), INDEX IDX_209AA41D4584665A (product_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE product_variant_managed_file (product_variant_id INT NOT NULL, managed_file_id INT NOT NULL, INDEX IDX_F97CF485A80EF684 (product_variant_id), INDEX IDX_F97CF485F2DC7781 (managed_file_id), PRIMARY KEY(product_variant_id, managed_file_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE product_variant_stock (id INT AUTO_INCREMENT NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', stock_level INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE ticket_type_product_variant_map (id INT AUTO_INCREMENT NOT NULL, ticket_type_id INT DEFAULT NULL, product_variant_id INT DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_F4C521B3C980D5C1 (ticket_type_id), UNIQUE INDEX UNIQ_F4C521B3A80EF684 (product_variant_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_cart_charge (id INT AUTO_INCREMENT NOT NULL, user_cart_id INT DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', charge_type VARCHAR(255) NOT NULL, pre_tax_amount_centessimal INT NOT NULL, INDEX IDX_F2F40FFE42D8D3B5 (user_cart_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_cart_product_line (id INT AUTO_INCREMENT NOT NULL, user_cart_id INT DEFAULT NULL, product_variant_id INT DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', quantity INT NOT NULL, INDEX IDX_D26CBF1542D8D3B5 (user_cart_id), INDEX IDX_D26CBF15A80EF684 (product_variant_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE order_product_line ADD CONSTRAINT FK_583C023D8D9F6D38 FOREIGN KEY (order_id) REFERENCES orders (id)');
        $this->addSql('ALTER TABLE order_product_line ADD CONSTRAINT FK_583C023DA80EF684 FOREIGN KEY (product_variant_id) REFERENCES product_variant (id)');
        $this->addSql('ALTER TABLE order_tax ADD CONSTRAINT FK_CDDAF5168D9F6D38 FOREIGN KEY (order_id) REFERENCES orders (id)');
        $this->addSql('ALTER TABLE payment ADD CONSTRAINT FK_6D28840D8D9F6D38 FOREIGN KEY (order_id) REFERENCES orders (id)');
        $this->addSql('ALTER TABLE payment_receipt ADD CONSTRAINT FK_4AD0B8AF4C3A3BB FOREIGN KEY (payment_id) REFERENCES payment (id)');
        $this->addSql('ALTER TABLE product_variant ADD CONSTRAINT FK_209AA41DB03A8386 FOREIGN KEY (created_by_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE product_variant ADD CONSTRAINT FK_209AA41D1C914464 FOREIGN KEY (product_variant_stock_id) REFERENCES product_variant_stock (id)');
        $this->addSql('ALTER TABLE product_variant ADD CONSTRAINT FK_209AA41D4584665A FOREIGN KEY (product_id) REFERENCES product (id)');
        $this->addSql('ALTER TABLE product_variant_managed_file ADD CONSTRAINT FK_F97CF485A80EF684 FOREIGN KEY (product_variant_id) REFERENCES product_variant (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE product_variant_managed_file ADD CONSTRAINT FK_F97CF485F2DC7781 FOREIGN KEY (managed_file_id) REFERENCES managed_file (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE ticket_type_product_variant_map ADD CONSTRAINT FK_F4C521B3C980D5C1 FOREIGN KEY (ticket_type_id) REFERENCES ticket_type (id)');
        $this->addSql('ALTER TABLE ticket_type_product_variant_map ADD CONSTRAINT FK_F4C521B3A80EF684 FOREIGN KEY (product_variant_id) REFERENCES product_variant (id)');
        $this->addSql('ALTER TABLE user_cart_charge ADD CONSTRAINT FK_F2F40FFE42D8D3B5 FOREIGN KEY (user_cart_id) REFERENCES user_cart (id)');
        $this->addSql('ALTER TABLE user_cart_product_line ADD CONSTRAINT FK_D26CBF1542D8D3B5 FOREIGN KEY (user_cart_id) REFERENCES user_cart (id)');
        $this->addSql('ALTER TABLE user_cart_product_line ADD CONSTRAINT FK_D26CBF15A80EF684 FOREIGN KEY (product_variant_id) REFERENCES product_variant (id)');
        $this->addSql('ALTER TABLE order_payment DROP FOREIGN KEY FK_9B522D468D9F6D38');
        $this->addSql('ALTER TABLE product_order_line DROP FOREIGN KEY FK_F82D1CA4584665A');
        $this->addSql('ALTER TABLE product_order_line DROP FOREIGN KEY FK_F82D1CA8D9F6D38');
        $this->addSql('ALTER TABLE user_cart_line DROP FOREIGN KEY FK_3E11392B42D8D3B5');
        $this->addSql('ALTER TABLE user_cart_line DROP FOREIGN KEY FK_3E11392B4584665A');
        $this->addSql('ALTER TABLE product_managed_file DROP FOREIGN KEY FK_D2BD57844584665A');
        $this->addSql('ALTER TABLE product_managed_file DROP FOREIGN KEY FK_D2BD5784F2DC7781');
        $this->addSql('DROP TABLE order_payment');
        $this->addSql('DROP TABLE product_order_line');
        $this->addSql('DROP TABLE product_stock');
        $this->addSql('DROP TABLE user_cart_line');
        $this->addSql('DROP TABLE product_managed_file');
        $this->addSql('ALTER TABLE orders ADD delivery_address_id INT DEFAULT NULL, ADD billing_address_id INT DEFAULT NULL, ADD order_status VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE orders ADD CONSTRAINT FK_E52FFDEEEBF23851 FOREIGN KEY (delivery_address_id) REFERENCES postal_address (id)');
        $this->addSql('ALTER TABLE orders ADD CONSTRAINT FK_E52FFDEE79D0C0E4 FOREIGN KEY (billing_address_id) REFERENCES postal_address (id)');
        $this->addSql('CREATE INDEX IDX_E52FFDEEEBF23851 ON orders (delivery_address_id)');
        $this->addSql('CREATE INDEX IDX_E52FFDEE79D0C0E4 ON orders (billing_address_id)');
        $this->addSql('ALTER TABLE organisation ADD postal_address_id INT DEFAULT NULL, ADD invoice_address_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE organisation ADD CONSTRAINT FK_E6E132B4FD54954B FOREIGN KEY (postal_address_id) REFERENCES postal_address (id)');
        $this->addSql('ALTER TABLE organisation ADD CONSTRAINT FK_E6E132B4C6BDFEB FOREIGN KEY (invoice_address_id) REFERENCES postal_address (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_E6E132B4FD54954B ON organisation (postal_address_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_E6E132B4C6BDFEB ON organisation (invoice_address_id)');
        $this->addSql('ALTER TABLE postal_address ADD is_default_billing_address TINYINT(1) NOT NULL, ADD is_default_shipping_address TINYINT(1) NOT NULL');
        $this->addSql('ALTER TABLE product DROP FOREIGN KEY FK_D34A04ADB03A8386');
        $this->addSql('DROP INDEX UNIQ_D34A04ADF9038C4 ON product');
        $this->addSql('DROP INDEX UNIQ_D34A04ADC67D0B87 ON product');
        $this->addSql('DROP INDEX IDX_D34A04ADB03A8386 ON product');
        $this->addSql('ALTER TABLE product DROP product_stock_id, DROP pre_tax_price_centessimal, DROP tax_exempt, DROP publication_date, DROP retired_date, CHANGE created_by_id organisation_id INT DEFAULT NULL, CHANGE sku base_sku VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE product ADD CONSTRAINT FK_D34A04AD9E6B1585 FOREIGN KEY (organisation_id) REFERENCES organisation (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_D34A04AD65F6468E ON product (base_sku)');
        $this->addSql('CREATE INDEX IDX_D34A04AD9E6B1585 ON product (organisation_id)');
        $this->addSql('ALTER TABLE puzzle_instance CHANGE config config JSON NOT NULL');
        $this->addSql('ALTER TABLE ticket_type DROP FOREIGN KEY FK_BE0542114584665A');
        $this->addSql('DROP INDEX UNIQ_BE0542114584665A ON ticket_type');
        $this->addSql('ALTER TABLE ticket_type ADD refundable_until DATETIME DEFAULT NULL, DROP product_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE order_payment (id INT AUTO_INCREMENT NOT NULL, order_id INT DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', reference VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, payment_amount_centessimal INT NOT NULL, payment_status VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, INDEX IDX_9B522D468D9F6D38 (order_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE product_order_line (id INT AUTO_INCREMENT NOT NULL, order_id INT DEFAULT NULL, product_id INT DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', quantity INT NOT NULL, UNIQUE INDEX UNIQ_F82D1CA4584665A (product_id), INDEX IDX_F82D1CA8D9F6D38 (order_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE product_stock (id INT AUTO_INCREMENT NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', initial_stock_level INT NOT NULL, current_stock_level INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE user_cart_line (id INT AUTO_INCREMENT NOT NULL, user_cart_id INT DEFAULT NULL, product_id INT DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', quantity INT NOT NULL, INDEX IDX_3E11392B42D8D3B5 (user_cart_id), INDEX IDX_3E11392B4584665A (product_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE product_managed_file (product_id INT NOT NULL, managed_file_id INT NOT NULL, INDEX IDX_D2BD57844584665A (product_id), INDEX IDX_D2BD5784F2DC7781 (managed_file_id), PRIMARY KEY(product_id, managed_file_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE order_payment ADD CONSTRAINT FK_9B522D468D9F6D38 FOREIGN KEY (order_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE product_order_line ADD CONSTRAINT FK_F82D1CA4584665A FOREIGN KEY (product_id) REFERENCES product (id)');
        $this->addSql('ALTER TABLE product_order_line ADD CONSTRAINT FK_F82D1CA8D9F6D38 FOREIGN KEY (order_id) REFERENCES orders (id)');
        $this->addSql('ALTER TABLE user_cart_line ADD CONSTRAINT FK_3E11392B42D8D3B5 FOREIGN KEY (user_cart_id) REFERENCES user_cart (id)');
        $this->addSql('ALTER TABLE user_cart_line ADD CONSTRAINT FK_3E11392B4584665A FOREIGN KEY (product_id) REFERENCES product (id)');
        $this->addSql('ALTER TABLE product_managed_file ADD CONSTRAINT FK_D2BD57844584665A FOREIGN KEY (product_id) REFERENCES product (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE product_managed_file ADD CONSTRAINT FK_D2BD5784F2DC7781 FOREIGN KEY (managed_file_id) REFERENCES managed_file (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE order_product_line DROP FOREIGN KEY FK_583C023D8D9F6D38');
        $this->addSql('ALTER TABLE order_product_line DROP FOREIGN KEY FK_583C023DA80EF684');
        $this->addSql('ALTER TABLE order_tax DROP FOREIGN KEY FK_CDDAF5168D9F6D38');
        $this->addSql('ALTER TABLE payment DROP FOREIGN KEY FK_6D28840D8D9F6D38');
        $this->addSql('ALTER TABLE payment_receipt DROP FOREIGN KEY FK_4AD0B8AF4C3A3BB');
        $this->addSql('ALTER TABLE product_variant DROP FOREIGN KEY FK_209AA41DB03A8386');
        $this->addSql('ALTER TABLE product_variant DROP FOREIGN KEY FK_209AA41D1C914464');
        $this->addSql('ALTER TABLE product_variant DROP FOREIGN KEY FK_209AA41D4584665A');
        $this->addSql('ALTER TABLE product_variant_managed_file DROP FOREIGN KEY FK_F97CF485A80EF684');
        $this->addSql('ALTER TABLE product_variant_managed_file DROP FOREIGN KEY FK_F97CF485F2DC7781');
        $this->addSql('ALTER TABLE ticket_type_product_variant_map DROP FOREIGN KEY FK_F4C521B3C980D5C1');
        $this->addSql('ALTER TABLE ticket_type_product_variant_map DROP FOREIGN KEY FK_F4C521B3A80EF684');
        $this->addSql('ALTER TABLE user_cart_charge DROP FOREIGN KEY FK_F2F40FFE42D8D3B5');
        $this->addSql('ALTER TABLE user_cart_product_line DROP FOREIGN KEY FK_D26CBF1542D8D3B5');
        $this->addSql('ALTER TABLE user_cart_product_line DROP FOREIGN KEY FK_D26CBF15A80EF684');
        $this->addSql('DROP TABLE order_product_line');
        $this->addSql('DROP TABLE order_tax');
        $this->addSql('DROP TABLE payment');
        $this->addSql('DROP TABLE payment_receipt');
        $this->addSql('DROP TABLE product_variant');
        $this->addSql('DROP TABLE product_variant_managed_file');
        $this->addSql('DROP TABLE product_variant_stock');
        $this->addSql('DROP TABLE ticket_type_product_variant_map');
        $this->addSql('DROP TABLE user_cart_charge');
        $this->addSql('DROP TABLE user_cart_product_line');
        $this->addSql('ALTER TABLE postal_address DROP is_default_billing_address, DROP is_default_shipping_address');
        $this->addSql('ALTER TABLE puzzle_instance CHANGE config config JSON NOT NULL COMMENT \'(DC2Type:json)\'');
        $this->addSql('ALTER TABLE product DROP FOREIGN KEY FK_D34A04AD9E6B1585');
        $this->addSql('DROP INDEX UNIQ_D34A04AD65F6468E ON product');
        $this->addSql('DROP INDEX IDX_D34A04AD9E6B1585 ON product');
        $this->addSql('ALTER TABLE product ADD product_stock_id INT NOT NULL, ADD pre_tax_price_centessimal INT NOT NULL, ADD tax_exempt TINYINT(1) NOT NULL, ADD publication_date DATETIME DEFAULT NULL, ADD retired_date DATETIME DEFAULT NULL, CHANGE organisation_id created_by_id INT DEFAULT NULL, CHANGE base_sku sku VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE product ADD CONSTRAINT FK_D34A04ADC67D0B87 FOREIGN KEY (product_stock_id) REFERENCES product_stock (id)');
        $this->addSql('ALTER TABLE product ADD CONSTRAINT FK_D34A04ADB03A8386 FOREIGN KEY (created_by_id) REFERENCES user (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_D34A04ADF9038C4 ON product (sku)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_D34A04ADC67D0B87 ON product (product_stock_id)');
        $this->addSql('CREATE INDEX IDX_D34A04ADB03A8386 ON product (created_by_id)');
        $this->addSql('ALTER TABLE organisation DROP FOREIGN KEY FK_E6E132B4FD54954B');
        $this->addSql('ALTER TABLE organisation DROP FOREIGN KEY FK_E6E132B4C6BDFEB');
        $this->addSql('DROP INDEX UNIQ_E6E132B4FD54954B ON organisation');
        $this->addSql('DROP INDEX UNIQ_E6E132B4C6BDFEB ON organisation');
        $this->addSql('ALTER TABLE organisation DROP postal_address_id, DROP invoice_address_id');
        $this->addSql('ALTER TABLE orders DROP FOREIGN KEY FK_E52FFDEEEBF23851');
        $this->addSql('ALTER TABLE orders DROP FOREIGN KEY FK_E52FFDEE79D0C0E4');
        $this->addSql('DROP INDEX IDX_E52FFDEEEBF23851 ON orders');
        $this->addSql('DROP INDEX IDX_E52FFDEE79D0C0E4 ON orders');
        $this->addSql('ALTER TABLE orders DROP delivery_address_id, DROP billing_address_id, DROP order_status');
        $this->addSql('ALTER TABLE ticket_type ADD product_id INT NOT NULL, DROP refundable_until');
        $this->addSql('ALTER TABLE ticket_type ADD CONSTRAINT FK_BE0542114584665A FOREIGN KEY (product_id) REFERENCES product (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_BE0542114584665A ON ticket_type (product_id)');
    }
}
