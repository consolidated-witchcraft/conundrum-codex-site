<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251213170832 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE abuse_report (id INT AUTO_INCREMENT NOT NULL, reported_user_id INT NOT NULL, reporting_user_id INT NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', reason VARCHAR(1024) NOT NULL, notes VARCHAR(1024) NOT NULL, checked_date DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', confirmed_date DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_21AF999BE7566E (reported_user_id), INDEX IDX_21AF999B713FF03D (reporting_user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE blocked_email_address (id INT AUTO_INCREMENT NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', email_address VARCHAR(255) NOT NULL, block_reason VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE bug_report (id INT AUTO_INCREMENT NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', summary VARCHAR(255) NOT NULL, reporter_name VARCHAR(255) NOT NULL, reporter_email VARCHAR(255) NOT NULL, text LONGTEXT NOT NULL, referring_url VARCHAR(255) DEFAULT NULL, actioned_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', closed_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE casebook (id INT AUTO_INCREMENT NOT NULL, game_id INT DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', name VARCHAR(255) NOT NULL, slug VARCHAR(255) NOT NULL, brief VARCHAR(2048) NOT NULL, publication_date DATETIME DEFAULT NULL, UNIQUE INDEX UNIQ_2042C41F989D9B62 (slug), INDEX IDX_2042C41FE48FD905 (game_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE casebook_subject (id INT AUTO_INCREMENT NOT NULL, casebook_id INT NOT NULL, casebook_subject_type_id INT NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', name VARCHAR(255) NOT NULL, description VARCHAR(2048) NOT NULL, casebook_subject_image VARCHAR(2048) NOT NULL, INDEX IDX_911EC37B467F13AB (casebook_id), INDEX IDX_911EC37B68710875 (casebook_subject_type_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE casebook_subject_clue (id INT AUTO_INCREMENT NOT NULL, type_id INT DEFAULT NULL, casebook_subject_id INT NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', title VARCHAR(255) NOT NULL, body VARCHAR(1024) NOT NULL, revealed_date VARCHAR(255) DEFAULT NULL, INDEX IDX_EFB778DDC54C8C93 (type_id), INDEX IDX_EFB778DD243DFBFE (casebook_subject_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE casebook_subject_clue_type (id INT AUTO_INCREMENT NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', label VARCHAR(255) NOT NULL, handle VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_17E2131F918020D9 (handle), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE casebook_subject_note (id INT AUTO_INCREMENT NOT NULL, created_by_id INT NOT NULL, casebook_subject_id INT NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', title VARCHAR(255) NOT NULL, body VARCHAR(1024) NOT NULL, INDEX IDX_6802F18B03A8386 (created_by_id), INDEX IDX_6802F18243DFBFE (casebook_subject_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE casebook_subject_type (id INT AUTO_INCREMENT NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', label VARCHAR(255) NOT NULL, handle VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_45E38225918020D9 (handle), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE game (id INT AUTO_INCREMENT NOT NULL, games_master_id INT NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', name VARCHAR(255) NOT NULL, slug VARCHAR(255) NOT NULL, description VARCHAR(1024) NOT NULL, UNIQUE INDEX UNIQ_232B318C989D9B62 (slug), INDEX gm_id (games_master_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE game_user (game_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_6686BA65E48FD905 (game_id), INDEX IDX_6686BA65A76ED395 (user_id), PRIMARY KEY(game_id, user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE game_invitation (id INT AUTO_INCREMENT NOT NULL, game_id INT NOT NULL, user_id INT DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', invitation_code VARCHAR(64) NOT NULL, email VARCHAR(255) NOT NULL, invitation_message VARCHAR(255) NOT NULL, expires_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', date_used DATE DEFAULT NULL COMMENT \'(DC2Type:date_immutable)\', UNIQUE INDEX UNIQ_1FC1A64FBA14FCCC (invitation_code), INDEX IDX_1FC1A64FE48FD905 (game_id), INDEX IDX_1FC1A64FA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE permission (id INT AUTO_INCREMENT NOT NULL, label VARCHAR(255) NOT NULL, handle VARCHAR(255) NOT NULL, description VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_E04992AA918020D9 (handle), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE puzzle_category (id INT AUTO_INCREMENT NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', label VARCHAR(255) NOT NULL, handle VARCHAR(255) NOT NULL, description VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_5210F490918020D9 (handle), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE puzzle_instance (id INT AUTO_INCREMENT NOT NULL, game_id INT NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', instance_code VARCHAR(255) NOT NULL, name VARCHAR(255) NOT NULL, description VARCHAR(2048) NOT NULL, template_slug VARCHAR(1024) NOT NULL, publication_date DATETIME DEFAULT NULL, config JSON NOT NULL COMMENT \'(DC2Type:json)\', UNIQUE INDEX UNIQ_166C5C8F5F1F6053 (instance_code), INDEX IDX_166C5C8FE48FD905 (game_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE quotation (id INT AUTO_INCREMENT NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', quotation VARCHAR(1024) NOT NULL, citation VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE reset_password_request (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, selector VARCHAR(20) NOT NULL, hashed_token VARCHAR(100) NOT NULL, requested_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', expires_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_7CE748AA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE role (id INT AUTO_INCREMENT NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', name VARCHAR(255) NOT NULL, handle VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_57698A6A918020D9 (handle), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE role_permission (role_id INT NOT NULL, permission_id INT NOT NULL, INDEX IDX_6F7DF886D60322AC (role_id), INDEX IDX_6F7DF886FED90CCA (permission_id), PRIMARY KEY(role_id, permission_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', email VARCHAR(255) NOT NULL, username VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), INDEX username_idx (username), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_role (user_id INT NOT NULL, role_id INT NOT NULL, INDEX IDX_2DE8C6A3A76ED395 (user_id), INDEX IDX_2DE8C6A3D60322AC (role_id), PRIMARY KEY(user_id, role_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_block (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', reason VARCHAR(255) NOT NULL, expiration_date DATETIME DEFAULT NULL, UNIQUE INDEX UNIQ_61D96C7AA76ED395 (user_id), INDEX user_idx (user_id), INDEX expr_idx (expiration_date), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE abuse_report ADD CONSTRAINT FK_21AF999BE7566E FOREIGN KEY (reported_user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE abuse_report ADD CONSTRAINT FK_21AF999B713FF03D FOREIGN KEY (reporting_user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE casebook ADD CONSTRAINT FK_2042C41FE48FD905 FOREIGN KEY (game_id) REFERENCES game (id)');
        $this->addSql('ALTER TABLE casebook_subject ADD CONSTRAINT FK_911EC37B467F13AB FOREIGN KEY (casebook_id) REFERENCES casebook (id)');
        $this->addSql('ALTER TABLE casebook_subject ADD CONSTRAINT FK_911EC37B68710875 FOREIGN KEY (casebook_subject_type_id) REFERENCES casebook_subject_type (id)');
        $this->addSql('ALTER TABLE casebook_subject_clue ADD CONSTRAINT FK_EFB778DDC54C8C93 FOREIGN KEY (type_id) REFERENCES casebook_subject_clue_type (id)');
        $this->addSql('ALTER TABLE casebook_subject_clue ADD CONSTRAINT FK_EFB778DD243DFBFE FOREIGN KEY (casebook_subject_id) REFERENCES casebook_subject (id)');
        $this->addSql('ALTER TABLE casebook_subject_note ADD CONSTRAINT FK_6802F18B03A8386 FOREIGN KEY (created_by_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE casebook_subject_note ADD CONSTRAINT FK_6802F18243DFBFE FOREIGN KEY (casebook_subject_id) REFERENCES casebook_subject (id)');
        $this->addSql('ALTER TABLE game ADD CONSTRAINT FK_232B318C3C75A0F1 FOREIGN KEY (games_master_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE game_user ADD CONSTRAINT FK_6686BA65E48FD905 FOREIGN KEY (game_id) REFERENCES game (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE game_user ADD CONSTRAINT FK_6686BA65A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE game_invitation ADD CONSTRAINT FK_1FC1A64FE48FD905 FOREIGN KEY (game_id) REFERENCES game (id)');
        $this->addSql('ALTER TABLE game_invitation ADD CONSTRAINT FK_1FC1A64FA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE puzzle_instance ADD CONSTRAINT FK_166C5C8FE48FD905 FOREIGN KEY (game_id) REFERENCES game (id)');
        $this->addSql('ALTER TABLE reset_password_request ADD CONSTRAINT FK_7CE748AA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE role_permission ADD CONSTRAINT FK_6F7DF886D60322AC FOREIGN KEY (role_id) REFERENCES role (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE role_permission ADD CONSTRAINT FK_6F7DF886FED90CCA FOREIGN KEY (permission_id) REFERENCES permission (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_role ADD CONSTRAINT FK_2DE8C6A3A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_role ADD CONSTRAINT FK_2DE8C6A3D60322AC FOREIGN KEY (role_id) REFERENCES role (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_block ADD CONSTRAINT FK_61D96C7AA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE abuse_report DROP FOREIGN KEY FK_21AF999BE7566E');
        $this->addSql('ALTER TABLE abuse_report DROP FOREIGN KEY FK_21AF999B713FF03D');
        $this->addSql('ALTER TABLE casebook DROP FOREIGN KEY FK_2042C41FE48FD905');
        $this->addSql('ALTER TABLE casebook_subject DROP FOREIGN KEY FK_911EC37B467F13AB');
        $this->addSql('ALTER TABLE casebook_subject DROP FOREIGN KEY FK_911EC37B68710875');
        $this->addSql('ALTER TABLE casebook_subject_clue DROP FOREIGN KEY FK_EFB778DDC54C8C93');
        $this->addSql('ALTER TABLE casebook_subject_clue DROP FOREIGN KEY FK_EFB778DD243DFBFE');
        $this->addSql('ALTER TABLE casebook_subject_note DROP FOREIGN KEY FK_6802F18B03A8386');
        $this->addSql('ALTER TABLE casebook_subject_note DROP FOREIGN KEY FK_6802F18243DFBFE');
        $this->addSql('ALTER TABLE game DROP FOREIGN KEY FK_232B318C3C75A0F1');
        $this->addSql('ALTER TABLE game_user DROP FOREIGN KEY FK_6686BA65E48FD905');
        $this->addSql('ALTER TABLE game_user DROP FOREIGN KEY FK_6686BA65A76ED395');
        $this->addSql('ALTER TABLE game_invitation DROP FOREIGN KEY FK_1FC1A64FE48FD905');
        $this->addSql('ALTER TABLE game_invitation DROP FOREIGN KEY FK_1FC1A64FA76ED395');
        $this->addSql('ALTER TABLE puzzle_instance DROP FOREIGN KEY FK_166C5C8FE48FD905');
        $this->addSql('ALTER TABLE reset_password_request DROP FOREIGN KEY FK_7CE748AA76ED395');
        $this->addSql('ALTER TABLE role_permission DROP FOREIGN KEY FK_6F7DF886D60322AC');
        $this->addSql('ALTER TABLE role_permission DROP FOREIGN KEY FK_6F7DF886FED90CCA');
        $this->addSql('ALTER TABLE user_role DROP FOREIGN KEY FK_2DE8C6A3A76ED395');
        $this->addSql('ALTER TABLE user_role DROP FOREIGN KEY FK_2DE8C6A3D60322AC');
        $this->addSql('ALTER TABLE user_block DROP FOREIGN KEY FK_61D96C7AA76ED395');
        $this->addSql('DROP TABLE abuse_report');
        $this->addSql('DROP TABLE blocked_email_address');
        $this->addSql('DROP TABLE bug_report');
        $this->addSql('DROP TABLE casebook');
        $this->addSql('DROP TABLE casebook_subject');
        $this->addSql('DROP TABLE casebook_subject_clue');
        $this->addSql('DROP TABLE casebook_subject_clue_type');
        $this->addSql('DROP TABLE casebook_subject_note');
        $this->addSql('DROP TABLE casebook_subject_type');
        $this->addSql('DROP TABLE game');
        $this->addSql('DROP TABLE game_user');
        $this->addSql('DROP TABLE game_invitation');
        $this->addSql('DROP TABLE permission');
        $this->addSql('DROP TABLE puzzle_category');
        $this->addSql('DROP TABLE puzzle_instance');
        $this->addSql('DROP TABLE quotation');
        $this->addSql('DROP TABLE reset_password_request');
        $this->addSql('DROP TABLE role');
        $this->addSql('DROP TABLE role_permission');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE user_role');
        $this->addSql('DROP TABLE user_block');
    }

    public function isTransactional(): bool
    {
        return false;
    }
}
