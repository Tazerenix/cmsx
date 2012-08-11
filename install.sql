



-- ---
-- Globals
-- ---

-- SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
-- SET FOREIGN_KEY_CHECKS=0;

-- ---
-- Table 'cmsx_article'
-- Contains posts from articles.
-- ---

DROP TABLE IF EXISTS `cmsx_article`;
		
CREATE TABLE `cmsx_article` (
  `article_id` INTEGER(11) NOT NULL AUTO_INCREMENT,
  `user_id` INTEGER(11) NOT NULL,
  `article_created` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `article_edited` TIMESTAMP NULL DEFAULT NULL,
  `article_title` MEDIUMTEXT NULL DEFAULT NULL,
  `article_slug` VARCHAR(255) NULL DEFAULT NULL,
  `article_content` LONGTEXT NULL DEFAULT NULL,
  `article_comments` INTEGER(11) NULL DEFAULT NULL,
  `article_tags` INTEGER(11) NULL DEFAULT NULL,
  `article_categories` INTEGER NULL DEFAULT NULL,
  `article_draft` INTEGER(1) NULL DEFAULT NULL,
  `article_removed` INTEGER(1) NULL DEFAULT NULL,
  PRIMARY KEY (`article_id`),
KEY (`article_slug`, `user_id`, `article_draft`, `article_removed`)
) COMMENT 'Contains posts from articles.';

-- ---
-- Table 'cmsx_user'
-- Contains information about registered users.
-- ---

DROP TABLE IF EXISTS `cmsx_user`;
		
CREATE TABLE `cmsx_user` (
  `user_id` INTEGER(11) NOT NULL AUTO_INCREMENT,
  `user_type` INTEGER(11) NOT NULL DEFAULT 1 COMMENT 'KEY',
  `user_name` VARCHAR(255) NOT NULL,
  `user_slug` VARCHAR(255) NOT NULL,
  `user_password` VARCHAR(255) NOT NULL,
  `user_email` VARCHAR(255) NOT NULL,
  `user_created` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `user_lastvisit` TIMESTAMP NULL DEFAULT NULL,
  `user_birthday` TIMESTAMP NULL DEFAULT NULL,
  `user_timezone` TIMESTAMP NULL DEFAULT NULL,
  `user_description` MEDIUMTEXT NULL DEFAULT NULL,
  `user_location` MEDIUMTEXT NULL DEFAULT NULL,
  `user_url` MEDIUMTEXT NULL DEFAULT NULL,
  `user_banned` INTEGER(1) NOT NULL DEFAULT 0,
  `user_loginattempts` INTEGER NULL DEFAULT NULL,
  `user_locked` INTEGER(1) NULL DEFAULT NULL,
  `user_lockedtime` TIMESTAMP NULL DEFAULT NULL,
  `user_activated` INTEGER(1) NULL DEFAULT NULL,
  `user_activationkey` VARCHAR(255) NULL DEFAULT NULL,
  `user_removed` INTEGER(1) NULL DEFAULT NULL,
  PRIMARY KEY (`user_id`),
KEY (`user_type`, `user_slug`, `user_password`, `user_email`, `user_banned`, `user_activated`, `user_removed`)
) COMMENT 'Contains information about registered users.';

-- ---
-- Table 'cmsx_comment'
-- 
-- ---

DROP TABLE IF EXISTS `cmsx_comment`;
		
CREATE TABLE `cmsx_comment` (
  `comment_id` INTEGER NULL AUTO_INCREMENT DEFAULT NULL,
  `user_id` INTEGER(11) NULL DEFAULT NULL,
  `article_id` INTEGER(11) NULL DEFAULT NULL COMMENT 'Refers to either page or article.',
  `comment_created` TIMESTAMP NULL DEFAULT NULL,
  `comment_edited` TIMESTAMP NULL DEFAULT NULL,
  `comment_content` LONGTEXT NULL DEFAULT NULL,
  `comment_removed` INTEGER(1) NULL DEFAULT NULL,
  PRIMARY KEY (`comment_id`),
KEY (`article_id`, `user_id`, `comment_removed`)
);

