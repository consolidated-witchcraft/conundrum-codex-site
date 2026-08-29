<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260829115651 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create characters and their notes.';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE character (id SERIAL NOT NULL, user_id INT NOT NULL, portrait_id INT NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, name VARCHAR(255) NOT NULL, character_class VARCHAR(255) NOT NULL, race VARCHAR(255) NOT NULL, npc BOOLEAN DEFAULT false NOT NULL, biography TEXT DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_937AB034A76ED395 ON character (user_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_937AB0341226EBF3 ON character (portrait_id)');
        $this->addSql('COMMENT ON COLUMN character.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN character.updated_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE character_note (id SERIAL NOT NULL, character_id INT NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, subject VARCHAR(255) NOT NULL, body TEXT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_D841EE8C1136BE75 ON character_note (character_id)');
        $this->addSql('COMMENT ON COLUMN character_note.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN character_note.updated_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE character ADD CONSTRAINT FK_937AB034A76ED395 FOREIGN KEY (user_id) REFERENCES "user" (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE character ADD CONSTRAINT FK_937AB0341226EBF3 FOREIGN KEY (portrait_id) REFERENCES managed_image (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE character_note ADD CONSTRAINT FK_D841EE8C1136BE75 FOREIGN KEY (character_id) REFERENCES character (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE character_note DROP CONSTRAINT FK_D841EE8C1136BE75');
        $this->addSql('ALTER TABLE character DROP CONSTRAINT FK_937AB034A76ED395');
        $this->addSql('ALTER TABLE character DROP CONSTRAINT FK_937AB0341226EBF3');
        $this->addSql('DROP TABLE character_note');
        $this->addSql('DROP TABLE character');
    }
}
