<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260307213538 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE user_world (user_id INT NOT NULL, world_id INT NOT NULL, INDEX IDX_D8B5EA1BA76ED395 (user_id), INDEX IDX_D8B5EA1B8925311C (world_id), PRIMARY KEY(user_id, world_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE user_world ADD CONSTRAINT FK_D8B5EA1BA76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_world ADD CONSTRAINT FK_D8B5EA1B8925311C FOREIGN KEY (world_id) REFERENCES world (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_user DROP FOREIGN KEY FK_F7129A80233D34C1');
        $this->addSql('ALTER TABLE user_user DROP FOREIGN KEY FK_F7129A803AD8644E');
        $this->addSql('ALTER TABLE world_user DROP FOREIGN KEY FK_5357149D8925311C');
        $this->addSql('ALTER TABLE world_user DROP FOREIGN KEY FK_5357149DA76ED395');
        $this->addSql('DROP TABLE user_user');
        $this->addSql('DROP TABLE world_user');
        $this->addSql('ALTER TABLE puzzle_instance CHANGE config config JSON NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE user_user (user_source INT NOT NULL, user_target INT NOT NULL, INDEX IDX_F7129A803AD8644E (user_source), INDEX IDX_F7129A80233D34C1 (user_target), PRIMARY KEY(user_source, user_target)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE world_user (world_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_5357149D8925311C (world_id), INDEX IDX_5357149DA76ED395 (user_id), PRIMARY KEY(world_id, user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE user_user ADD CONSTRAINT FK_F7129A80233D34C1 FOREIGN KEY (user_target) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_user ADD CONSTRAINT FK_F7129A803AD8644E FOREIGN KEY (user_source) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE world_user ADD CONSTRAINT FK_5357149D8925311C FOREIGN KEY (world_id) REFERENCES world (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE world_user ADD CONSTRAINT FK_5357149DA76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_world DROP FOREIGN KEY FK_D8B5EA1BA76ED395');
        $this->addSql('ALTER TABLE user_world DROP FOREIGN KEY FK_D8B5EA1B8925311C');
        $this->addSql('DROP TABLE user_world');
        $this->addSql('ALTER TABLE puzzle_instance CHANGE config config JSON NOT NULL COMMENT \'(DC2Type:json)\'');
    }
}
