<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260123181818 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE game ADD hero_image_file_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE game ADD CONSTRAINT FK_232B318C268A15CE FOREIGN KEY (hero_image_file_id) REFERENCES managed_file (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_232B318C268A15CE ON game (hero_image_file_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE game DROP FOREIGN KEY FK_232B318C268A15CE');
        $this->addSql('DROP INDEX UNIQ_232B318C268A15CE ON game');
        $this->addSql('ALTER TABLE game DROP hero_image_file_id');
    }
}
