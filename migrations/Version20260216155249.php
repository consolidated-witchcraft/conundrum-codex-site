<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260216155249 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user_cart_charge DROP FOREIGN KEY FK_F2F40FFE996DBA9C');
        $this->addSql('ALTER TABLE user_cart_charge_line DROP FOREIGN KEY FK_B71162B442D8D3B5');
        $this->addSql('DROP TABLE user_cart_charge_line');
        $this->addSql('ALTER TABLE puzzle_instance CHANGE config config JSON NOT NULL');
        $this->addSql('DROP INDEX UNIQ_F2F40FFE996DBA9C ON user_cart_charge');
        $this->addSql('ALTER TABLE user_cart_charge CHANGE user_cart_charge_line_id user_cart_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE user_cart_charge ADD CONSTRAINT FK_F2F40FFE42D8D3B5 FOREIGN KEY (user_cart_id) REFERENCES user_cart (id)');
        $this->addSql('CREATE INDEX IDX_F2F40FFE42D8D3B5 ON user_cart_charge (user_cart_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE user_cart_charge_line (id INT AUTO_INCREMENT NOT NULL, user_cart_id INT DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_B71162B442D8D3B5 (user_cart_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE user_cart_charge_line ADD CONSTRAINT FK_B71162B442D8D3B5 FOREIGN KEY (user_cart_id) REFERENCES user_cart (id)');
        $this->addSql('ALTER TABLE puzzle_instance CHANGE config config JSON NOT NULL COMMENT \'(DC2Type:json)\'');
        $this->addSql('ALTER TABLE user_cart_charge DROP FOREIGN KEY FK_F2F40FFE42D8D3B5');
        $this->addSql('DROP INDEX IDX_F2F40FFE42D8D3B5 ON user_cart_charge');
        $this->addSql('ALTER TABLE user_cart_charge CHANGE user_cart_id user_cart_charge_line_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE user_cart_charge ADD CONSTRAINT FK_F2F40FFE996DBA9C FOREIGN KEY (user_cart_charge_line_id) REFERENCES user_cart_charge_line (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_F2F40FFE996DBA9C ON user_cart_charge (user_cart_charge_line_id)');
    }
}
