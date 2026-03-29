/*M!999999\- enable the sandbox mode */ 
-- MariaDB dump 10.19-12.0.2-MariaDB, for debian-linux-gnu (x86_64)
--
-- Host: localhost    Database: dnd-puzzles
-- ------------------------------------------------------
-- Server version	12.0.2-MariaDB-ubu2404-log

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*M!100616 SET @OLD_NOTE_VERBOSITY=@@NOTE_VERBOSITY, NOTE_VERBOSITY=0 */;

--
-- Table structure for table `abuse_report`
--

DROP TABLE IF EXISTS `abuse_report`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `abuse_report` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `reported_user_id` int(11) NOT NULL,
  `reporting_user_id` int(11) DEFAULT NULL,
  `created_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  `updated_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  `reason` varchar(1024) NOT NULL,
  `notes` varchar(1024) DEFAULT NULL,
  `checked_date` datetime DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)',
  `confirmed_date` datetime DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)',
  PRIMARY KEY (`id`),
  KEY `IDX_21AF999BE7566E` (`reported_user_id`),
  KEY `IDX_21AF999B713FF03D` (`reporting_user_id`),
  CONSTRAINT `FK_21AF999B713FF03D` FOREIGN KEY (`reporting_user_id`) REFERENCES `user` (`id`),
  CONSTRAINT `FK_21AF999BE7566E` FOREIGN KEY (`reported_user_id`) REFERENCES `user` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `blocked_email_address`
--

DROP TABLE IF EXISTS `blocked_email_address`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `blocked_email_address` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `created_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  `updated_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  `email_address` varchar(255) NOT NULL,
  `block_reason` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `bug_report`
--

DROP TABLE IF EXISTS `bug_report`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `bug_report` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `created_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  `updated_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  `summary` varchar(255) NOT NULL,
  `reporter_name` varchar(255) NOT NULL,
  `reporter_email` varchar(255) NOT NULL,
  `text` longtext NOT NULL,
  `referring_url` varchar(255) DEFAULT NULL,
  `actioned_at` datetime DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)',
  `closed_at` datetime DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `casebook`
--

DROP TABLE IF EXISTS `casebook`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `casebook` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `game_id` int(11) NOT NULL,
  `created_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  `updated_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `brief` varchar(2048) NOT NULL,
  `publication_date` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_2042C41F989D9B62` (`slug`),
  KEY `IDX_2042C41FE48FD905` (`game_id`),
  CONSTRAINT `FK_2042C41FE48FD905` FOREIGN KEY (`game_id`) REFERENCES `game` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `casebook_subject`
--

DROP TABLE IF EXISTS `casebook_subject`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `casebook_subject` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `casebook_id` int(11) NOT NULL,
  `casebook_subject_type_id` int(11) NOT NULL,
  `created_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  `updated_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  `name` varchar(255) NOT NULL,
  `description` varchar(2048) NOT NULL,
  `revealed_date` datetime DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)',
  `casebook_subject_image_file_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_911EC37B51412E32` (`casebook_subject_image_file_id`),
  KEY `IDX_911EC37B467F13AB` (`casebook_id`),
  KEY `IDX_911EC37B68710875` (`casebook_subject_type_id`),
  CONSTRAINT `FK_911EC37B467F13AB` FOREIGN KEY (`casebook_id`) REFERENCES `casebook` (`id`),
  CONSTRAINT `FK_911EC37B51412E32` FOREIGN KEY (`casebook_subject_image_file_id`) REFERENCES `managed_image` (`id`) ON DELETE SET NULL,
  CONSTRAINT `FK_911EC37B68710875` FOREIGN KEY (`casebook_subject_type_id`) REFERENCES `casebook_subject_type` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `casebook_subject_clue`
--

DROP TABLE IF EXISTS `casebook_subject_clue`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `casebook_subject_clue` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type_id` int(11) NOT NULL,
  `casebook_subject_id` int(11) NOT NULL,
  `created_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  `updated_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  `title` varchar(255) NOT NULL,
  `body` varchar(1024) NOT NULL,
  `revealed_date` datetime DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)',
  PRIMARY KEY (`id`),
  KEY `IDX_EFB778DDC54C8C93` (`type_id`),
  KEY `IDX_EFB778DD243DFBFE` (`casebook_subject_id`),
  CONSTRAINT `FK_EFB778DD243DFBFE` FOREIGN KEY (`casebook_subject_id`) REFERENCES `casebook_subject` (`id`),
  CONSTRAINT `FK_EFB778DDC54C8C93` FOREIGN KEY (`type_id`) REFERENCES `casebook_subject_clue_type` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=72 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `casebook_subject_clue_type`
--

DROP TABLE IF EXISTS `casebook_subject_clue_type`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `casebook_subject_clue_type` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `created_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  `updated_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  `label` varchar(255) NOT NULL,
  `handle` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_17E2131F918020D9` (`handle`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `casebook_subject_note`
--

DROP TABLE IF EXISTS `casebook_subject_note`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `casebook_subject_note` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `created_by_id` int(11) NOT NULL,
  `casebook_subject_id` int(11) NOT NULL,
  `created_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  `updated_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  `title` varchar(255) NOT NULL,
  `body` varchar(1024) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_6802F18B03A8386` (`created_by_id`),
  KEY `IDX_6802F18243DFBFE` (`casebook_subject_id`),
  CONSTRAINT `FK_6802F18243DFBFE` FOREIGN KEY (`casebook_subject_id`) REFERENCES `casebook_subject` (`id`),
  CONSTRAINT `FK_6802F18B03A8386` FOREIGN KEY (`created_by_id`) REFERENCES `user` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `casebook_subject_type`
