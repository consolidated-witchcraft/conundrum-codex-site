<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260831091741 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add a non-null level column to characters with a default of 1.';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE "character" ADD level INT DEFAULT 1 NOT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE "character" DROP level');
    }
}
