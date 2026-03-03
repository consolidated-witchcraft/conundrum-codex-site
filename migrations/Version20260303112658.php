<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260303112658 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE puzzle_instance CHANGE config config JSON NOT NULL');
        $this->addSql('ALTER TABLE user ADD preferenceshas_accepted_cookies TINYINT(1) NOT NULL, ADD preferencesprofile_public TINYINT(1) NOT NULL, DROP has_accepted_cookies, DROP profile_public');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE puzzle_instance CHANGE config config JSON NOT NULL COMMENT \'(DC2Type:json)\'');
        $this->addSql('ALTER TABLE user ADD has_accepted_cookies TINYINT(1) NOT NULL, ADD profile_public TINYINT(1) NOT NULL, DROP preferenceshas_accepted_cookies, DROP preferencesprofile_public');
    }
}
