<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260216154133 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE user_cart_charge (id INT AUTO_INCREMENT NOT NULL, user_cart_line_id INT DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', charge_type VARCHAR(255) NOT NULL, amount_centessimal INT NOT NULL, UNIQUE INDEX UNIQ_F2F40FFE366DC147 (user_cart_line_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_cart_charge_line (id INT AUTO_INCREMENT NOT NULL, user_cart_id INT DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_B71162B442D8D3B5 (user_cart_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_cart_product_line (id INT AUTO_INCREMENT NOT NULL, user_cart_id INT DEFAULT NULL, product_variant_id INT DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', quantity INT NOT NULL, INDEX IDX_D26CBF1542D8D3B5 (user_cart_id), INDEX IDX_D26CBF15A80EF684 (product_variant_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE user_cart_charge ADD CONSTRAINT FK_F2F40FFE366DC147 FOREIGN KEY (user_cart_line_id) REFERENCES user_cart_charge_line (id)');
        $this->addSql('ALTER TABLE user_cart_charge_line ADD CONSTRAINT FK_B71162B442D8D3B5 FOREIGN KEY (user_cart_id) REFERENCES user_cart (id)');
        $this->addSql('ALTER TABLE user_cart_product_line ADD CONSTRAINT FK_D26CBF1542D8D3B5 FOREIGN KEY (user_cart_id) REFERENCES user_cart (id)');
        $this->addSql('ALTER TABLE user_cart_product_line ADD CONSTRAINT FK_D26CBF15A80EF684 FOREIGN KEY (product_variant_id) REFERENCES product_variant (id)');
        $this->addSql('ALTER TABLE user_cart_line DROP FOREIGN KEY FK_3E11392BA80EF684');
        $this->addSql('ALTER TABLE user_cart_line DROP FOREIGN KEY FK_3E11392B42D8D3B5');
        $this->addSql('DROP TABLE user_cart_line');
        $this->addSql('ALTER TABLE puzzle_instance CHANGE config config JSON NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE user_cart_line (id INT AUTO_INCREMENT NOT NULL, user_cart_id INT DEFAULT NULL, product_variant_id INT DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', quantity INT NOT NULL, INDEX IDX_3E11392B42D8D3B5 (user_cart_id), INDEX IDX_3E11392BA80EF684 (product_variant_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE user_cart_line ADD CONSTRAINT FK_3E11392BA80EF684 FOREIGN KEY (product_variant_id) REFERENCES product_variant (id)');
        $this->addSql('ALTER TABLE user_cart_line ADD CONSTRAINT FK_3E11392B42D8D3B5 FOREIGN KEY (user_cart_id) REFERENCES user_cart (id)');
        $this->addSql('ALTER TABLE user_cart_charge DROP FOREIGN KEY FK_F2F40FFE366DC147');
        $this->addSql('ALTER TABLE user_cart_charge_line DROP FOREIGN KEY FK_B71162B442D8D3B5');
        $this->addSql('ALTER TABLE user_cart_product_line DROP FOREIGN KEY FK_D26CBF1542D8D3B5');
        $this->addSql('ALTER TABLE user_cart_product_line DROP FOREIGN KEY FK_D26CBF15A80EF684');
        $this->addSql('DROP TABLE user_cart_charge');
        $this->addSql('DROP TABLE user_cart_charge_line');
        $this->addSql('DROP TABLE user_cart_product_line');
        $this->addSql('ALTER TABLE puzzle_instance CHANGE config config JSON NOT NULL COMMENT \'(DC2Type:json)\'');
    }
}
