<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260323121500 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Make managed image derivative height optional and add crop/centering flags.';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE managed_image_derivative_format CHANGE resolution_y resolution_y INT DEFAULT NULL');
        $this->addSql('ALTER TABLE managed_image_derivative_format ADD allow_crop TINYINT(1) NOT NULL DEFAULT 0, ADD centre_image TINYINT(1) NOT NULL DEFAULT 0');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE managed_image_derivative_format DROP allow_crop, DROP centre_image');
        $this->addSql('ALTER TABLE managed_image_derivative_format CHANGE resolution_y resolution_y INT NOT NULL');
    }
}
