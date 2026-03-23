<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260322224919 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        if (!$this->tableExists('product_variant_managed_image')) {
            $this->addSql('CREATE TABLE product_variant_managed_image (product_variant_id INT NOT NULL, managed_image_id INT NOT NULL, INDEX IDX_452D8056A80EF684 (product_variant_id), INDEX IDX_452D8056E37FACA6 (managed_image_id), PRIMARY KEY(product_variant_id, managed_image_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        }

        if ($this->tableExists('product_variant_managed_file')) {
            $this->addSql('INSERT IGNORE INTO product_variant_managed_image (product_variant_id, managed_image_id)
                SELECT product_variant_managed_file.product_variant_id, managed_image.id
                FROM product_variant_managed_file
                INNER JOIN managed_image ON managed_image.source_image_id = product_variant_managed_file.managed_file_id');
        }

        if (!$this->foreignKeyExists('product_variant_managed_image', 'FK_452D8056A80EF684')) {
            $this->addSql('ALTER TABLE product_variant_managed_image ADD CONSTRAINT FK_452D8056A80EF684 FOREIGN KEY (product_variant_id) REFERENCES product_variant (id) ON DELETE CASCADE');
        }

        if (!$this->foreignKeyExists('product_variant_managed_image', 'FK_452D8056E37FACA6')) {
            $this->addSql('ALTER TABLE product_variant_managed_image ADD CONSTRAINT FK_452D8056E37FACA6 FOREIGN KEY (managed_image_id) REFERENCES managed_image (id) ON DELETE CASCADE');
        }

        if ($this->tableExists('product_variant_managed_file')) {
            if ($this->foreignKeyExists('product_variant_managed_file', 'FK_F97CF485A80EF684')) {
                $this->addSql('ALTER TABLE product_variant_managed_file DROP FOREIGN KEY FK_F97CF485A80EF684');
            }

            if ($this->foreignKeyExists('product_variant_managed_file', 'FK_F97CF485F2DC7781')) {
                $this->addSql('ALTER TABLE product_variant_managed_file DROP FOREIGN KEY FK_F97CF485F2DC7781');
            }

            $this->addSql('DROP TABLE product_variant_managed_file');
        }

        $this->migrateManagedImageReference(
            tableName: 'casebook_subject',
            columnName: 'casebook_subject_image_file_id',
            foreignKeyName: 'FK_911EC37B51412E32',
            joinType: 'LEFT JOIN',
            foreignKeySql: 'ALTER TABLE casebook_subject ADD CONSTRAINT FK_911EC37B51412E32 FOREIGN KEY (casebook_subject_image_file_id) REFERENCES managed_image (id) ON DELETE SET NULL',
        );
        $this->migrateManagedImageReference(
            tableName: 'event',
            columnName: 'hero_image_id',
            foreignKeyName: 'FK_3BAE0AA798BB94C5',
            joinType: 'INNER JOIN',
            foreignKeySql: 'ALTER TABLE event ADD CONSTRAINT FK_3BAE0AA798BB94C5 FOREIGN KEY (hero_image_id) REFERENCES managed_image (id)',
        );
        $this->migrateManagedImageReference(
            tableName: 'game',
            columnName: 'hero_image_file_id',
            foreignKeyName: 'FK_232B318C268A15CE',
            joinType: 'LEFT JOIN',
            foreignKeySql: 'ALTER TABLE game ADD CONSTRAINT FK_232B318C268A15CE FOREIGN KEY (hero_image_file_id) REFERENCES managed_image (id)',
        );
        $this->migrateManagedImageReference(
            tableName: 'game_session',
            columnName: 'session_image_id',
            foreignKeyName: 'FK_4586AAFBB6094505',
            joinType: 'LEFT JOIN',
            foreignKeySql: 'ALTER TABLE game_session ADD CONSTRAINT FK_4586AAFBB6094505 FOREIGN KEY (session_image_id) REFERENCES managed_image (id)',
        );
        $this->migrateManagedImageReference(
            tableName: 'organisation',
            columnName: 'organisation_logo_id',
            foreignKeyName: 'FK_E6E132B49A0B6349',
            joinType: 'INNER JOIN',
            foreignKeySql: 'ALTER TABLE organisation ADD CONSTRAINT FK_E6E132B49A0B6349 FOREIGN KEY (organisation_logo_id) REFERENCES managed_image (id)',
        );
        $this->addSql('ALTER TABLE puzzle_instance CHANGE config config JSON NOT NULL');
        $this->migrateManagedImageReference(
            tableName: 'user',
            columnName: 'avatar_id',
            foreignKeyName: 'FK_8D93D64986383B10',
            joinType: 'LEFT JOIN',
            foreignKeySql: 'ALTER TABLE user ADD CONSTRAINT FK_8D93D64986383B10 FOREIGN KEY (avatar_id) REFERENCES managed_image (id) ON DELETE SET NULL',
        );
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE product_variant_managed_file (product_variant_id INT NOT NULL, managed_file_id INT NOT NULL, INDEX IDX_F97CF485A80EF684 (product_variant_id), INDEX IDX_F97CF485F2DC7781 (managed_file_id), PRIMARY KEY(product_variant_id, managed_file_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE product_variant_managed_file ADD CONSTRAINT FK_F97CF485A80EF684 FOREIGN KEY (product_variant_id) REFERENCES product_variant (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE product_variant_managed_file ADD CONSTRAINT FK_F97CF485F2DC7781 FOREIGN KEY (managed_file_id) REFERENCES managed_file (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE product_variant_managed_image DROP FOREIGN KEY FK_452D8056A80EF684');
        $this->addSql('ALTER TABLE product_variant_managed_image DROP FOREIGN KEY FK_452D8056E37FACA6');
        $this->addSql('DROP TABLE product_variant_managed_image');
        $this->addSql('ALTER TABLE puzzle_instance CHANGE config config JSON NOT NULL COMMENT \'(DC2Type:json)\'');
        $this->addSql('ALTER TABLE game DROP FOREIGN KEY FK_232B318C268A15CE');
        $this->addSql('ALTER TABLE game ADD CONSTRAINT FK_232B318C268A15CE FOREIGN KEY (hero_image_file_id) REFERENCES managed_file (id)');
        $this->addSql('ALTER TABLE casebook_subject DROP FOREIGN KEY FK_911EC37B51412E32');
        $this->addSql('ALTER TABLE casebook_subject ADD CONSTRAINT FK_911EC37B51412E32 FOREIGN KEY (casebook_subject_image_file_id) REFERENCES managed_file (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE game_session DROP FOREIGN KEY FK_4586AAFBB6094505');
        $this->addSql('ALTER TABLE game_session ADD CONSTRAINT FK_4586AAFBB6094505 FOREIGN KEY (session_image_id) REFERENCES managed_file (id)');
        $this->addSql('ALTER TABLE organisation DROP FOREIGN KEY FK_E6E132B49A0B6349');
        $this->addSql('ALTER TABLE organisation ADD CONSTRAINT FK_E6E132B49A0B6349 FOREIGN KEY (organisation_logo_id) REFERENCES managed_file (id)');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D64986383B10');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D64986383B10 FOREIGN KEY (avatar_id) REFERENCES managed_file (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE event DROP FOREIGN KEY FK_3BAE0AA798BB94C5');
        $this->addSql('ALTER TABLE event ADD CONSTRAINT FK_3BAE0AA798BB94C5 FOREIGN KEY (hero_image_id) REFERENCES managed_file (id)');
    }

    private function migrateManagedImageReference(
        string $tableName,
        string $columnName,
        string $foreignKeyName,
        string $joinType,
        string $foreignKeySql,
    ): void {
        if ($this->foreignKeyReferencesTable($tableName, $foreignKeyName, 'managed_image')) {
            return;
        }

        $this->createMissingManagedImagesForReference($tableName, $columnName);

        if ($this->foreignKeyExists($tableName, $foreignKeyName)) {
            $this->addSql(sprintf('ALTER TABLE %s DROP FOREIGN KEY %s', $tableName, $foreignKeyName));
        }

        $this->addSql(sprintf(
            'UPDATE %1$s
                %2$s managed_image ON managed_image.source_image_id = %1$s.%3$s
                SET %1$s.%3$s = managed_image.id',
            $tableName,
            $joinType,
            $columnName,
        ));
        $this->addSql($foreignKeySql);
    }

    private function createMissingManagedImagesForReference(string $tableName, string $columnName): void
    {
        $this->addSql(sprintf(
            'INSERT INTO managed_image (created_by_id, source_image_id, created_at, updated_at, description)
                SELECT managed_file.uploader_id, managed_file.id, managed_file.created_at, managed_file.updated_at, managed_file.description
                FROM %1$s
                INNER JOIN managed_file ON managed_file.id = %1$s.%2$s
                LEFT JOIN managed_image ON managed_image.source_image_id = managed_file.id
                WHERE %1$s.%2$s IS NOT NULL
                  AND managed_image.id IS NULL',
            $tableName,
            $columnName,
        ));
    }

    private function tableExists(string $tableName): bool
    {
        return $this->connection->createSchemaManager()->tablesExist([$tableName]);
    }

    private function foreignKeyExists(string $tableName, string $foreignKeyName): bool
    {
        foreach ($this->connection->createSchemaManager()->listTableForeignKeys($tableName) as $foreignKey) {
            if ($foreignKey->getName() === $foreignKeyName) {
                return true;
            }
        }

        return false;
    }

    private function foreignKeyReferencesTable(string $tableName, string $foreignKeyName, string $referencedTable): bool
    {
        foreach ($this->connection->createSchemaManager()->listTableForeignKeys($tableName) as $foreignKey) {
            if ($foreignKey->getName() !== $foreignKeyName) {
                continue;
            }

            return $foreignKey->getForeignTableName() === $referencedTable;
        }

        return false;
    }
}
