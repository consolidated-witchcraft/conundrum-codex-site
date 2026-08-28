<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260828172133 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create campaigns and their user memberships';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE campaign (id SERIAL NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, name VARCHAR(255) NOT NULL, slug VARCHAR(255) NOT NULL, description VARCHAR(1024) NOT NULL, status VARCHAR(255) NOT NULL, start_date TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_1F1512DD989D9B62 ON campaign (slug)');
        $this->addSql('COMMENT ON COLUMN campaign.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN campaign.updated_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN campaign.start_date IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE campaign_membership (id SERIAL NOT NULL, campaign_id INT NOT NULL, user_id INT NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, type VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_41E8F22DF639F774 ON campaign_membership (campaign_id)');
        $this->addSql('CREATE INDEX IDX_41E8F22DA76ED395 ON campaign_membership (user_id)');
        $this->addSql('CREATE UNIQUE INDEX campaign_membership_campaign_user_unique ON campaign_membership (campaign_id, user_id)');
        $this->addSql('COMMENT ON COLUMN campaign_membership.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN campaign_membership.updated_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE campaign_membership ADD CONSTRAINT FK_41E8F22DF639F774 FOREIGN KEY (campaign_id) REFERENCES campaign (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE campaign_membership ADD CONSTRAINT FK_41E8F22DA76ED395 FOREIGN KEY (user_id) REFERENCES "user" (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE campaign_membership DROP CONSTRAINT FK_41E8F22DF639F774');
        $this->addSql('ALTER TABLE campaign_membership DROP CONSTRAINT FK_41E8F22DA76ED395');
        $this->addSql('DROP TABLE campaign');
        $this->addSql('DROP TABLE campaign_membership');
    }
}