--

DROP TABLE IF EXISTS `casebook_subject_type`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `casebook_subject_type` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `created_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  `updated_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  `label` varchar(255) NOT NULL,
  `handle` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_45E38225918020D9` (`handle`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `doctrine_migration_versions`
--

DROP TABLE IF EXISTS `doctrine_migration_versions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `doctrine_migration_versions` (
  `version` varchar(191) NOT NULL,
  `executed_at` datetime DEFAULT NULL,
  `execution_time` int(11) DEFAULT NULL,
  PRIMARY KEY (`version`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `event`
--

DROP TABLE IF EXISTS `event`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `event` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `hero_image_id` int(11) DEFAULT NULL,
  `location_id` int(11) DEFAULT NULL,
  `organisation_id` int(11) DEFAULT NULL,
  `created_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  `updated_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  `title` varchar(255) NOT NULL,
  `event_slug` varchar(255) NOT NULL,
  `description` varchar(2048) NOT NULL,
  `prebook_only` tinyint(1) NOT NULL,
  `start_date` datetime NOT NULL,
  `end_date` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_3BAE0AA78756D7C9` (`event_slug`),
  UNIQUE KEY `UNIQ_3BAE0AA798BB94C5` (`hero_image_id`),
  UNIQUE KEY `UNIQ_3BAE0AA764D218E` (`location_id`),
  KEY `IDX_3BAE0AA79E6B1585` (`organisation_id`),
  CONSTRAINT `FK_3BAE0AA764D218E` FOREIGN KEY (`location_id`) REFERENCES `postal_address` (`id`),
  CONSTRAINT `FK_3BAE0AA798BB94C5` FOREIGN KEY (`hero_image_id`) REFERENCES `managed_image` (`id`),
  CONSTRAINT `FK_3BAE0AA79E6B1585` FOREIGN KEY (`organisation_id`) REFERENCES `organisation` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `feature_switcher`
--

DROP TABLE IF EXISTS `feature_switcher`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `feature_switcher` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `created_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  `updated_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  `handle` varchar(255) NOT NULL,
  `label` varchar(255) NOT NULL,
  `enabled` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_FDA05D9A918020D9` (`handle`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `game`
--

DROP TABLE IF EXISTS `game`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `game` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `games_master_id` int(11) NOT NULL,
  `created_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  `updated_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `description` varchar(1024) NOT NULL,
  `archived_date` datetime DEFAULT NULL,
  `hero_image_file_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_232B318C989D9B62` (`slug`),
  UNIQUE KEY `UNIQ_232B318C268A15CE` (`hero_image_file_id`),
  KEY `gm_id` (`games_master_id`),
  CONSTRAINT `FK_232B318C268A15CE` FOREIGN KEY (`hero_image_file_id`) REFERENCES `managed_image` (`id`),
  CONSTRAINT `FK_232B318C3C75A0F1` FOREIGN KEY (`games_master_id`) REFERENCES `user` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `game_invitation`
--

DROP TABLE IF EXISTS `game_invitation`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `game_invitation` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `game_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `created_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  `updated_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  `invitation_code` varchar(64) NOT NULL,
  `email` varchar(255) NOT NULL,
  `invitation_message` varchar(255) NOT NULL,
  `expires_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  `date_used` date DEFAULT NULL COMMENT '(DC2Type:date_immutable)',
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_1FC1A64FBA14FCCC` (`invitation_code`),
  KEY `IDX_1FC1A64FE48FD905` (`game_id`),
  KEY `IDX_1FC1A64FA76ED395` (`user_id`),
  CONSTRAINT `FK_1FC1A64FA76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`),
  CONSTRAINT `FK_1FC1A64FE48FD905` FOREIGN KEY (`game_id`) REFERENCES `game` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `game_session`
--

DROP TABLE IF EXISTS `game_session`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `game_session` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `game_id` int(11) NOT NULL,
  `session_image_id` int(11) DEFAULT NULL,
  `created_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  `updated_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  `session_date` datetime NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` longtext DEFAULT NULL,
  `cancellation_date` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_4586AAFBB6094505` (`session_image_id`),
  KEY `IDX_4586AAFBE48FD905` (`game_id`),
  CONSTRAINT `FK_4586AAFBB6094505` FOREIGN KEY (`session_image_id`) REFERENCES `managed_image` (`id`),
  CONSTRAINT `FK_4586AAFBE48FD905` FOREIGN KEY (`game_id`) REFERENCES `game` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `game_user`
--

DROP TABLE IF EXISTS `game_user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `game_user` (
  `game_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  PRIMARY KEY (`game_id`,`user_id`),
  KEY `IDX_6686BA65E48FD905` (`game_id`),
  KEY `IDX_6686BA65A76ED395` (`user_id`),
  CONSTRAINT `FK_6686BA65A76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE,
  CONSTRAINT `FK_6686BA65E48FD905` FOREIGN KEY (`game_id`) REFERENCES `game` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `image_type_format_map`
--

DROP TABLE IF EXISTS `image_type_format_map`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `image_type_format_map` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `created_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  `updated_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  `managed_image_type` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_3836D7E5C28E566C` (`managed_image_type`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `image_type_format_map_derivative_format`
--

DROP TABLE IF EXISTS `image_type_format_map_derivative_format`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `image_type_format_map_derivative_format` (
  `image_type_format_map_id` int(11) NOT NULL,
  `managed_image_derivative_format_id` int(11) NOT NULL,
  PRIMARY KEY (`image_type_format_map_id`,`managed_image_derivative_format_id`),
  KEY `IDX_7DA2EA43C8AB1102` (`image_type_format_map_id`),
  KEY `IDX_7DA2EA43F73594CB` (`managed_image_derivative_format_id`),
  CONSTRAINT `FK_236D179D3A6EB7D5` FOREIGN KEY (`managed_image_derivative_format_id`) REFERENCES `managed_image_derivative_format` (`id`) ON DELETE CASCADE,
  CONSTRAINT `FK_236D179DC1E3F3A7` FOREIGN KEY (`image_type_format_map_id`) REFERENCES `image_type_format_map` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `managed_file`
--

DROP TABLE IF EXISTS `managed_file`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `managed_file` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uploader_id` int(11) NOT NULL,
  `created_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  `updated_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  `path` varchar(1024) NOT NULL,
  `mime_type` varchar(128) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` varchar(512) NOT NULL,
  `file_hash` varchar(64) DEFAULT NULL,
  `is_orphaned` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `IDX_5F1975EF16678C77` (`uploader_id`),
  CONSTRAINT `FK_5F1975EF16678C77` FOREIGN KEY (`uploader_id`) REFERENCES `user` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=146 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `managed_image`
--

DROP TABLE IF EXISTS `managed_image`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `managed_image` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `created_by_id` int(11) NOT NULL,
  `source_image_id` int(11) NOT NULL,
  `created_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  `updated_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  `description` varchar(255) NOT NULL,
  `managed_image_type` varchar(255) NOT NULL DEFAULT 'default',
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_E8EC6D917E8513BF` (`source_image_id`),
  KEY `IDX_E8EC6D91B03A8386` (`created_by_id`),
  CONSTRAINT `FK_E8EC6D917E8513BF` FOREIGN KEY (`source_image_id`) REFERENCES `managed_file` (`id`),
  CONSTRAINT `FK_E8EC6D91B03A8386` FOREIGN KEY (`created_by_id`) REFERENCES `user` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=148 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `managed_image_derivative`
--

DROP TABLE IF EXISTS `managed_image_derivative`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `managed_image_derivative` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `parent_image_id` int(11) NOT NULL,
  `format_id` int(11) NOT NULL,
  `derivative_id` int(11) NOT NULL,
  `created_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  `updated_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  PRIMARY KEY (`id`),
  KEY `IDX_901AB6A9A26889BC` (`parent_image_id`),
  KEY `IDX_901AB6A9D629F605` (`format_id`),
  KEY `IDX_901AB6A9B13D9776` (`derivative_id`),
  CONSTRAINT `FK_901AB6A9A26889BC` FOREIGN KEY (`parent_image_id`) REFERENCES `managed_image` (`id`),
  CONSTRAINT `FK_901AB6A9B13D9776` FOREIGN KEY (`derivative_id`) REFERENCES `managed_file` (`id`),
  CONSTRAINT `FK_901AB6A9D629F605` FOREIGN KEY (`format_id`) REFERENCES `managed_image_derivative_format` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `managed_image_derivative_format`
--

DROP TABLE IF EXISTS `managed_image_derivative_format`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `managed_image_derivative_format` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `created_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  `updated_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  `handle` varchar(255) NOT NULL,
  `resolution_x` int(11) NOT NULL,
  `resolution_y` int(11) DEFAULT NULL,
  `quality` int(11) NOT NULL,
  `allow_crop` tinyint(1) NOT NULL DEFAULT 0,
  `centre_image` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_AE399D0E918020D9` (`handle`)
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `order_charge_line`
--

DROP TABLE IF EXISTS `order_charge_line`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `order_charge_line` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) DEFAULT NULL,
  `created_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  `updated_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  `charge_type` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `price_centessimal` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_8265A6AF8D9F6D38` (`order_id`),
  CONSTRAINT `FK_8265A6AF8D9F6D38` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `order_product_line`
--

DROP TABLE IF EXISTS `order_product_line`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `order_product_line` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) DEFAULT NULL,
  `product_variant_id` int(11) DEFAULT NULL,
  `created_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  `updated_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  `quantity` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_583C023D8D9F6D38` (`order_id`),
  KEY `IDX_583C023DA80EF684` (`product_variant_id`),
  CONSTRAINT `FK_583C023D8D9F6D38` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`),
  CONSTRAINT `FK_583C023DA80EF684` FOREIGN KEY (`product_variant_id`) REFERENCES `product_variant` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `order_tax`
--

DROP TABLE IF EXISTS `order_tax`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `order_tax` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) DEFAULT NULL,
  `created_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  `updated_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  `tax_rate_public_reference` varchar(255) NOT NULL,
  `tax_rate_name` varchar(255) NOT NULL,
  `tax_rate_handle` varchar(255) NOT NULL,
  `amount_centessimal` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_CDDAF5168D9F6D38` (`order_id`),
  CONSTRAINT `FK_CDDAF5168D9F6D38` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `orders`
--

DROP TABLE IF EXISTS `orders`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `orders` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `created_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  `updated_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  `order_reference` varchar(255) NOT NULL,
  `delivery_address_id` int(11) DEFAULT NULL,
  `billing_address_id` int(11) DEFAULT NULL,
  `order_status` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_E52FFDEE122432EB` (`order_reference`),
  KEY `IDX_E52FFDEEA76ED395` (`user_id`),
  KEY `IDX_E52FFDEEEBF23851` (`delivery_address_id`),
  KEY `IDX_E52FFDEE79D0C0E4` (`billing_address_id`),
  CONSTRAINT `FK_E52FFDEE79D0C0E4` FOREIGN KEY (`billing_address_id`) REFERENCES `postal_address` (`id`),
  CONSTRAINT `FK_E52FFDEEA76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`),
  CONSTRAINT `FK_E52FFDEEEBF23851` FOREIGN KEY (`delivery_address_id`) REFERENCES `postal_address` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `organisation`
