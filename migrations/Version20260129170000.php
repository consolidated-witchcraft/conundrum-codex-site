<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260129170000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE game_session (id INT AUTO_INCREMENT NOT NULL, game_id INT NOT NULL, session_image_id INT DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', session_date DATETIME NOT NULL, title VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, cancellation_date DATETIME DEFAULT NULL, INDEX IDX_4586AAFBE48FD905 (game_id), UNIQUE INDEX UNIQ_4586AAFBB6094505 (session_image_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE game_session ADD CONSTRAINT FK_4586AAFBE48FD905 FOREIGN KEY (game_id) REFERENCES game (id)');
        $this->addSql('ALTER TABLE game_session ADD CONSTRAINT FK_4586AAFBB6094505 FOREIGN KEY (session_image_id) REFERENCES managed_file (id)');
        $this->addSql('ALTER TABLE managed_file CHANGE uploader_id uploader_id INT NOT NULL');
        $this->addSql('ALTER TABLE user_block CHANGE user_id user_id INT NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE game_session DROP FOREIGN KEY FK_4586AAFBE48FD905');
        $this->addSql('ALTER TABLE game_session DROP FOREIGN KEY FK_4586AAFBB6094505');
        $this->addSql('DROP TABLE game_session');
        $this->addSql('ALTER TABLE user_block CHANGE user_id user_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE managed_file CHANGE uploader_id uploader_id INT DEFAULT NULL');
    }
}
