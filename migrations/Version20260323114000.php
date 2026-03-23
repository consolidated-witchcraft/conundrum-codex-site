<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260323114000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add missing timestamps to image_type_format_map for AbstractDomainEntity compatibility.';
    }

    public function up(Schema $schema): void
    {
        if (!$this->connection->createSchemaManager()->tablesExist(['image_type_format_map'])) {
            return;
        }

        $columns = array_map(
            static fn ($column): string => $column->getName(),
            $this->connection->createSchemaManager()->listTableColumns('image_type_format_map'),
        );

        if (!in_array('created_at', $columns, true)) {
            $this->addSql("ALTER TABLE image_type_format_map ADD created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '(DC2Type:datetime_immutable)'");
        }

        if (!in_array('updated_at', $columns, true)) {
            $this->addSql("ALTER TABLE image_type_format_map ADD updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '(DC2Type:datetime_immutable)'");
        }
    }

    public function down(Schema $schema): void
    {
        if (!$this->connection->createSchemaManager()->tablesExist(['image_type_format_map'])) {
            return;
        }

        $columns = array_map(
            static fn ($column): string => $column->getName(),
            $this->connection->createSchemaManager()->listTableColumns('image_type_format_map'),
        );

        if (in_array('created_at', $columns, true)) {
            $this->addSql('ALTER TABLE image_type_format_map DROP created_at');
        }

        if (in_array('updated_at', $columns, true)) {
            $this->addSql('ALTER TABLE image_type_format_map DROP updated_at');
        }
    }
}
