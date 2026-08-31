<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260829194007 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add characters to campaign memberships with one active character per membership.';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE campaign_character (id SERIAL NOT NULL, campaign_membership_id INT NOT NULL, character_id INT NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, active BOOLEAN DEFAULT false NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_53F2AC4FDE8FF9B7 ON campaign_character (campaign_membership_id)');
        $this->addSql('CREATE INDEX IDX_53F2AC4F1136BE75 ON campaign_character (character_id)');
        $this->addSql('CREATE UNIQUE INDEX campaign_character_membership_character_unique ON campaign_character (campaign_membership_id, character_id)');
        $this->addSql('CREATE UNIQUE INDEX campaign_character_active_membership_unique ON campaign_character (campaign_membership_id) WHERE (active = true)');
        $this->addSql('COMMENT ON COLUMN campaign_character.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN campaign_character.updated_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE campaign_character ADD CONSTRAINT FK_53F2AC4FDE8FF9B7 FOREIGN KEY (campaign_membership_id) REFERENCES campaign_membership (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE campaign_character ADD CONSTRAINT FK_53F2AC4F1136BE75 FOREIGN KEY (character_id) REFERENCES character (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE campaign_character DROP CONSTRAINT FK_53F2AC4FDE8FF9B7');
        $this->addSql('ALTER TABLE campaign_character DROP CONSTRAINT FK_53F2AC4F1136BE75');
        $this->addSql('DROP TABLE campaign_character');
    }
}