--

DROP TABLE IF EXISTS `organisation`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `organisation` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `organisation_logo_id` int(11) NOT NULL,
  `owner_id` int(11) DEFAULT NULL,
  `created_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  `updated_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  `name` varchar(255) NOT NULL,
  `handle` varchar(255) NOT NULL,
  `deactivation_date` datetime DEFAULT NULL,
  `postal_address_id` int(11) DEFAULT NULL,
  `invoice_address_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_E6E132B4918020D9` (`handle`),
  UNIQUE KEY `UNIQ_E6E132B49A0B6349` (`organisation_logo_id`),
  UNIQUE KEY `UNIQ_E6E132B4FD54954B` (`postal_address_id`),
  UNIQUE KEY `UNIQ_E6E132B4C6BDFEB` (`invoice_address_id`),
  KEY `IDX_E6E132B47E3C61F9` (`owner_id`),
  CONSTRAINT `FK_E6E132B47E3C61F9` FOREIGN KEY (`owner_id`) REFERENCES `user` (`id`),
  CONSTRAINT `FK_E6E132B49A0B6349` FOREIGN KEY (`organisation_logo_id`) REFERENCES `managed_image` (`id`),
  CONSTRAINT `FK_E6E132B4C6BDFEB` FOREIGN KEY (`invoice_address_id`) REFERENCES `postal_address` (`id`),
  CONSTRAINT `FK_E6E132B4FD54954B` FOREIGN KEY (`postal_address_id`) REFERENCES `postal_address` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `payment`
