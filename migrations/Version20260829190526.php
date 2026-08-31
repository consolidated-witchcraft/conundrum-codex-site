<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260829190526 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add an optional character sheet managed file to characters.';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE "character" ADD character_sheet_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE "character" ADD CONSTRAINT FK_937AB034D313EF34 FOREIGN KEY (character_sheet_id) REFERENCES managed_file (id) ON DELETE SET NULL NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_937AB034D313EF34 ON "character" (character_sheet_id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE "character" DROP CONSTRAINT FK_937AB034D313EF34');
        $this->addSql('DROP INDEX UNIQ_937AB034D313EF34');
        $this->addSql('ALTER TABLE "character" DROP character_sheet_id');
    }
}
