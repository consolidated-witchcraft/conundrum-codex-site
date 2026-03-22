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
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE product_variant_managed_image (product_variant_id INT NOT NULL, managed_image_id INT NOT NULL, INDEX IDX_452D8056A80EF684 (product_variant_id), INDEX IDX_452D8056E37FACA6 (managed_image_id), PRIMARY KEY(product_variant_id, managed_image_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('INSERT INTO product_variant_managed_image (product_variant_id, managed_image_id)
            SELECT product_variant_managed_file.product_variant_id, managed_image.id
            FROM product_variant_managed_file
            INNER JOIN managed_image ON managed_image.source_image_id = product_variant_managed_file.managed_file_id');
        $this->addSql('ALTER TABLE product_variant_managed_image ADD CONSTRAINT FK_452D8056A80EF684 FOREIGN KEY (product_variant_id) REFERENCES product_variant (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE product_variant_managed_image ADD CONSTRAINT FK_452D8056E37FACA6 FOREIGN KEY (managed_image_id) REFERENCES managed_image (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE product_variant_managed_file DROP FOREIGN KEY FK_F97CF485A80EF684');
        $this->addSql('ALTER TABLE product_variant_managed_file DROP FOREIGN KEY FK_F97CF485F2DC7781');
        $this->addSql('DROP TABLE product_variant_managed_file');
        $this->addSql('ALTER TABLE casebook_subject DROP FOREIGN KEY FK_911EC37B51412E32');
        $this->addSql('UPDATE casebook_subject
            LEFT JOIN managed_image ON managed_image.source_image_id = casebook_subject.casebook_subject_image_file_id
            SET casebook_subject.casebook_subject_image_file_id = managed_image.id');
        $this->addSql('ALTER TABLE casebook_subject ADD CONSTRAINT FK_911EC37B51412E32 FOREIGN KEY (casebook_subject_image_file_id) REFERENCES managed_image (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE event DROP FOREIGN KEY FK_3BAE0AA798BB94C5');
        $this->addSql('UPDATE event
            INNER JOIN managed_image ON managed_image.source_image_id = event.hero_image_id
            SET event.hero_image_id = managed_image.id');
        $this->addSql('ALTER TABLE event ADD CONSTRAINT FK_3BAE0AA798BB94C5 FOREIGN KEY (hero_image_id) REFERENCES managed_image (id)');
        $this->addSql('ALTER TABLE game DROP FOREIGN KEY FK_232B318C268A15CE');
        $this->addSql('UPDATE game
            LEFT JOIN managed_image ON managed_image.source_image_id = game.hero_image_file_id
            SET game.hero_image_file_id = managed_image.id');
        $this->addSql('ALTER TABLE game ADD CONSTRAINT FK_232B318C268A15CE FOREIGN KEY (hero_image_file_id) REFERENCES managed_image (id)');
        $this->addSql('ALTER TABLE game_session DROP FOREIGN KEY FK_4586AAFBB6094505');
        $this->addSql('UPDATE game_session
            LEFT JOIN managed_image ON managed_image.source_image_id = game_session.session_image_id
            SET game_session.session_image_id = managed_image.id');
        $this->addSql('ALTER TABLE game_session ADD CONSTRAINT FK_4586AAFBB6094505 FOREIGN KEY (session_image_id) REFERENCES managed_image (id)');
        $this->addSql('ALTER TABLE organisation DROP FOREIGN KEY FK_E6E132B49A0B6349');
        $this->addSql('UPDATE organisation
            INNER JOIN managed_image ON managed_image.source_image_id = organisation.organisation_logo_id
            SET organisation.organisation_logo_id = managed_image.id');
        $this->addSql('ALTER TABLE organisation ADD CONSTRAINT FK_E6E132B49A0B6349 FOREIGN KEY (organisation_logo_id) REFERENCES managed_image (id)');
        $this->addSql('ALTER TABLE puzzle_instance CHANGE config config JSON NOT NULL');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D64986383B10');
        $this->addSql('UPDATE user
            LEFT JOIN managed_image ON managed_image.source_image_id = user.avatar_id
            SET user.avatar_id = managed_image.id');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D64986383B10 FOREIGN KEY (avatar_id) REFERENCES managed_image (id) ON DELETE SET NULL');
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
}
