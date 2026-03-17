<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260316103000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Increase world calendar day of week abbreviation length to four characters';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE world_calendar_day_of_week CHANGE abbreviation abbreviation VARCHAR(4) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE world_calendar_day_of_week CHANGE abbreviation abbreviation VARCHAR(3) NOT NULL');
    }
}
