<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260123164408 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE managed_file (id INT AUTO_INCREMENT NOT NULL, uploader_id INT DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', path VARCHAR(1024) NOT NULL, mime_type VARCHAR(128) NOT NULL, title VARCHAR(255) NOT NULL, description VARCHAR(512) NOT NULL, INDEX IDX_5F1975EF16678C77 (uploader_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE managed_file ADD CONSTRAINT FK_5F1975EF16678C77 FOREIGN KEY (uploader_id) REFERENCES user (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE managed_file DROP FOREIGN KEY FK_5F1975EF16678C77');
        $this->addSql('DROP TABLE managed_file');
    }

    public function isTransactional(): bool
    {
        return false;
    }
}
