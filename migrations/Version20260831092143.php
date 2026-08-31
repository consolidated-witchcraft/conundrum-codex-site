<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260831092143 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE campaign ALTER name TYPE VARCHAR(255)');
        $this->addSql('ALTER TABLE campaign ALTER slug TYPE VARCHAR(255)');
        $this->addSql('ALTER TABLE campaign ALTER description TYPE VARCHAR(1024)');
        $this->addSql('ALTER TABLE casebook_subject_clue ALTER title TYPE VARCHAR(255)');
        $this->addSql('ALTER TABLE casebook_subject_clue ALTER body TYPE VARCHAR(1024)');
        $this->addSql('ALTER TABLE "character" ALTER name TYPE VARCHAR(255)');
        $this->addSql('ALTER TABLE "character" ALTER character_class TYPE VARCHAR(255)');
        $this->addSql('ALTER TABLE "character" ALTER race TYPE VARCHAR(255)');
        $this->addSql('ALTER TABLE character_note ALTER subject TYPE VARCHAR(255)');
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
        $this->addSql('ALTER TABLE character ALTER name TYPE VARCHAR(255)');
        $this->addSql('ALTER TABLE character ALTER character_class TYPE VARCHAR(255)');
        $this->addSql('ALTER TABLE character ALTER race TYPE VARCHAR(255)');
        $this->addSql('ALTER TABLE user_feat ALTER rarity TYPE VARCHAR(255)');
        $this->addSql('ALTER TABLE "user" ALTER email TYPE VARCHAR(255)');
        $this->addSql('ALTER TABLE "user" ALTER username TYPE VARCHAR(255)');
        $this->addSql('ALTER TABLE "user" ALTER password TYPE VARCHAR(255)');
        $this->addSql('ALTER TABLE "user" ALTER preferences_preferred_font_stack TYPE VARCHAR(255)');
        $this->addSql('ALTER TABLE campaign ALTER name TYPE VARCHAR(255)');
        $this->addSql('ALTER TABLE campaign ALTER slug TYPE VARCHAR(255)');
        $this->addSql('ALTER TABLE campaign ALTER description TYPE VARCHAR(1024)');
        $this->addSql('ALTER TABLE world ALTER description TYPE VARCHAR(2198)');
        $this->addSql('ALTER TABLE character_note ALTER subject TYPE VARCHAR(255)');
        $this->addSql('ALTER TABLE ticket ALTER customer_email TYPE VARCHAR(255)');
        $this->addSql('ALTER TABLE orders ALTER order_status TYPE VARCHAR(255)');
        $this->addSql('ALTER TABLE casebook_subject_clue ALTER title TYPE VARCHAR(255)');
        $this->addSql('ALTER TABLE casebook_subject_clue ALTER body TYPE VARCHAR(1024)');
        $this->addSql('ALTER TABLE puzzle_instance ALTER instance_code TYPE VARCHAR(255)');
        $this->addSql('ALTER TABLE puzzle_instance ALTER name TYPE VARCHAR(255)');
        $this->addSql('ALTER TABLE puzzle_instance ALTER description TYPE VARCHAR(2048)');
        $this->addSql('ALTER TABLE puzzle_instance ALTER template_slug TYPE VARCHAR(1024)');
        $this->addSql('ALTER TABLE puzzle_instance ALTER config TYPE JSON');
        $this->addSql('ALTER TABLE game ALTER name TYPE VARCHAR(255)');
        $this->addSql('ALTER TABLE game ALTER slug TYPE VARCHAR(255)');
        $this->addSql('ALTER TABLE game ALTER description TYPE VARCHAR(1024)');
        $this->addSql('ALTER TABLE play_event ALTER puzzle_instance_code TYPE VARCHAR(255)');
        $this->addSql('ALTER TABLE play_event ALTER puzzle_template_slug TYPE VARCHAR(255)');
        $this->addSql('ALTER TABLE order_charge_line ALTER charge_type TYPE VARCHAR(255)');
        $this->addSql('ALTER TABLE user_cart_charge ALTER charge_type TYPE VARCHAR(255)');
        $this->addSql('ALTER TABLE payment ALTER payment_status TYPE VARCHAR(255)');
        $this->addSql('ALTER TABLE product_variant ALTER type TYPE VARCHAR(255)');
        $this->addSql('ALTER TABLE user_access_token ALTER user_identifier TYPE VARCHAR(1024)');
        $this->addSql('ALTER TABLE user_access_token ALTER token TYPE VARCHAR(128)');
    }
}
