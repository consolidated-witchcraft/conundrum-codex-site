<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260323101500 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add managed image type enum-backed string column.';
    }

    public function up(Schema $schema): void
    {
        $this->addSql("ALTER TABLE managed_image ADD managed_image_type VARCHAR(255) NOT NULL DEFAULT 'default'");
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE managed_image DROP managed_image_type');
    }
}
