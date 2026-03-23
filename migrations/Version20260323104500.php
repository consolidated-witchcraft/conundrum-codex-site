<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260323104500 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add read-only image type to derivative format mapping tables.';
    }

    public function up(Schema $schema): void
    {
        $this->addSql("CREATE TABLE image_type_format_map (id INT AUTO_INCREMENT NOT NULL, managed_image_type VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_DA6C7AB1D0E4530E (managed_image_type), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB");
        $this->addSql('CREATE TABLE image_type_format_map_derivative_format (image_type_format_map_id INT NOT NULL, managed_image_derivative_format_id INT NOT NULL, INDEX IDX_236D179DC1E3F3A7 (image_type_format_map_id), INDEX IDX_236D179D3A6EB7D5 (managed_image_derivative_format_id), PRIMARY KEY(image_type_format_map_id, managed_image_derivative_format_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE image_type_format_map_derivative_format ADD CONSTRAINT FK_236D179DC1E3F3A7 FOREIGN KEY (image_type_format_map_id) REFERENCES image_type_format_map (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE image_type_format_map_derivative_format ADD CONSTRAINT FK_236D179D3A6EB7D5 FOREIGN KEY (managed_image_derivative_format_id) REFERENCES managed_image_derivative_format (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE image_type_format_map_derivative_format DROP FOREIGN KEY FK_236D179DC1E3F3A7');
        $this->addSql('ALTER TABLE image_type_format_map_derivative_format DROP FOREIGN KEY FK_236D179D3A6EB7D5');
        $this->addSql('DROP TABLE image_type_format_map_derivative_format');
        $this->addSql('DROP TABLE image_type_format_map');
    }
}
