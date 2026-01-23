<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260123181057 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE casebook_subject ADD casebook_subject_image_file_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE casebook_subject ADD CONSTRAINT FK_911EC37B51412E32 FOREIGN KEY (casebook_subject_image_file_id) REFERENCES managed_file (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_911EC37B51412E32 ON casebook_subject (casebook_subject_image_file_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE casebook_subject DROP FOREIGN KEY FK_911EC37B51412E32');
        $this->addSql('DROP INDEX UNIQ_911EC37B51412E32 ON casebook_subject');
        $this->addSql('ALTER TABLE casebook_subject DROP casebook_subject_image_file_id');
    }
}