--

DROP TABLE IF EXISTS `payment`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `payment` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) DEFAULT NULL,
  `created_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  `updated_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  `name` varchar(255) NOT NULL,
  `payment_amount_centessimal` int(11) NOT NULL,
  `payment_reference` varchar(255) NOT NULL,
  `payment_status` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_6D28840DED8A663B` (`payment_reference`),
  KEY `IDX_6D28840D8D9F6D38` (`order_id`),
  CONSTRAINT `FK_6D28840D8D9F6D38` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `payment_receipt`
--

DROP TABLE IF EXISTS `payment_receipt`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `payment_receipt` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `payment_id` int(11) DEFAULT NULL,
  `created_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  `updated_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  `receipt_reference` varchar(255) NOT NULL,
  `amount_centessimal` int(11) NOT NULL,
  `receipt_date` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_4AD0B8AF8220C10D` (`receipt_reference`),
  KEY `IDX_4AD0B8AF4C3A3BB` (`payment_id`),
  CONSTRAINT `FK_4AD0B8AF4C3A3BB` FOREIGN KEY (`payment_id`) REFERENCES `payment` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `permission`
--

DROP TABLE IF EXISTS `permission`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `permission` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `label` varchar(255) NOT NULL,
  `handle` varchar(255) NOT NULL,
  `description` varchar(255) NOT NULL,
  `created_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  `updated_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_E04992AA918020D9` (`handle`)
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `play_event`
--

DROP TABLE IF EXISTS `play_event`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `play_event` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `created_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  `updated_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  `type` varchar(255) NOT NULL,
  `puzzle_instance_code` varchar(255) NOT NULL,
  `puzzle_template_slug` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_CA71ED1BA76ED395` (`user_id`),
  CONSTRAINT `FK_CA71ED1BA76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `postal_address`
--

DROP TABLE IF EXISTS `postal_address`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `postal_address` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `created_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  `updated_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  `label` varchar(255) NOT NULL,
  `address_line_one` varchar(255) NOT NULL,
  `town_city` varchar(255) NOT NULL,
  `postal_zip` varchar(255) NOT NULL,
  `country_code` varchar(255) NOT NULL,
  `county_province` varchar(255) NOT NULL,
  `address_line_two` varchar(255) DEFAULT NULL,
  `latitude` double DEFAULT NULL,
  `longitude` double DEFAULT NULL,
  `is_default_billing_address` tinyint(1) NOT NULL,
  `is_default_shipping_address` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_972EFBF7A76ED395` (`user_id`),
  CONSTRAINT `FK_972EFBF7A76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `product`
--

DROP TABLE IF EXISTS `product`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `product` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `organisation_id` int(11) DEFAULT NULL,
  `created_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  `updated_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  `base_sku` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` varchar(2198) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_D34A04AD65F6468E` (`base_sku`),
  KEY `IDX_D34A04AD9E6B1585` (`organisation_id`),
  CONSTRAINT `FK_D34A04AD9E6B1585` FOREIGN KEY (`organisation_id`) REFERENCES `organisation` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `product_variant`
--

DROP TABLE IF EXISTS `product_variant`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `product_variant` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `created_by_id` int(11) DEFAULT NULL,
  `product_variant_stock_id` int(11) DEFAULT NULL,
  `product_id` int(11) NOT NULL,
  `created_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  `updated_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  `sku` varchar(255) NOT NULL,
  `product_variant_name` varchar(255) NOT NULL,
  `type` varchar(255) NOT NULL DEFAULT 'e_ticket',
  `delta` int(11) NOT NULL,
  `pre_tax_price_centessimal` int(11) NOT NULL,
  `tax_exempt` tinyint(1) NOT NULL,
  `requires_postage` tinyint(1) NOT NULL,
  `publication_date` datetime DEFAULT NULL,
  `retired_date` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_209AA41DF9038C4` (`sku`),
  KEY `IDX_209AA41DB03A8386` (`created_by_id`),
  KEY `IDX_209AA41D1C914464` (`product_variant_stock_id`),
  KEY `IDX_209AA41D4584665A` (`product_id`),
  CONSTRAINT `FK_209AA41D1C914464` FOREIGN KEY (`product_variant_stock_id`) REFERENCES `product_variant_stock` (`id`),
  CONSTRAINT `FK_209AA41D4584665A` FOREIGN KEY (`product_id`) REFERENCES `product` (`id`),
  CONSTRAINT `FK_209AA41DB03A8386` FOREIGN KEY (`created_by_id`) REFERENCES `user` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `product_variant_managed_image`
