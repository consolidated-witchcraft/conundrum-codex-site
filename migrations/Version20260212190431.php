<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260212190431 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE event (id INT AUTO_INCREMENT NOT NULL, hero_image_id INT DEFAULT NULL, location_id INT DEFAULT NULL, organisation_id INT DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', title VARCHAR(255) NOT NULL, event_slug VARCHAR(255) NOT NULL, description VARCHAR(2048) NOT NULL, prebook_only TINYINT(1) NOT NULL, start_date DATETIME NOT NULL, end_date DATETIME NOT NULL, UNIQUE INDEX UNIQ_3BAE0AA78756D7C9 (event_slug), UNIQUE INDEX UNIQ_3BAE0AA798BB94C5 (hero_image_id), UNIQUE INDEX UNIQ_3BAE0AA764D218E (location_id), INDEX IDX_3BAE0AA79E6B1585 (organisation_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE order_charge_line (id INT AUTO_INCREMENT NOT NULL, order_id INT DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', charge_type VARCHAR(255) NOT NULL, name VARCHAR(255) NOT NULL, price_centessimal INT NOT NULL, INDEX IDX_8265A6AF8D9F6D38 (order_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE order_payment (id INT AUTO_INCREMENT NOT NULL, order_id INT DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', reference VARCHAR(255) NOT NULL, payment_amount_centessimal INT NOT NULL, payment_status VARCHAR(255) NOT NULL, INDEX IDX_9B522D468D9F6D38 (order_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE orders (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', order_reference VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_E52FFDEE122432EB (order_reference), INDEX IDX_E52FFDEEA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE organisation (id INT AUTO_INCREMENT NOT NULL, organisation_logo_id INT NOT NULL, owner_id INT DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', name VARCHAR(255) NOT NULL, handle VARCHAR(255) NOT NULL, deactivation_date DATETIME DEFAULT NULL, UNIQUE INDEX UNIQ_E6E132B4918020D9 (handle), UNIQUE INDEX UNIQ_E6E132B49A0B6349 (organisation_logo_id), INDEX IDX_E6E132B47E3C61F9 (owner_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE postal_address (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', label VARCHAR(255) NOT NULL, address_line_one VARCHAR(255) NOT NULL, town_city VARCHAR(255) NOT NULL, postal_zip VARCHAR(255) NOT NULL, country_code VARCHAR(255) NOT NULL, county_province VARCHAR(255) NOT NULL, address_line_two VARCHAR(255) DEFAULT NULL, latitude DOUBLE PRECISION DEFAULT NULL, longitude DOUBLE PRECISION DEFAULT NULL, INDEX IDX_972EFBF7A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE product (id INT AUTO_INCREMENT NOT NULL, created_by_id INT DEFAULT NULL, product_stock_id INT NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', sku VARCHAR(255) NOT NULL, name VARCHAR(255) NOT NULL, description VARCHAR(2198) NOT NULL, pre_tax_price_centessimal INT NOT NULL, tax_exempt TINYINT(1) NOT NULL, publication_date DATETIME DEFAULT NULL, retired_date DATETIME DEFAULT NULL, UNIQUE INDEX UNIQ_D34A04ADF9038C4 (sku), INDEX IDX_D34A04ADB03A8386 (created_by_id), UNIQUE INDEX UNIQ_D34A04ADC67D0B87 (product_stock_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE product_managed_file (product_id INT NOT NULL, managed_file_id INT NOT NULL, INDEX IDX_D2BD57844584665A (product_id), INDEX IDX_D2BD5784F2DC7781 (managed_file_id), PRIMARY KEY(product_id, managed_file_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE product_order_line (id INT AUTO_INCREMENT NOT NULL, order_id INT DEFAULT NULL, product_id INT DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', quantity INT NOT NULL, INDEX IDX_F82D1CA8D9F6D38 (order_id), UNIQUE INDEX UNIQ_F82D1CA4584665A (product_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE product_stock (id INT AUTO_INCREMENT NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', initial_stock_level INT NOT NULL, current_stock_level INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE ticket (id INT AUTO_INCREMENT NOT NULL, ticket_type_id INT DEFAULT NULL, user_id INT DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', customer_email VARCHAR(255) NOT NULL, customer_name VARCHAR(255) NOT NULL, ticket_code VARCHAR(255) NOT NULL, issued_date DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', paid_date DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', UNIQUE INDEX UNIQ_97A0ADA345CE25A0 (ticket_code), INDEX IDX_97A0ADA3C980D5C1 (ticket_type_id), INDEX IDX_97A0ADA3A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE ticket_type (id INT AUTO_INCREMENT NOT NULL, product_id INT NOT NULL, event_id INT DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', name VARCHAR(255) NOT NULL, slug VARCHAR(255) NOT NULL, description VARCHAR(255) NOT NULL, price_centessimal INT NOT NULL, UNIQUE INDEX UNIQ_BE0542114584665A (product_id), INDEX IDX_BE05421171F7E88B (event_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_cart (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', UNIQUE INDEX UNIQ_7122C47EA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_cart_line (id INT AUTO_INCREMENT NOT NULL, user_cart_id INT DEFAULT NULL, product_id INT DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', quantity INT NOT NULL, INDEX IDX_3E11392B42D8D3B5 (user_cart_id), INDEX IDX_3E11392B4584665A (product_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE event ADD CONSTRAINT FK_3BAE0AA798BB94C5 FOREIGN KEY (hero_image_id) REFERENCES managed_file (id)');
        $this->addSql('ALTER TABLE event ADD CONSTRAINT FK_3BAE0AA764D218E FOREIGN KEY (location_id) REFERENCES postal_address (id)');
        $this->addSql('ALTER TABLE event ADD CONSTRAINT FK_3BAE0AA79E6B1585 FOREIGN KEY (organisation_id) REFERENCES organisation (id)');
        $this->addSql('ALTER TABLE order_charge_line ADD CONSTRAINT FK_8265A6AF8D9F6D38 FOREIGN KEY (order_id) REFERENCES orders (id)');
        $this->addSql('ALTER TABLE order_payment ADD CONSTRAINT FK_9B522D468D9F6D38 FOREIGN KEY (order_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE orders ADD CONSTRAINT FK_E52FFDEEA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE organisation ADD CONSTRAINT FK_E6E132B49A0B6349 FOREIGN KEY (organisation_logo_id) REFERENCES managed_file (id)');
        $this->addSql('ALTER TABLE organisation ADD CONSTRAINT FK_E6E132B47E3C61F9 FOREIGN KEY (owner_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE postal_address ADD CONSTRAINT FK_972EFBF7A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE product ADD CONSTRAINT FK_D34A04ADB03A8386 FOREIGN KEY (created_by_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE product ADD CONSTRAINT FK_D34A04ADC67D0B87 FOREIGN KEY (product_stock_id) REFERENCES product_stock (id)');
        $this->addSql('ALTER TABLE product_managed_file ADD CONSTRAINT FK_D2BD57844584665A FOREIGN KEY (product_id) REFERENCES product (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE product_managed_file ADD CONSTRAINT FK_D2BD5784F2DC7781 FOREIGN KEY (managed_file_id) REFERENCES managed_file (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE product_order_line ADD CONSTRAINT FK_F82D1CA8D9F6D38 FOREIGN KEY (order_id) REFERENCES orders (id)');
        $this->addSql('ALTER TABLE product_order_line ADD CONSTRAINT FK_F82D1CA4584665A FOREIGN KEY (product_id) REFERENCES product (id)');
        $this->addSql('ALTER TABLE ticket ADD CONSTRAINT FK_97A0ADA3C980D5C1 FOREIGN KEY (ticket_type_id) REFERENCES ticket_type (id)');
        $this->addSql('ALTER TABLE ticket ADD CONSTRAINT FK_97A0ADA3A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE ticket_type ADD CONSTRAINT FK_BE0542114584665A FOREIGN KEY (product_id) REFERENCES product (id)');
        $this->addSql('ALTER TABLE ticket_type ADD CONSTRAINT FK_BE05421171F7E88B FOREIGN KEY (event_id) REFERENCES event (id)');
        $this->addSql('ALTER TABLE user_cart ADD CONSTRAINT FK_7122C47EA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE user_cart_line ADD CONSTRAINT FK_3E11392B42D8D3B5 FOREIGN KEY (user_cart_id) REFERENCES user_cart (id)');
        $this->addSql('ALTER TABLE user_cart_line ADD CONSTRAINT FK_3E11392B4584665A FOREIGN KEY (product_id) REFERENCES product (id)');
        $this->addSql('ALTER TABLE puzzle_instance CHANGE config config JSON NOT NULL');
        $this->addSql('ALTER TABLE user ADD organisation_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D6499E6B1585 FOREIGN KEY (organisation_id) REFERENCES organisation (id)');
        $this->addSql('CREATE INDEX IDX_8D93D6499E6B1585 ON user (organisation_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D6499E6B1585');
        $this->addSql('ALTER TABLE event DROP FOREIGN KEY FK_3BAE0AA798BB94C5');
        $this->addSql('ALTER TABLE event DROP FOREIGN KEY FK_3BAE0AA764D218E');
        $this->addSql('ALTER TABLE event DROP FOREIGN KEY FK_3BAE0AA79E6B1585');
        $this->addSql('ALTER TABLE order_charge_line DROP FOREIGN KEY FK_8265A6AF8D9F6D38');
        $this->addSql('ALTER TABLE order_payment DROP FOREIGN KEY FK_9B522D468D9F6D38');
        $this->addSql('ALTER TABLE orders DROP FOREIGN KEY FK_E52FFDEEA76ED395');
        $this->addSql('ALTER TABLE organisation DROP FOREIGN KEY FK_E6E132B49A0B6349');
        $this->addSql('ALTER TABLE organisation DROP FOREIGN KEY FK_E6E132B47E3C61F9');
        $this->addSql('ALTER TABLE postal_address DROP FOREIGN KEY FK_972EFBF7A76ED395');
        $this->addSql('ALTER TABLE product DROP FOREIGN KEY FK_D34A04ADB03A8386');
        $this->addSql('ALTER TABLE product DROP FOREIGN KEY FK_D34A04ADC67D0B87');
        $this->addSql('ALTER TABLE product_managed_file DROP FOREIGN KEY FK_D2BD57844584665A');
        $this->addSql('ALTER TABLE product_managed_file DROP FOREIGN KEY FK_D2BD5784F2DC7781');
        $this->addSql('ALTER TABLE product_order_line DROP FOREIGN KEY FK_F82D1CA8D9F6D38');
        $this->addSql('ALTER TABLE product_order_line DROP FOREIGN KEY FK_F82D1CA4584665A');
        $this->addSql('ALTER TABLE ticket DROP FOREIGN KEY FK_97A0ADA3C980D5C1');
        $this->addSql('ALTER TABLE ticket DROP FOREIGN KEY FK_97A0ADA3A76ED395');
        $this->addSql('ALTER TABLE ticket_type DROP FOREIGN KEY FK_BE0542114584665A');
        $this->addSql('ALTER TABLE ticket_type DROP FOREIGN KEY FK_BE05421171F7E88B');
        $this->addSql('ALTER TABLE user_cart DROP FOREIGN KEY FK_7122C47EA76ED395');
        $this->addSql('ALTER TABLE user_cart_line DROP FOREIGN KEY FK_3E11392B42D8D3B5');
        $this->addSql('ALTER TABLE user_cart_line DROP FOREIGN KEY FK_3E11392B4584665A');
        $this->addSql('DROP TABLE event');
        $this->addSql('DROP TABLE order_charge_line');
        $this->addSql('DROP TABLE order_payment');
        $this->addSql('DROP TABLE orders');
        $this->addSql('DROP TABLE organisation');
        $this->addSql('DROP TABLE postal_address');
        $this->addSql('DROP TABLE product');
        $this->addSql('DROP TABLE product_managed_file');
        $this->addSql('DROP TABLE product_order_line');
        $this->addSql('DROP TABLE product_stock');
        $this->addSql('DROP TABLE ticket');
        $this->addSql('DROP TABLE ticket_type');
        $this->addSql('DROP TABLE user_cart');
        $this->addSql('DROP TABLE user_cart_line');
        $this->addSql('ALTER TABLE puzzle_instance CHANGE config config JSON NOT NULL COMMENT \'(DC2Type:json)\'');
        $this->addSql('DROP INDEX IDX_8D93D6499E6B1585 ON user');
        $this->addSql('ALTER TABLE user DROP organisation_id');
    }
}
