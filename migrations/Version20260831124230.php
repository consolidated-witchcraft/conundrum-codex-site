<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260831124230 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Remove the Game system and delete Game-owned Casebook and PuzzleInstance data';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('DELETE FROM casebook_subject_clue');
        $this->addSql('DELETE FROM casebook_subject_note');
        $this->addSql('DELETE FROM casebook_subject');
        $this->addSql('DELETE FROM casebook');
        $this->addSql('DELETE FROM puzzle_instance');
        $this->addSql('ALTER TABLE puzzle_instance DROP CONSTRAINT fk_166c5c8fe48fd905');
        $this->addSql('ALTER TABLE casebook DROP CONSTRAINT fk_2042c41fe48fd905');
        $this->addSql('ALTER TABLE game DROP CONSTRAINT fk_232b318c268a15ce');
        $this->addSql('ALTER TABLE game DROP CONSTRAINT fk_232b318c3c75a0f1');
        $this->addSql('ALTER TABLE game_session DROP CONSTRAINT fk_4586aafbb6094505');
        $this->addSql('ALTER TABLE game_session DROP CONSTRAINT fk_4586aafbe48fd905');
        $this->addSql('ALTER TABLE game_invitation DROP CONSTRAINT fk_1fc1a64fa76ed395');
        $this->addSql('ALTER TABLE game_invitation DROP CONSTRAINT fk_1fc1a64fe48fd905');
        $this->addSql('ALTER TABLE game_user DROP CONSTRAINT fk_6686ba65a76ed395');
        $this->addSql('ALTER TABLE game_user DROP CONSTRAINT fk_6686ba65e48fd905');
        $this->addSql('DROP TABLE game');
        $this->addSql('DROP TABLE game_session');
        $this->addSql('DROP TABLE game_invitation');
        $this->addSql('DROP TABLE game_user');
        $this->addSql('DROP INDEX idx_2042c41fe48fd905');
        $this->addSql('ALTER TABLE casebook DROP game_id');
        $this->addSql('DROP INDEX idx_166c5c8fe48fd905');
        $this->addSql('ALTER TABLE puzzle_instance DROP game_id');
        $this->addSql('ALTER TABLE puzzle_instance ALTER instance_code TYPE VARCHAR(255)');
        $this->addSql('ALTER TABLE puzzle_instance ALTER name TYPE VARCHAR(255)');
        $this->addSql('ALTER TABLE puzzle_instance ALTER description TYPE VARCHAR(2048)');
        $this->addSql('ALTER TABLE puzzle_instance ALTER template_slug TYPE VARCHAR(1024)');
        $this->addSql('ALTER TABLE puzzle_instance ALTER config TYPE JSON');
        $this->addSql('ALTER TABLE user_account_type DROP maximum_concurrent_games');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('CREATE TABLE game (id SERIAL NOT NULL, games_master_id INT NOT NULL, hero_image_file_id INT DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, name VARCHAR(255) NOT NULL, slug VARCHAR(255) NOT NULL, description VARCHAR(1024) NOT NULL, archived_date TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX gm_id ON game (games_master_id)');
        $this->addSql('CREATE UNIQUE INDEX uniq_232b318c268a15ce ON game (hero_image_file_id)');
        $this->addSql('CREATE UNIQUE INDEX uniq_232b318c989d9b62 ON game (slug)');
        $this->addSql('COMMENT ON COLUMN game.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN game.updated_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE game_session (id SERIAL NOT NULL, game_id INT NOT NULL, session_image_id INT DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, session_date TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, title VARCHAR(255) NOT NULL, description TEXT DEFAULT NULL, cancellation_date TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX idx_4586aafbe48fd905 ON game_session (game_id)');
        $this->addSql('CREATE UNIQUE INDEX uniq_4586aafbb6094505 ON game_session (session_image_id)');
        $this->addSql('COMMENT ON COLUMN game_session.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN game_session.updated_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE game_invitation (id SERIAL NOT NULL, game_id INT NOT NULL, user_id INT DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, invitation_code VARCHAR(64) NOT NULL, email VARCHAR(255) NOT NULL, invitation_message VARCHAR(255) NOT NULL, expires_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, date_used DATE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX idx_1fc1a64fa76ed395 ON game_invitation (user_id)');
        $this->addSql('CREATE INDEX idx_1fc1a64fe48fd905 ON game_invitation (game_id)');
        $this->addSql('CREATE UNIQUE INDEX uniq_1fc1a64fba14fccc ON game_invitation (invitation_code)');
        $this->addSql('COMMENT ON COLUMN game_invitation.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN game_invitation.updated_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN game_invitation.expires_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN game_invitation.date_used IS \'(DC2Type:date_immutable)\'');
        $this->addSql('CREATE TABLE game_user (game_id INT NOT NULL, user_id INT NOT NULL, PRIMARY KEY(game_id, user_id))');
        $this->addSql('CREATE INDEX idx_6686ba65a76ed395 ON game_user (user_id)');
        $this->addSql('CREATE INDEX idx_6686ba65e48fd905 ON game_user (game_id)');
        $this->addSql('ALTER TABLE game ADD CONSTRAINT fk_232b318c268a15ce FOREIGN KEY (hero_image_file_id) REFERENCES managed_image (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE game ADD CONSTRAINT fk_232b318c3c75a0f1 FOREIGN KEY (games_master_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE game_session ADD CONSTRAINT fk_4586aafbb6094505 FOREIGN KEY (session_image_id) REFERENCES managed_image (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE game_session ADD CONSTRAINT fk_4586aafbe48fd905 FOREIGN KEY (game_id) REFERENCES game (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE game_invitation ADD CONSTRAINT fk_1fc1a64fa76ed395 FOREIGN KEY (user_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE game_invitation ADD CONSTRAINT fk_1fc1a64fe48fd905 FOREIGN KEY (game_id) REFERENCES game (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE game_user ADD CONSTRAINT fk_6686ba65a76ed395 FOREIGN KEY (user_id) REFERENCES "user" (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE game_user ADD CONSTRAINT fk_6686ba65e48fd905 FOREIGN KEY (game_id) REFERENCES game (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE puzzle_instance ADD game_id INT NOT NULL');
        $this->addSql('ALTER TABLE puzzle_instance ALTER instance_code TYPE VARCHAR(255)');
        $this->addSql('ALTER TABLE puzzle_instance ALTER name TYPE VARCHAR(255)');
        $this->addSql('ALTER TABLE puzzle_instance ALTER description TYPE VARCHAR(2048)');
        $this->addSql('ALTER TABLE puzzle_instance ALTER template_slug TYPE VARCHAR(1024)');
        $this->addSql('ALTER TABLE puzzle_instance ALTER config TYPE JSON');
        $this->addSql('ALTER TABLE puzzle_instance ADD CONSTRAINT fk_166c5c8fe48fd905 FOREIGN KEY (game_id) REFERENCES game (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX idx_166c5c8fe48fd905 ON puzzle_instance (game_id)');
        $this->addSql('ALTER TABLE user_account_type ADD maximum_concurrent_games INT DEFAULT 5 NOT NULL');
        $this->addSql('ALTER TABLE casebook ADD game_id INT NOT NULL');
        $this->addSql('ALTER TABLE casebook ADD CONSTRAINT fk_2042c41fe48fd905 FOREIGN KEY (game_id) REFERENCES game (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX idx_2042c41fe48fd905 ON casebook (game_id)');
    }
}