--

DROP TABLE IF EXISTS `product_variant_managed_image`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `product_variant_managed_image` (
  `product_variant_id` int(11) NOT NULL,
  `managed_image_id` int(11) NOT NULL,
  PRIMARY KEY (`product_variant_id`,`managed_image_id`),
  KEY `IDX_452D8056A80EF684` (`product_variant_id`),
  KEY `IDX_452D8056E37FACA6` (`managed_image_id`),
  CONSTRAINT `FK_452D8056A80EF684` FOREIGN KEY (`product_variant_id`) REFERENCES `product_variant` (`id`) ON DELETE CASCADE,
  CONSTRAINT `FK_452D8056E37FACA6` FOREIGN KEY (`managed_image_id`) REFERENCES `managed_image` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `product_variant_stock`
--

DROP TABLE IF EXISTS `product_variant_stock`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `product_variant_stock` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `created_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  `updated_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  `stock_level` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `puzzle_category`
--

DROP TABLE IF EXISTS `puzzle_category`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `puzzle_category` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `created_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  `updated_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  `label` varchar(255) NOT NULL,
  `handle` varchar(255) NOT NULL,
  `description` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_5210F490918020D9` (`handle`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `puzzle_instance`
--

DROP TABLE IF EXISTS `puzzle_instance`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `puzzle_instance` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `game_id` int(11) NOT NULL,
  `created_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  `updated_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  `instance_code` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` varchar(2048) NOT NULL,
  `template_slug` varchar(1024) NOT NULL,
  `publication_date` datetime DEFAULT NULL,
  `config` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`config`)),
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_166C5C8F5F1F6053` (`instance_code`),
  KEY `IDX_166C5C8FE48FD905` (`game_id`),
  CONSTRAINT `FK_166C5C8FE48FD905` FOREIGN KEY (`game_id`) REFERENCES `game` (`id`),
  CONSTRAINT `puzzle_instance_chk_1` CHECK (json_valid(`config`))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `quotation`
--

DROP TABLE IF EXISTS `quotation`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `quotation` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `created_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  `updated_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  `quotation` varchar(1024) NOT NULL,
  `citation` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `reset_password_request`
--

DROP TABLE IF EXISTS `reset_password_request`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `reset_password_request` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `selector` varchar(20) NOT NULL,
  `hashed_token` varchar(100) NOT NULL,
  `requested_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  `expires_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  `user_id` int(11) NOT NULL,
  `created_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  `updated_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  PRIMARY KEY (`id`),
  KEY `IDX_7CE748AA76ED395` (`user_id`),
  CONSTRAINT `FK_7CE748AA76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `role`
--

DROP TABLE IF EXISTS `role`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `role` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `created_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  `updated_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  `label` varchar(255) NOT NULL,
  `handle` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_57698A6A918020D9` (`handle`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `role_permission`
--

DROP TABLE IF EXISTS `role_permission`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `role_permission` (
  `role_id` int(11) NOT NULL,
  `permission_id` int(11) NOT NULL,
  PRIMARY KEY (`role_id`,`permission_id`),
  KEY `IDX_6F7DF886D60322AC` (`role_id`),
  KEY `IDX_6F7DF886FED90CCA` (`permission_id`),
  CONSTRAINT `FK_6F7DF886D60322AC` FOREIGN KEY (`role_id`) REFERENCES `role` (`id`) ON DELETE CASCADE,
  CONSTRAINT `FK_6F7DF886FED90CCA` FOREIGN KEY (`permission_id`) REFERENCES `permission` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ticket`
--

DROP TABLE IF EXISTS `ticket`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `ticket` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ticket_type_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `created_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  `updated_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  `customer_email` varchar(255) NOT NULL,
  `customer_name` varchar(255) NOT NULL,
  `ticket_code` varchar(255) NOT NULL,
  `issued_date` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  `paid_date` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_97A0ADA345CE25A0` (`ticket_code`),
  KEY `IDX_97A0ADA3C980D5C1` (`ticket_type_id`),
  KEY `IDX_97A0ADA3A76ED395` (`user_id`),
  CONSTRAINT `FK_97A0ADA3A76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`),
  CONSTRAINT `FK_97A0ADA3C980D5C1` FOREIGN KEY (`ticket_type_id`) REFERENCES `ticket_type` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ticket_type`
--

DROP TABLE IF EXISTS `ticket_type`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `ticket_type` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `event_id` int(11) DEFAULT NULL,
  `created_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  `updated_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `description` varchar(255) NOT NULL,
  `price_centessimal` int(11) NOT NULL,
  `refundable_until` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_BE05421171F7E88B` (`event_id`),
  CONSTRAINT `FK_BE05421171F7E88B` FOREIGN KEY (`event_id`) REFERENCES `event` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ticket_type_product_variant_map`
