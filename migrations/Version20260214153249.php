<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260214153249 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE order_product_line (id INT AUTO_INCREMENT NOT NULL, order_id INT DEFAULT NULL, product_id INT DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', quantity INT NOT NULL, INDEX IDX_583C023D8D9F6D38 (order_id), INDEX IDX_583C023D4584665A (product_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE order_tax (id INT AUTO_INCREMENT NOT NULL, order_id INT DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', tax_rate_public_reference VARCHAR(255) NOT NULL, tax_rate_name VARCHAR(255) NOT NULL, tax_rate_handle VARCHAR(255) NOT NULL, amount_centessimal INT NOT NULL, INDEX IDX_CDDAF5168D9F6D38 (order_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE payment (payment_reference BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', order_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, payment_amount_centessimal INT NOT NULL, human_readable_payment_reference VARCHAR(255) NOT NULL, payment_status VARCHAR(255) NOT NULL, INDEX IDX_6D28840D8D9F6D38 (order_id), PRIMARY KEY(payment_reference)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE order_product_line ADD CONSTRAINT FK_583C023D8D9F6D38 FOREIGN KEY (order_id) REFERENCES orders (id)');
        $this->addSql('ALTER TABLE order_product_line ADD CONSTRAINT FK_583C023D4584665A FOREIGN KEY (product_id) REFERENCES product (id)');
        $this->addSql('ALTER TABLE order_tax ADD CONSTRAINT FK_CDDAF5168D9F6D38 FOREIGN KEY (order_id) REFERENCES orders (id)');
        $this->addSql('ALTER TABLE payment ADD CONSTRAINT FK_6D28840D8D9F6D38 FOREIGN KEY (order_id) REFERENCES orders (id)');
        $this->addSql('ALTER TABLE order_payment DROP FOREIGN KEY FK_9B522D468D9F6D38');
        $this->addSql('ALTER TABLE product_order_line DROP FOREIGN KEY FK_F82D1CA8D9F6D38');
        $this->addSql('ALTER TABLE product_order_line DROP FOREIGN KEY FK_F82D1CA4584665A');
        $this->addSql('DROP TABLE order_payment');
        $this->addSql('DROP TABLE product_order_line');
        $this->addSql('DROP INDEX UNIQ_E52FFDEE122432EB ON orders');
        $this->addSql('ALTER TABLE orders DROP order_reference');
        $this->addSql('ALTER TABLE product ADD type VARCHAR(255) DEFAULT \'e_ticket\' NOT NULL');
        $this->addSql('ALTER TABLE puzzle_instance CHANGE config config JSON NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE order_payment (id INT AUTO_INCREMENT NOT NULL, order_id INT DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', reference VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, payment_amount_centessimal INT NOT NULL, payment_status VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, INDEX IDX_9B522D468D9F6D38 (order_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE product_order_line (id INT AUTO_INCREMENT NOT NULL, order_id INT DEFAULT NULL, product_id INT DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', quantity INT NOT NULL, UNIQUE INDEX UNIQ_F82D1CA4584665A (product_id), INDEX IDX_F82D1CA8D9F6D38 (order_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE order_payment ADD CONSTRAINT FK_9B522D468D9F6D38 FOREIGN KEY (order_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE product_order_line ADD CONSTRAINT FK_F82D1CA8D9F6D38 FOREIGN KEY (order_id) REFERENCES orders (id)');
        $this->addSql('ALTER TABLE product_order_line ADD CONSTRAINT FK_F82D1CA4584665A FOREIGN KEY (product_id) REFERENCES product (id)');
        $this->addSql('ALTER TABLE order_product_line DROP FOREIGN KEY FK_583C023D8D9F6D38');
        $this->addSql('ALTER TABLE order_product_line DROP FOREIGN KEY FK_583C023D4584665A');
        $this->addSql('ALTER TABLE order_tax DROP FOREIGN KEY FK_CDDAF5168D9F6D38');
        $this->addSql('ALTER TABLE payment DROP FOREIGN KEY FK_6D28840D8D9F6D38');
        $this->addSql('DROP TABLE order_product_line');
        $this->addSql('DROP TABLE order_tax');
        $this->addSql('DROP TABLE payment');
        $this->addSql('ALTER TABLE puzzle_instance CHANGE config config JSON NOT NULL COMMENT \'(DC2Type:json)\'');
        $this->addSql('ALTER TABLE product DROP type');
        $this->addSql('ALTER TABLE orders ADD order_reference VARCHAR(255) NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_E52FFDEE122432EB ON orders (order_reference)');
    }
}
