<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260831121020 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE campaign_invitation (id SERIAL NOT NULL, campaign_id INT NOT NULL, user_id INT DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, invitation_code VARCHAR(64) NOT NULL, email VARCHAR(255) NOT NULL, invitation_message VARCHAR(255) NOT NULL, expires_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, used_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_360A410ABA14FCCC ON campaign_invitation (invitation_code)');
        $this->addSql('CREATE INDEX IDX_360A410AF639F774 ON campaign_invitation (campaign_id)');
        $this->addSql('CREATE INDEX IDX_360A410AA76ED395 ON campaign_invitation (user_id)');
        $this->addSql('COMMENT ON COLUMN campaign_invitation.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN campaign_invitation.updated_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN campaign_invitation.expires_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN campaign_invitation.used_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE campaign_invitation ADD CONSTRAINT FK_360A410AF639F774 FOREIGN KEY (campaign_id) REFERENCES campaign (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE campaign_invitation ADD CONSTRAINT FK_360A410AA76ED395 FOREIGN KEY (user_id) REFERENCES "user" (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE campaign_invitation DROP CONSTRAINT FK_360A410AF639F774');
        $this->addSql('ALTER TABLE campaign_invitation DROP CONSTRAINT FK_360A410AA76ED395');
        $this->addSql('DROP TABLE campaign_invitation');
    }
}