--

DROP TABLE IF EXISTS `ticket_type_product_variant_map`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `ticket_type_product_variant_map` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ticket_type_id` int(11) DEFAULT NULL,
  `product_variant_id` int(11) DEFAULT NULL,
  `created_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  `updated_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_F4C521B3A80EF684` (`product_variant_id`),
  KEY `IDX_F4C521B3C980D5C1` (`ticket_type_id`),
  CONSTRAINT `FK_F4C521B3A80EF684` FOREIGN KEY (`product_variant_id`) REFERENCES `product_variant` (`id`),
  CONSTRAINT `FK_F4C521B3C980D5C1` FOREIGN KEY (`ticket_type_id`) REFERENCES `ticket_type` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `user`
--

DROP TABLE IF EXISTS `user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `created_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  `updated_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  `email` varchar(255) NOT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `user_account_type_id` int(11) NOT NULL,
  `avatar_id` int(11) DEFAULT NULL,
  `organisation_id` int(11) DEFAULT NULL,
  `preferences_has_accepted_cookies` tinyint(1) NOT NULL,
  `preferences_profile_public` tinyint(1) NOT NULL,
  `preferences_preferred_font_stack` varchar(255) NOT NULL DEFAULT 'regular',
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_8D93D649E7927C74` (`email`),
  UNIQUE KEY `UNIQ_8D93D64986383B10` (`avatar_id`),
  KEY `username_idx` (`username`),
  KEY `IDX_8D93D6491EBBF454` (`user_account_type_id`),
  KEY `IDX_8D93D6499E6B1585` (`organisation_id`),
  CONSTRAINT `FK_8D93D6491EBBF454` FOREIGN KEY (`user_account_type_id`) REFERENCES `user_account_type` (`id`),
  CONSTRAINT `FK_8D93D64986383B10` FOREIGN KEY (`avatar_id`) REFERENCES `managed_image` (`id`) ON DELETE SET NULL,
  CONSTRAINT `FK_8D93D6499E6B1585` FOREIGN KEY (`organisation_id`) REFERENCES `organisation` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `user_access_token`
--