-- ---
-- Table 'cmsx_page'
-- 
-- ---

DROP TABLE IF EXISTS `cmsx_page`;
		
CREATE TABLE `cmsx_page` (
  `page_id` INTEGER(11) NOT NULL AUTO_INCREMENT,
  `user_id` INTEGER(11) NOT NULL,
  `page_created` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `page_edited` TIMESTAMP NULL DEFAULT NULL,
  `page_title` MEDIUMTEXT NULL DEFAULT NULL,
  `page_slug` VARCHAR(255) NULL DEFAULT NULL,
  `page_content` LONGTEXT NULL DEFAULT NULL,
  `page_draft` INTEGER(1) NULL DEFAULT NULL,
  `page_removed` INTEGER(1) NULL DEFAULT NULL,
  PRIMARY KEY (`page_id`),
KEY (`page_slug`, `page_draft`, `page_removed`, `user_id`)
);

-- ---
-- Table 'cmsx_tag'
-- 
-- ---

DROP TABLE IF EXISTS `cmsx_tag`;
		
CREATE TABLE `cmsx_tag` (
  `tag_id` INTEGER(11) NULL AUTO_INCREMENT DEFAULT NULL,
  `tag_text` VARCHAR(64) NULL DEFAULT NULL,
  `tag_slug` VARCHAR(64) NULL DEFAULT NULL,
  `tag_created` TIMESTAMP NULL DEFAULT NULL,
  `tag_count` INTEGER(11) NULL DEFAULT NULL,
  `tag_removed` INTEGER(1) NULL DEFAULT NULL,
  PRIMARY KEY (`tag_id`),
KEY (`tag_slug`, `tag_removed`)
);

-- ---
-- Table 'cmsx_category'
-- 
-- ---

DROP TABLE IF EXISTS `cmsx_category`;
		
CREATE TABLE `cmsx_category` (
  `category_id` INTEGER NULL AUTO_INCREMENT DEFAULT NULL,
  `category_text` VARCHAR(64) NULL DEFAULT NULL,
  `category_slug` VARCHAR(64) NULL DEFAULT NULL,
  `category_created` TIMESTAMP NULL DEFAULT NULL,
  `category_count` INTEGER(11) NULL DEFAULT NULL,
  `category_removed` INTEGER(1) NULL DEFAULT NULL,
  PRIMARY KEY (`category_id`),
KEY (`category_slug`, `category_removed`)
);

-- ---
-- Table 'cmsx_link'
-- 
-- ---

DROP TABLE IF EXISTS `cmsx_link`;
		
CREATE TABLE `cmsx_link` (
  `link_id` INTEGER NULL AUTO_INCREMENT DEFAULT NULL,
  `link_text` VARCHAR(64) NULL DEFAULT NULL,
  `link_slug` VARCHAR(64) NULL DEFAULT NULL,
  `link_url` MEDIUMTEXT NULL DEFAULT NULL,
  `link_target` VARCHAR(32) NULL DEFAULT NULL,
  `link_created` TIMESTAMP NULL DEFAULT NULL,
  `link_visible` INTEGER(1) NULL DEFAULT NULL,
  `link_removed` INTEGER(1) NULL DEFAULT NULL,
  PRIMARY KEY (`link_id`),
KEY (`link_slug`, `link_visible`, `link_removed`)
);

-- ---
-- Table 'cmsx_option'
-- 
-- ---

DROP TABLE IF EXISTS `cmsx_option`;
		
CREATE TABLE `cmsx_option` (
  `option_id` INTEGER NULL AUTO_INCREMENT DEFAULT NULL,
  `option_name` VARCHAR(32) NULL DEFAULT NULL,
  `option_value` LONGTEXT NULL DEFAULT NULL,
  PRIMARY KEY (`option_id`),
KEY(`option_name`)
);
