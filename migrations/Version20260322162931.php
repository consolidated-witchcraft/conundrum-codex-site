<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260322162931 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        if (!$this->tableExists('world_edge')) {
            $this->addSql('CREATE TABLE world_edge (id INT AUTO_INCREMENT NOT NULL, from_entity_id INT NOT NULL, to_entity_id INT NOT NULL, source_revision_id INT NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', relationship_type VARCHAR(100) NOT NULL, revealed_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', valid_from DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', valid_to DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', description LONGTEXT DEFAULT NULL, is_inferred TINYINT(1) DEFAULT 0 NOT NULL, INDEX idx_world_edge_from (from_entity_id), INDEX idx_world_edge_to (to_entity_id), INDEX idx_world_edge_type (relationship_type), INDEX idx_world_edge_source_revision (source_revision_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        }

        if (!$this->tableExists('world_entity')) {
            $this->addSql('CREATE TABLE world_entity (id INT AUTO_INCREMENT NOT NULL, current_revision_id INT DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', slug VARCHAR(255) NOT NULL, current_name VARCHAR(255) NOT NULL, revealed_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', status VARCHAR(32) DEFAULT \'canonical\' NOT NULL, entity_type VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_9E16392B989D9B62 (slug), INDEX IDX_9E16392BA32ED756 (current_revision_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        }

        if (!$this->tableExists('world_entity_revision')) {
            $this->addSql('CREATE TABLE world_entity_revision (id INT AUTO_INCREMENT NOT NULL, entity_id INT NOT NULL, created_by_id INT DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', revision_number INT NOT NULL, source_markdown LONGTEXT NOT NULL, source_hash VARCHAR(64) NOT NULL, parser_version VARCHAR(32) NOT NULL, ast_json JSON DEFAULT NULL COMMENT \'(DC2Type:json)\', ast_source_hash VARCHAR(64) DEFAULT NULL, parse_errors_json JSON DEFAULT NULL COMMENT \'(DC2Type:json)\', parse_status VARCHAR(32) NOT NULL, INDEX IDX_FAE7DA43B03A8386 (created_by_id), INDEX idx_world_entity_revision_entity (entity_id), UNIQUE INDEX uniq_entity_revision_number (entity_id, revision_number), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        }

        if (!$this->tableExists('world_event_participation')) {
            $this->addSql('CREATE TABLE world_event_participation (id INT AUTO_INCREMENT NOT NULL, event_id INT NOT NULL, participant_entity_id INT NOT NULL, source_revision_id INT NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', role VARCHAR(100) NOT NULL, revealed_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', description LONGTEXT DEFAULT NULL, INDEX idx_world_event_participation_event (event_id), INDEX idx_world_event_participation_participant (participant_entity_id), INDEX idx_world_event_participation_source_revision (source_revision_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        }

        if (!$this->tableExists('world_fact')) {
            $this->addSql('CREATE TABLE world_fact (id INT AUTO_INCREMENT NOT NULL, subject_entity_id INT NOT NULL, source_revision_id INT NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', fact_type VARCHAR(100) NOT NULL, value_text LONGTEXT NOT NULL, revealed_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX idx_world_fact_subject (subject_entity_id), INDEX idx_world_fact_type (fact_type), INDEX idx_world_fact_source_revision (source_revision_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        }

        if (!$this->tableExists('world_faction')) {
            $this->addSql('CREATE TABLE world_faction (id INT NOT NULL, faction_name VARCHAR(100) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        }

        if (!$this->tableExists('world_item')) {
            $this->addSql('CREATE TABLE world_item (id INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        }

        if (!$this->tableExists('world_location')) {
            $this->addSql('CREATE TABLE world_location (id INT NOT NULL, location_type VARCHAR(100) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        }

        if (!$this->tableExists('world_person')) {
            $this->addSql('CREATE TABLE world_person (id INT NOT NULL, species VARCHAR(100) DEFAULT NULL, gender VARCHAR(100) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        }

        $this->addForeignKeyIfMissing('world_edge', 'FK_ABC211B291B9E8E7', 'ALTER TABLE world_edge ADD CONSTRAINT FK_ABC211B291B9E8E7 FOREIGN KEY (from_entity_id) REFERENCES world_entity (id) ON DELETE CASCADE');
        $this->addForeignKeyIfMissing('world_edge', 'FK_ABC211B2B086AA2C', 'ALTER TABLE world_edge ADD CONSTRAINT FK_ABC211B2B086AA2C FOREIGN KEY (to_entity_id) REFERENCES world_entity (id) ON DELETE CASCADE');
        $this->addForeignKeyIfMissing('world_edge', 'FK_ABC211B221852C2F', 'ALTER TABLE world_edge ADD CONSTRAINT FK_ABC211B221852C2F FOREIGN KEY (source_revision_id) REFERENCES world_entity_revision (id) ON DELETE CASCADE');
        $this->addForeignKeyIfMissing('world_entity', 'FK_9E16392BA32ED756', 'ALTER TABLE world_entity ADD CONSTRAINT FK_9E16392BA32ED756 FOREIGN KEY (current_revision_id) REFERENCES world_entity_revision (id) ON DELETE SET NULL');
        $this->addForeignKeyIfMissing('world_entity_revision', 'FK_FAE7DA4381257D5D', 'ALTER TABLE world_entity_revision ADD CONSTRAINT FK_FAE7DA4381257D5D FOREIGN KEY (entity_id) REFERENCES world_entity (id) ON DELETE CASCADE');
        $this->addForeignKeyIfMissing('world_entity_revision', 'FK_FAE7DA43B03A8386', 'ALTER TABLE world_entity_revision ADD CONSTRAINT FK_FAE7DA43B03A8386 FOREIGN KEY (created_by_id) REFERENCES user (id)');
        $this->addForeignKeyIfMissing('world_event_participation', 'FK_8E19C76F71F7E88B', 'ALTER TABLE world_event_participation ADD CONSTRAINT FK_8E19C76F71F7E88B FOREIGN KEY (event_id) REFERENCES world_event (id) ON DELETE CASCADE');
        $this->addForeignKeyIfMissing('world_event_participation', 'FK_8E19C76F4227B279', 'ALTER TABLE world_event_participation ADD CONSTRAINT FK_8E19C76F4227B279 FOREIGN KEY (participant_entity_id) REFERENCES world_entity (id) ON DELETE CASCADE');
        $this->addForeignKeyIfMissing('world_event_participation', 'FK_8E19C76F21852C2F', 'ALTER TABLE world_event_participation ADD CONSTRAINT FK_8E19C76F21852C2F FOREIGN KEY (source_revision_id) REFERENCES world_entity_revision (id) ON DELETE CASCADE');
        $this->addForeignKeyIfMissing('world_fact', 'FK_B1609941C274E977', 'ALTER TABLE world_fact ADD CONSTRAINT FK_B1609941C274E977 FOREIGN KEY (subject_entity_id) REFERENCES world_entity (id) ON DELETE CASCADE');
        $this->addForeignKeyIfMissing('world_fact', 'FK_B160994121852C2F', 'ALTER TABLE world_fact ADD CONSTRAINT FK_B160994121852C2F FOREIGN KEY (source_revision_id) REFERENCES world_entity_revision (id) ON DELETE CASCADE');
        $this->addForeignKeyIfMissing('world_faction', 'FK_6C41A5C7BF396750', 'ALTER TABLE world_faction ADD CONSTRAINT FK_6C41A5C7BF396750 FOREIGN KEY (id) REFERENCES world_entity (id) ON DELETE CASCADE');
        $this->addForeignKeyIfMissing('world_item', 'FK_C1DFE7CABF396750', 'ALTER TABLE world_item ADD CONSTRAINT FK_C1DFE7CABF396750 FOREIGN KEY (id) REFERENCES world_entity (id) ON DELETE CASCADE');
        $this->addForeignKeyIfMissing('world_location', 'FK_AB7E08B2BF396750', 'ALTER TABLE world_location ADD CONSTRAINT FK_AB7E08B2BF396750 FOREIGN KEY (id) REFERENCES world_entity (id) ON DELETE CASCADE');
        $this->addForeignKeyIfMissing('world_person', 'FK_A4E2AC35BF396750', 'ALTER TABLE world_person ADD CONSTRAINT FK_A4E2AC35BF396750 FOREIGN KEY (id) REFERENCES world_entity (id) ON DELETE CASCADE');

        if ($this->tableExists('puzzle_instance') && $this->columnExists('puzzle_instance', 'config')) {
            $this->addSql('ALTER TABLE puzzle_instance CHANGE config config JSON NOT NULL');
        }

        $this->migrateWorldEventTable();
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE world_event DROP FOREIGN KEY FK_BACED8A8BF396750');
        $this->addSql('ALTER TABLE world_event DROP FOREIGN KEY FK_BACED8A864D218E');
        $this->addSql('ALTER TABLE world_edge DROP FOREIGN KEY FK_ABC211B291B9E8E7');
        $this->addSql('ALTER TABLE world_edge DROP FOREIGN KEY FK_ABC211B2B086AA2C');
        $this->addSql('ALTER TABLE world_edge DROP FOREIGN KEY FK_ABC211B221852C2F');
        $this->addSql('ALTER TABLE world_entity DROP FOREIGN KEY FK_9E16392BA32ED756');
        $this->addSql('ALTER TABLE world_entity_revision DROP FOREIGN KEY FK_FAE7DA4381257D5D');
        $this->addSql('ALTER TABLE world_entity_revision DROP FOREIGN KEY FK_FAE7DA43B03A8386');
        $this->addSql('ALTER TABLE world_event_participation DROP FOREIGN KEY FK_8E19C76F71F7E88B');
        $this->addSql('ALTER TABLE world_event_participation DROP FOREIGN KEY FK_8E19C76F4227B279');
        $this->addSql('ALTER TABLE world_event_participation DROP FOREIGN KEY FK_8E19C76F21852C2F');
        $this->addSql('ALTER TABLE world_fact DROP FOREIGN KEY FK_B1609941C274E977');
        $this->addSql('ALTER TABLE world_fact DROP FOREIGN KEY FK_B160994121852C2F');
        $this->addSql('ALTER TABLE world_faction DROP FOREIGN KEY FK_6C41A5C7BF396750');
        $this->addSql('ALTER TABLE world_item DROP FOREIGN KEY FK_C1DFE7CABF396750');
        $this->addSql('ALTER TABLE world_location DROP FOREIGN KEY FK_AB7E08B2BF396750');
        $this->addSql('ALTER TABLE world_person DROP FOREIGN KEY FK_A4E2AC35BF396750');
        $this->addSql('DROP TABLE world_edge');
        $this->addSql('DROP TABLE world_entity');
        $this->addSql('DROP TABLE world_entity_revision');
        $this->addSql('DROP TABLE world_event_participation');
        $this->addSql('DROP TABLE world_fact');
        $this->addSql('DROP TABLE world_faction');
        $this->addSql('DROP TABLE world_item');
        $this->addSql('DROP TABLE world_location');
        $this->addSql('DROP TABLE world_person');
        $this->addSql('DROP INDEX IDX_BACED8A864D218E ON world_event');
        $this->addSql('ALTER TABLE world_event ADD world_id INT NOT NULL, ADD created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', ADD updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', ADD timestamp BIGINT NOT NULL, ADD title VARCHAR(255) NOT NULL, ADD description VARCHAR(5000) NOT NULL, DROP location_id, DROP start_at, DROP end_at, DROP summary, CHANGE id id INT AUTO_INCREMENT NOT NULL');
        $this->addSql('ALTER TABLE world_event ADD CONSTRAINT FK_BACED8A88925311C FOREIGN KEY (world_id) REFERENCES world (id)');
        $this->addSql('CREATE INDEX IDX_BACED8A88925311C ON world_event (world_id)');
        $this->addSql('ALTER TABLE puzzle_instance CHANGE config config JSON NOT NULL COMMENT \'(DC2Type:json)\'');
    }

    private function migrateWorldEventTable(): void
    {
        if (!$this->tableExists('world_event')) {
            return;
        }

        if ($this->foreignKeyExists('world_event', 'FK_BACED8A88925311C')) {
            $this->addSql('ALTER TABLE world_event DROP FOREIGN KEY FK_BACED8A88925311C');
        }

        if ($this->indexExists('world_event', 'IDX_BACED8A88925311C')) {
            $this->addSql('DROP INDEX IDX_BACED8A88925311C ON world_event');
        }

        $alterClauses = [];
        if (!$this->columnExists('world_event', 'location_id')) {
            $alterClauses[] = 'ADD location_id INT DEFAULT NULL';
        }
        if (!$this->columnExists('world_event', 'start_at')) {
            $alterClauses[] = 'ADD start_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\'';
        }
        if (!$this->columnExists('world_event', 'end_at')) {
            $alterClauses[] = 'ADD end_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\'';
        }
        if (!$this->columnExists('world_event', 'summary')) {
            $alterClauses[] = 'ADD summary LONGTEXT DEFAULT NULL';
        }
        if ($this->columnExists('world_event', 'world_id')) {
            $alterClauses[] = 'DROP world_id';
        }
        if ($this->columnExists('world_event', 'created_at')) {
            $alterClauses[] = 'DROP created_at';
        }
        if ($this->columnExists('world_event', 'updated_at')) {
            $alterClauses[] = 'DROP updated_at';
        }
        if ($this->columnExists('world_event', 'timestamp')) {
            $alterClauses[] = 'DROP timestamp';
        }
        if ($this->columnExists('world_event', 'title')) {
            $alterClauses[] = 'DROP title';
        }
        if ($this->columnExists('world_event', 'description')) {
            $alterClauses[] = 'DROP description';
        }
        if ($this->idColumnAutoIncrements('world_event')) {
            $alterClauses[] = 'CHANGE id id INT NOT NULL';
        }

        if ($alterClauses !== []) {
            $this->addSql(sprintf('ALTER TABLE world_event %s', implode(', ', $alterClauses)));
        }

        $this->addForeignKeyIfMissing('world_event', 'FK_BACED8A864D218E', 'ALTER TABLE world_event ADD CONSTRAINT FK_BACED8A864D218E FOREIGN KEY (location_id) REFERENCES world_location (id) ON DELETE SET NULL');
        $this->addForeignKeyIfMissing('world_event', 'FK_BACED8A8BF396750', 'ALTER TABLE world_event ADD CONSTRAINT FK_BACED8A8BF396750 FOREIGN KEY (id) REFERENCES world_entity (id) ON DELETE CASCADE');

        if (!$this->indexExists('world_event', 'IDX_BACED8A864D218E') && $this->columnExists('world_event', 'location_id')) {
            $this->addSql('CREATE INDEX IDX_BACED8A864D218E ON world_event (location_id)');
        }
    }

    private function tableExists(string $tableName): bool
    {
        return $this->connection->createSchemaManager()->tablesExist([$tableName]);
    }

    private function columnExists(string $tableName, string $columnName): bool
    {
        if (!$this->tableExists($tableName)) {
            return false;
        }

        return in_array(
            $columnName,
            array_map(
                static fn ($column): string => $column->getName(),
                $this->connection->createSchemaManager()->listTableColumns($tableName),
            ),
            true,
        );
    }

    private function indexExists(string $tableName, string $indexName): bool
    {
        if (!$this->tableExists($tableName)) {
            return false;
        }

        foreach ($this->connection->createSchemaManager()->listTableIndexes($tableName) as $index) {
            if ($index->getName() === $indexName) {
                return true;
            }
        }

        return false;
    }

    private function foreignKeyExists(string $tableName, string $foreignKeyName): bool
    {
        if (!$this->tableExists($tableName)) {
            return false;
        }

        foreach ($this->connection->createSchemaManager()->listTableForeignKeys($tableName) as $foreignKey) {
            if ($foreignKey->getName() === $foreignKeyName) {
                return true;
            }
        }

        return false;
    }

    private function addForeignKeyIfMissing(string $tableName, string $foreignKeyName, string $sql): void
    {
        if (!$this->tableExists($tableName) || $this->foreignKeyExists($tableName, $foreignKeyName)) {
            return;
        }

        $this->addSql($sql);
    }

    private function idColumnAutoIncrements(string $tableName): bool
    {
        if (!$this->columnExists($tableName, 'id')) {
            return false;
        }

        return $this->connection->createSchemaManager()->listTableColumns($tableName)['id']->getAutoincrement();
    }
}
