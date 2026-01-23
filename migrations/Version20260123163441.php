<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260123163441 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE abuse_report CHANGE notes notes VARCHAR(1024) DEFAULT NULL');
        $this->addSql('ALTER TABLE casebook CHANGE game_id game_id INT NOT NULL');
        $this->addSql('ALTER TABLE casebook_subject_clue CHANGE type_id type_id INT NOT NULL');
//        $this->addSql('ALTER TABLE reset_password_request DROP FOREIGN KEY FK_7CE748AA76ED395');
//        $this->addSql('DROP INDEX IDX_7CE748AA76ED395 ON reset_password_request');
        $this->addSql('ALTER TABLE reset_password_request DROP user_id');
        $this->addSql('ALTER TABLE user CHANGE user_account_type_id user_account_type_id INT NOT NULL');
        $this->addSql('ALTER TABLE user_block CHANGE expiration_date expiration_date DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\'');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user_block CHANGE expiration_date expiration_date DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE casebook CHANGE game_id game_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE abuse_report CHANGE notes notes VARCHAR(1024) NOT NULL');
        $this->addSql('ALTER TABLE reset_password_request ADD user_id INT NOT NULL');
        $this->addSql('ALTER TABLE reset_password_request ADD CONSTRAINT FK_7CE748AA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_7CE748AA76ED395 ON reset_password_request (user_id)');
        $this->addSql('ALTER TABLE user CHANGE user_account_type_id user_account_type_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE casebook_subject_clue CHANGE type_id type_id INT DEFAULT NULL');
    }

    public function isTransactional(): bool
    {
        return false;
    }
}