DROP TABLE IF EXISTS `user_access_token`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_access_token` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_identifier` varchar(1024) NOT NULL,
  `token` varchar(128) NOT NULL,
  `expires_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  `created_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  `updated_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_366EA16A5F37A13B` (`token`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `user_account_type`
--

DROP TABLE IF EXISTS `user_account_type`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_account_type` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `created_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  `updated_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  `label` varchar(255) NOT NULL,
  `handle` varchar(255) NOT NULL,
  `description` varchar(255) NOT NULL,
  `maximum_concurrent_games` int(10) unsigned NOT NULL DEFAULT 5,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_8081AC81918020D9` (`handle`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `user_block`
--

DROP TABLE IF EXISTS `user_block`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_block` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `created_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  `updated_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  `reason` varchar(255) NOT NULL,
  `expiration_date` datetime DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)',
  PRIMARY KEY (`id`),
  KEY `user_idx` (`user_id`),
  KEY `expr_idx` (`expiration_date`),
  CONSTRAINT `FK_61D96C7AA76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `user_cart`
--

DROP TABLE IF EXISTS `user_cart`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_cart` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `created_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  `updated_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_7122C47EA76ED395` (`user_id`),
  CONSTRAINT `FK_7122C47EA76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `user_cart_charge`
--

DROP TABLE IF EXISTS `user_cart_charge`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_cart_charge` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_cart_id` int(11) DEFAULT NULL,
  `created_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  `updated_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  `charge_type` varchar(255) NOT NULL,
  `pre_tax_amount_centessimal` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_F2F40FFE42D8D3B5` (`user_cart_id`),
  CONSTRAINT `FK_F2F40FFE42D8D3B5` FOREIGN KEY (`user_cart_id`) REFERENCES `user_cart` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `user_cart_product_line`
--

DROP TABLE IF EXISTS `user_cart_product_line`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_cart_product_line` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_cart_id` int(11) DEFAULT NULL,
  `product_variant_id` int(11) DEFAULT NULL,
  `created_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  `updated_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  `quantity` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_D26CBF1542D8D3B5` (`user_cart_id`),
  KEY `IDX_D26CBF15A80EF684` (`product_variant_id`),
  CONSTRAINT `FK_D26CBF1542D8D3B5` FOREIGN KEY (`user_cart_id`) REFERENCES `user_cart` (`id`),
  CONSTRAINT `FK_D26CBF15A80EF684` FOREIGN KEY (`product_variant_id`) REFERENCES `product_variant` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `user_feat`
--

DROP TABLE IF EXISTS `user_feat`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_feat` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `created_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  `updated_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  `label` varchar(255) NOT NULL,
  `handle` varchar(255) NOT NULL,
  `description` varchar(512) NOT NULL,
  `games_master_awardable` tinyint(1) NOT NULL,
  `icon_class` varchar(128) NOT NULL,
  `rarity` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_201ADD02918020D9` (`handle`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `user_role`
--

DROP TABLE IF EXISTS `user_role`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_role` (
  `user_id` int(11) NOT NULL,
  `role_id` int(11) NOT NULL,
  PRIMARY KEY (`user_id`,`role_id`),
  KEY `IDX_2DE8C6A3A76ED395` (`user_id`),
  KEY `IDX_2DE8C6A3D60322AC` (`role_id`),
  CONSTRAINT `FK_2DE8C6A3A76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE,
  CONSTRAINT `FK_2DE8C6A3D60322AC` FOREIGN KEY (`role_id`) REFERENCES `role` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `user_user_feat`
--

DROP TABLE IF EXISTS `user_user_feat`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_user_feat` (
  `user_id` int(11) NOT NULL,
  `user_feat_id` int(11) NOT NULL,
  PRIMARY KEY (`user_id`,`user_feat_id`),
  KEY `IDX_20FA50E6A76ED395` (`user_id`),
  KEY `IDX_20FA50E6AC315356` (`user_feat_id`),
  CONSTRAINT `FK_20FA50E6A76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE,
  CONSTRAINT `FK_20FA50E6AC315356` FOREIGN KEY (`user_feat_id`) REFERENCES `user_feat` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `user_world`
--

DROP TABLE IF EXISTS `user_world`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_world` (
  `user_id` int(11) NOT NULL,
  `world_id` int(11) NOT NULL,
  PRIMARY KEY (`user_id`,`world_id`),
  KEY `IDX_D8B5EA1BA76ED395` (`user_id`),
  KEY `IDX_D8B5EA1B8925311C` (`world_id`),
  CONSTRAINT `FK_D8B5EA1B8925311C` FOREIGN KEY (`world_id`) REFERENCES `world` (`id`) ON DELETE CASCADE,
  CONSTRAINT `FK_D8B5EA1BA76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `world`
--

DROP TABLE IF EXISTS `world`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `world` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `creator_id` int(11) DEFAULT NULL,
  `created_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  `updated_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  `name` varchar(255) NOT NULL,
  `description` varchar(2198) NOT NULL,
  `slug` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_3A771143989D9B62` (`slug`),
  KEY `IDX_3A77114361220EA6` (`creator_id`),
  KEY `name_idx` (`name`),
  CONSTRAINT `FK_3A77114361220EA6` FOREIGN KEY (`creator_id`) REFERENCES `user` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `world_calendar_day_of_week`
--

DROP TABLE IF EXISTS `world_calendar_day_of_week`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `world_calendar_day_of_week` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `world_calendar_settings_id` int(11) NOT NULL,
  `created_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  `updated_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  `name` varchar(255) NOT NULL,
  `abbreviation` varchar(4) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `world_calendar_day_of_week_settings_name_unique` (`world_calendar_settings_id`,`name`),
  UNIQUE KEY `world_calendar_day_of_week_settings_abbreviation_unique` (`world_calendar_settings_id`,`abbreviation`),
  KEY `IDX_9254F5A4CD67FC5D` (`world_calendar_settings_id`),
  CONSTRAINT `FK_9254F5A4CD67FC5D` FOREIGN KEY (`world_calendar_settings_id`) REFERENCES `world_calendar_settings` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `world_calendar_month`
--

DROP TABLE IF EXISTS `world_calendar_month`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `world_calendar_month` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `world_calendar_settings_id` int(11) NOT NULL,
  `created_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  `updated_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  `name` varchar(255) NOT NULL,
  `delta` int(11) NOT NULL,
  `number_of_days` int(11) NOT NULL,
  `abbreviation` varchar(3) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `world_calendar_month_settings_delta_unique` (`world_calendar_settings_id`,`delta`),
  UNIQUE KEY `world_calendar_month_abbreviation_unique` (`abbreviation`),
  KEY `IDX_27392640CD67FC5D` (`world_calendar_settings_id`),
  CONSTRAINT `FK_27392640CD67FC5D` FOREIGN KEY (`world_calendar_settings_id`) REFERENCES `world_calendar_settings` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `world_calendar_settings`
--

DROP TABLE IF EXISTS `world_calendar_settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `world_calendar_settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `world_id` int(11) NOT NULL,
  `created_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  `updated_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  `hours_in_day` int(11) NOT NULL DEFAULT 24,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_65495EDA8925311C` (`world_id`),
  CONSTRAINT `FK_65495EDA8925311C` FOREIGN KEY (`world_id`) REFERENCES `world` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `world_edge`
--

DROP TABLE IF EXISTS `world_edge`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `world_edge` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `from_entity_id` int(11) NOT NULL,
  `to_entity_id` int(11) NOT NULL,
  `source_revision_id` int(11) NOT NULL,
  `created_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  `updated_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  `relationship_type` varchar(100) NOT NULL,
  `revealed_at` datetime DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)',
  `valid_from` datetime DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)',
  `valid_to` datetime DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)',
  `description` longtext DEFAULT NULL,
  `is_inferred` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `idx_world_edge_from` (`from_entity_id`),
  KEY `idx_world_edge_to` (`to_entity_id`),
  KEY `idx_world_edge_type` (`relationship_type`),
  KEY `idx_world_edge_source_revision` (`source_revision_id`),
  CONSTRAINT `FK_ABC211B221852C2F` FOREIGN KEY (`source_revision_id`) REFERENCES `world_entity_revision` (`id`) ON DELETE CASCADE,
  CONSTRAINT `FK_ABC211B291B9E8E7` FOREIGN KEY (`from_entity_id`) REFERENCES `world_entity` (`id`) ON DELETE CASCADE,
  CONSTRAINT `FK_ABC211B2B086AA2C` FOREIGN KEY (`to_entity_id`) REFERENCES `world_entity` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `world_entity`
--

DROP TABLE IF EXISTS `world_entity`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `world_entity` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `current_revision_id` int(11) DEFAULT NULL,
  `created_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  `updated_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  `slug` varchar(255) NOT NULL,
  `current_name` varchar(255) NOT NULL,
  `revealed_at` datetime DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)',
  `status` varchar(32) NOT NULL DEFAULT 'canonical',
  `entity_type` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_9E16392B989D9B62` (`slug`),
  KEY `IDX_9E16392BA32ED756` (`current_revision_id`),
  CONSTRAINT `FK_9E16392BA32ED756` FOREIGN KEY (`current_revision_id`) REFERENCES `world_entity_revision` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `world_entity_revision`
--

DROP TABLE IF EXISTS `world_entity_revision`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `world_entity_revision` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `entity_id` int(11) NOT NULL,
  `created_by_id` int(11) DEFAULT NULL,
  `created_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  `updated_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  `revision_number` int(11) NOT NULL,
  `source_markdown` longtext NOT NULL,
  `source_hash` varchar(64) NOT NULL,
  `parser_version` varchar(32) NOT NULL,
  `ast_json` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT '(DC2Type:json)' CHECK (json_valid(`ast_json`)),
  `ast_source_hash` varchar(64) DEFAULT NULL,
  `parse_errors_json` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT '(DC2Type:json)' CHECK (json_valid(`parse_errors_json`)),
  `parse_status` varchar(32) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_entity_revision_number` (`entity_id`,`revision_number`),
  KEY `IDX_FAE7DA43B03A8386` (`created_by_id`),
  KEY `idx_world_entity_revision_entity` (`entity_id`),
  CONSTRAINT `FK_FAE7DA4381257D5D` FOREIGN KEY (`entity_id`) REFERENCES `world_entity` (`id`) ON DELETE CASCADE,
  CONSTRAINT `FK_FAE7DA43B03A8386` FOREIGN KEY (`created_by_id`) REFERENCES `user` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `world_event`
--

DROP TABLE IF EXISTS `world_event`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `world_event` (
  `id` int(11) NOT NULL,
  `location_id` int(11) DEFAULT NULL,
  `start_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  `end_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  `summary` longtext DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_BACED8A864D218E` (`location_id`),
  CONSTRAINT `FK_BACED8A864D218E` FOREIGN KEY (`location_id`) REFERENCES `world_location` (`id`) ON DELETE SET NULL,
  CONSTRAINT `FK_BACED8A8BF396750` FOREIGN KEY (`id`) REFERENCES `world_entity` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `world_event_participation`
--

DROP TABLE IF EXISTS `world_event_participation`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `world_event_participation` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `event_id` int(11) NOT NULL,
  `participant_entity_id` int(11) NOT NULL,
  `source_revision_id` int(11) NOT NULL,
  `created_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  `updated_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  `role` varchar(100) NOT NULL,
  `revealed_at` datetime DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)',
  `description` longtext DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_world_event_participation_event` (`event_id`),
  KEY `idx_world_event_participation_participant` (`participant_entity_id`),
  KEY `idx_world_event_participation_source_revision` (`source_revision_id`),
  CONSTRAINT `FK_8E19C76F21852C2F` FOREIGN KEY (`source_revision_id`) REFERENCES `world_entity_revision` (`id`) ON DELETE CASCADE,
  CONSTRAINT `FK_8E19C76F4227B279` FOREIGN KEY (`participant_entity_id`) REFERENCES `world_entity` (`id`) ON DELETE CASCADE,
  CONSTRAINT `FK_8E19C76F71F7E88B` FOREIGN KEY (`event_id`) REFERENCES `world_event` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `world_fact`
--

DROP TABLE IF EXISTS `world_fact`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `world_fact` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `subject_entity_id` int(11) NOT NULL,
  `source_revision_id` int(11) NOT NULL,
  `created_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  `updated_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  `fact_type` varchar(100) NOT NULL,
  `value_text` longtext NOT NULL,
  `revealed_at` datetime DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)',
  PRIMARY KEY (`id`),
  KEY `idx_world_fact_subject` (`subject_entity_id`),
  KEY `idx_world_fact_type` (`fact_type`),
  KEY `idx_world_fact_source_revision` (`source_revision_id`),
  CONSTRAINT `FK_B160994121852C2F` FOREIGN KEY (`source_revision_id`) REFERENCES `world_entity_revision` (`id`) ON DELETE CASCADE,
  CONSTRAINT `FK_B1609941C274E977` FOREIGN KEY (`subject_entity_id`) REFERENCES `world_entity` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `world_faction`
--

DROP TABLE IF EXISTS `world_faction`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `world_faction` (
  `id` int(11) NOT NULL,
  `faction_name` varchar(100) NOT NULL,
  PRIMARY KEY (`id`),
  CONSTRAINT `FK_6C41A5C7BF396750` FOREIGN KEY (`id`) REFERENCES `world_entity` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `world_item`
--

DROP TABLE IF EXISTS `world_item`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `world_item` (
  `id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  CONSTRAINT `FK_C1DFE7CABF396750` FOREIGN KEY (`id`) REFERENCES `world_entity` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `world_location`
--

DROP TABLE IF EXISTS `world_location`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `world_location` (
  `id` int(11) NOT NULL,
  `location_type` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`),
  CONSTRAINT `FK_AB7E08B2BF396750` FOREIGN KEY (`id`) REFERENCES `world_entity` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `world_person`
--

DROP TABLE IF EXISTS `world_person`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `world_person` (
  `id` int(11) NOT NULL,
  `species` varchar(100) DEFAULT NULL,
  `gender` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`),
  CONSTRAINT `FK_A4E2AC35BF396750` FOREIGN KEY (`id`) REFERENCES `world_entity` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*M!100616 SET NOTE_VERBOSITY=@OLD_NOTE_VERBOSITY */;

-- Dump completed on 2026-03-29 13:23:57
