<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260828210053 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE character (id SERIAL NOT NULL, campaign_membership_id INT NOT NULL, avatar_id INT NOT NULL, character_sheet_id INT DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, name VARCHAR(255) NOT NULL, description TEXT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_937AB034DE8FF9B7 ON character (campaign_membership_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_937AB03486383B10 ON character (avatar_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_937AB034D313EF34 ON character (character_sheet_id)');
        $this->addSql('COMMENT ON COLUMN character.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN character.updated_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE character ADD CONSTRAINT FK_937AB034DE8FF9B7 FOREIGN KEY (campaign_membership_id) REFERENCES campaign_membership (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE character ADD CONSTRAINT FK_937AB03486383B10 FOREIGN KEY (avatar_id) REFERENCES managed_file (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE character ADD CONSTRAINT FK_937AB034D313EF34 FOREIGN KEY (character_sheet_id) REFERENCES managed_file (id) ON DELETE SET NULL NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE campaign ALTER name TYPE VARCHAR(255)');
        $this->addSql('ALTER TABLE campaign ALTER slug TYPE VARCHAR(255)');
        $this->addSql('ALTER TABLE campaign ALTER description TYPE VARCHAR(1024)');
        $this->addSql('ALTER TABLE casebook_subject_clue ALTER title TYPE VARCHAR(255)');
        $this->addSql('ALTER TABLE casebook_subject_clue ALTER body TYPE VARCHAR(1024)');
        $this->addSql('ALTER TABLE game ALTER name TYPE VARCHAR(255)');
        $this->addSql('ALTER TABLE game ALTER slug TYPE VARCHAR(255)');
        $this->addSql('ALTER TABLE game ALTER description TYPE VARCHAR(1024)');
        $this->addSql('ALTER TABLE order_charge_line ALTER charge_type TYPE VARCHAR(255)');
        $this->addSql('ALTER TABLE orders ALTER order_status TYPE VARCHAR(255)');
        $this->addSql('ALTER TABLE payment ALTER payment_status TYPE VARCHAR(255)');
        $this->addSql('ALTER TABLE play_event ALTER puzzle_instance_code TYPE VARCHAR(255)');
        $this->addSql('ALTER TABLE play_event ALTER puzzle_template_slug TYPE VARCHAR(255)');
        $this->addSql('ALTER TABLE product_variant ALTER type TYPE VARCHAR(255)');
        $this->addSql('ALTER TABLE puzzle_instance ALTER instance_code TYPE VARCHAR(255)');
        $this->addSql('ALTER TABLE puzzle_instance ALTER name TYPE VARCHAR(255)');
        $this->addSql('ALTER TABLE puzzle_instance ALTER description TYPE VARCHAR(2048)');
        $this->addSql('ALTER TABLE puzzle_instance ALTER template_slug TYPE VARCHAR(1024)');
        $this->addSql('ALTER TABLE puzzle_instance ALTER config TYPE JSON');
        $this->addSql('ALTER TABLE ticket ALTER customer_email TYPE VARCHAR(255)');
        $this->addSql('ALTER TABLE "user" ALTER email TYPE VARCHAR(255)');
        $this->addSql('ALTER TABLE "user" ALTER username TYPE VARCHAR(255)');
        $this->addSql('ALTER TABLE "user" ALTER password TYPE VARCHAR(255)');
        $this->addSql('ALTER TABLE "user" ALTER preferences_preferred_font_stack TYPE VARCHAR(255)');
        $this->addSql('ALTER TABLE user_access_token ALTER user_identifier TYPE VARCHAR(1024)');
        $this->addSql('ALTER TABLE user_access_token ALTER token TYPE VARCHAR(128)');
        $this->addSql('ALTER TABLE user_cart_charge ALTER charge_type TYPE VARCHAR(255)');
        $this->addSql('ALTER TABLE user_feat ALTER rarity TYPE VARCHAR(255)');
        $this->addSql('ALTER TABLE world ALTER description TYPE VARCHAR(2198)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE character DROP CONSTRAINT FK_937AB034DE8FF9B7');
        $this->addSql('ALTER TABLE character DROP CONSTRAINT FK_937AB03486383B10');
        $this->addSql('ALTER TABLE character DROP CONSTRAINT FK_937AB034D313EF34');
        $this->addSql('DROP TABLE character');
        $this->addSql('ALTER TABLE orders ALTER order_status TYPE VARCHAR(255)');
        $this->addSql('ALTER TABLE user_access_token ALTER user_identifier TYPE VARCHAR(1024)');
        $this->addSql('ALTER TABLE user_access_token ALTER token TYPE VARCHAR(128)');
        $this->addSql('ALTER TABLE "user" ALTER email TYPE VARCHAR(255)');
        $this->addSql('ALTER TABLE "user" ALTER username TYPE VARCHAR(255)');
        $this->addSql('ALTER TABLE "user" ALTER password TYPE VARCHAR(255)');
        $this->addSql('ALTER TABLE "user" ALTER preferences_preferred_font_stack TYPE VARCHAR(255)');
        $this->addSql('ALTER TABLE user_cart_charge ALTER charge_type TYPE VARCHAR(255)');
        $this->addSql('ALTER TABLE game ALTER name TYPE VARCHAR(255)');
        $this->addSql('ALTER TABLE game ALTER slug TYPE VARCHAR(255)');
        $this->addSql('ALTER TABLE game ALTER description TYPE VARCHAR(1024)');
        $this->addSql('ALTER TABLE user_feat ALTER rarity TYPE VARCHAR(255)');
        $this->addSql('ALTER TABLE world ALTER description TYPE VARCHAR(2198)');
        $this->addSql('ALTER TABLE campaign ALTER name TYPE VARCHAR(255)');
        $this->addSql('ALTER TABLE campaign ALTER slug TYPE VARCHAR(255)');
        $this->addSql('ALTER TABLE campaign ALTER description TYPE VARCHAR(1024)');
        $this->addSql('ALTER TABLE ticket ALTER customer_email TYPE VARCHAR(255)');
        $this->addSql('ALTER TABLE casebook_subject_clue ALTER title TYPE VARCHAR(255)');
        $this->addSql('ALTER TABLE casebook_subject_clue ALTER body TYPE VARCHAR(1024)');
        $this->addSql('ALTER TABLE puzzle_instance ALTER instance_code TYPE VARCHAR(255)');
        $this->addSql('ALTER TABLE puzzle_instance ALTER name TYPE VARCHAR(255)');
        $this->addSql('ALTER TABLE puzzle_instance ALTER description TYPE VARCHAR(2048)');
        $this->addSql('ALTER TABLE puzzle_instance ALTER template_slug TYPE VARCHAR(1024)');
        $this->addSql('ALTER TABLE puzzle_instance ALTER config TYPE JSON');
        $this->addSql('ALTER TABLE play_event ALTER puzzle_instance_code TYPE VARCHAR(255)');
        $this->addSql('ALTER TABLE play_event ALTER puzzle_template_slug TYPE VARCHAR(255)');
        $this->addSql('ALTER TABLE payment ALTER payment_status TYPE VARCHAR(255)');
        $this->addSql('ALTER TABLE product_variant ALTER type TYPE VARCHAR(255)');
        $this->addSql('ALTER TABLE order_charge_line ALTER charge_type TYPE VARCHAR(255)');
    }
}
