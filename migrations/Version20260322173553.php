<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260322173553 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE managed_image (id INT AUTO_INCREMENT NOT NULL, created_by_id INT NOT NULL, source_image_id INT NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', description VARCHAR(255) NOT NULL, INDEX IDX_E8EC6D91B03A8386 (created_by_id), UNIQUE INDEX UNIQ_E8EC6D917E8513BF (source_image_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE managed_image_derivative (id INT AUTO_INCREMENT NOT NULL, parent_image_id INT NOT NULL, format_id INT NOT NULL, derivative_id INT NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_901AB6A9A26889BC (parent_image_id), INDEX IDX_901AB6A9D629F605 (format_id), INDEX IDX_901AB6A9B13D9776 (derivative_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE managed_image_derivative_format (id INT AUTO_INCREMENT NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', handle VARCHAR(255) NOT NULL, resolution_x INT NOT NULL, resolution_y INT NOT NULL, quality INT NOT NULL, UNIQUE INDEX UNIQ_AE399D0E918020D9 (handle), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE managed_image ADD CONSTRAINT FK_E8EC6D91B03A8386 FOREIGN KEY (created_by_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE managed_image ADD CONSTRAINT FK_E8EC6D917E8513BF FOREIGN KEY (source_image_id) REFERENCES managed_file (id)');
        $this->addSql('ALTER TABLE managed_image_derivative ADD CONSTRAINT FK_901AB6A9A26889BC FOREIGN KEY (parent_image_id) REFERENCES managed_image (id)');
        $this->addSql('ALTER TABLE managed_image_derivative ADD CONSTRAINT FK_901AB6A9D629F605 FOREIGN KEY (format_id) REFERENCES managed_image_derivative_format (id)');
        $this->addSql('ALTER TABLE managed_image_derivative ADD CONSTRAINT FK_901AB6A9B13D9776 FOREIGN KEY (derivative_id) REFERENCES managed_file (id)');
        $this->addSql('ALTER TABLE managed_file ADD file_hash VARCHAR(64) DEFAULT NULL');
        $this->addSql('ALTER TABLE puzzle_instance CHANGE config config JSON NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE managed_image DROP FOREIGN KEY FK_E8EC6D91B03A8386');
        $this->addSql('ALTER TABLE managed_image DROP FOREIGN KEY FK_E8EC6D917E8513BF');
        $this->addSql('ALTER TABLE managed_image_derivative DROP FOREIGN KEY FK_901AB6A9A26889BC');
        $this->addSql('ALTER TABLE managed_image_derivative DROP FOREIGN KEY FK_901AB6A9D629F605');
        $this->addSql('ALTER TABLE managed_image_derivative DROP FOREIGN KEY FK_901AB6A9B13D9776');
        $this->addSql('DROP TABLE managed_image');
        $this->addSql('DROP TABLE managed_image_derivative');
        $this->addSql('DROP TABLE managed_image_derivative_format');
        $this->addSql('ALTER TABLE puzzle_instance CHANGE config config JSON NOT NULL COMMENT \'(DC2Type:json)\'');
        $this->addSql('ALTER TABLE managed_file DROP file_hash');
    }
}
