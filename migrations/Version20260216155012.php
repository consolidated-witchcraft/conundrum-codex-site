<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260216155012 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE puzzle_instance CHANGE config config JSON NOT NULL');
        $this->addSql('ALTER TABLE user_cart_charge DROP FOREIGN KEY FK_F2F40FFE366DC147');
        $this->addSql('DROP INDEX UNIQ_F2F40FFE366DC147 ON user_cart_charge');
        $this->addSql('ALTER TABLE user_cart_charge CHANGE user_cart_line_id user_cart_charge_line_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE user_cart_charge ADD CONSTRAINT FK_F2F40FFE996DBA9C FOREIGN KEY (user_cart_charge_line_id) REFERENCES user_cart_charge_line (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_F2F40FFE996DBA9C ON user_cart_charge (user_cart_charge_line_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE puzzle_instance CHANGE config config JSON NOT NULL COMMENT \'(DC2Type:json)\'');
        $this->addSql('ALTER TABLE user_cart_charge DROP FOREIGN KEY FK_F2F40FFE996DBA9C');
        $this->addSql('DROP INDEX UNIQ_F2F40FFE996DBA9C ON user_cart_charge');
        $this->addSql('ALTER TABLE user_cart_charge CHANGE user_cart_charge_line_id user_cart_line_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE user_cart_charge ADD CONSTRAINT FK_F2F40FFE366DC147 FOREIGN KEY (user_cart_line_id) REFERENCES user_cart_charge_line (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_F2F40FFE366DC147 ON user_cart_charge (user_cart_line_id)');
    }
}
