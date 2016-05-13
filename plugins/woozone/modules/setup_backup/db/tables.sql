-- --------------------------------------------------------

--
-- Table structure for table `{wp_prefix}amz_assets`
--

CREATE TABLE IF NOT EXISTS `{wp_prefix}amz_assets` (
	`id` BIGINT(15) UNSIGNED NOT NULL AUTO_INCREMENT,
	`post_id` INT(11) NOT NULL,
	`asset` VARCHAR(225) NULL DEFAULT NULL,
	`thumb` VARCHAR(225) NULL DEFAULT NULL,
	`download_status` ENUM('new','success','inprogress','error') NULL DEFAULT 'new',
	`hash` VARCHAR(32) NULL DEFAULT NULL,
	`media_id` INT(11) NULL DEFAULT '0',
	`msg` TEXT NULL,
	`date_added` DATETIME NULL DEFAULT NULL,
	`date_download` DATETIME NULL DEFAULT NULL,
	PRIMARY KEY (`id`),
	INDEX `post_id` (`post_id`),
	INDEX `hash` (`hash`),
	INDEX `media_id` (`media_id`),
	INDEX `download_status` (`download_status`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ;

-- --------------------------------------------------------

--
-- Table structure for table `{wp_prefix}amz_products`
--

CREATE TABLE IF NOT EXISTS `{wp_prefix}amz_products` (
	`id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
	`post_id` INT(11) NOT NULL,
	`post_parent` INT(11) NULL DEFAULT '0',
	`type` ENUM('post','variation') NULL DEFAULT 'post',
	`title` TEXT NULL,
	`nb_assets` INT(4) NULL DEFAULT '0',
	`nb_assets_done` INT(4) NULL DEFAULT '0',
	`status` ENUM('new','success') NULL DEFAULT 'new',
	PRIMARY KEY (`post_id`, `id`),
	UNIQUE INDEX `post_id` (`post_id`),
	INDEX `post_parent` (`post_parent`),
	INDEX `type` (`type`),
	INDEX `nb_assets` (`nb_assets`),
	INDEX `nb_assets_done` (`nb_assets_done`),
	INDEX `id` (`id`),
	INDEX `status` (`status`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ;

-- --------------------------------------------------------

--
-- Table structure for table `{wp_prefix}amz_cross_sell`
--

CREATE TABLE IF NOT EXISTS `{wp_prefix}amz_cross_sell` (
	`ASIN` VARCHAR(10) NOT NULL,
	`products` TEXT NULL,
	`nr_products` INT(11) NULL DEFAULT NULL,
	`add_date` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
	PRIMARY KEY (`ASIN`),
	UNIQUE INDEX `ASIN` (`ASIN`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ;

-- --------------------------------------------------------

--
-- Table structure for table `{wp_prefix}amz_report_log`
--

CREATE TABLE IF NOT EXISTS `{wp_prefix}amz_report_log` (
	`id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
	`log_id` VARCHAR(50) NULL DEFAULT NULL,
	`log_action` VARCHAR(50) NULL DEFAULT NULL,
	`desc` VARCHAR(255) NULL DEFAULT NULL,
	`log_data_type` VARCHAR(50) NULL DEFAULT NULL,
	`log_data` LONGTEXT NULL,
	`source` TEXT NULL,
	`date_add` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
	PRIMARY KEY (`id`),
	INDEX `log_id` (`log_id`),
	INDEX `log_action` (`log_action`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ;

-- --------------------------------------------------------

--
-- Table structure for table `{wp_prefix}amz_queue`
--

CREATE TABLE `{wp_prefix}amz_queue` (
	`id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
	`asin` VARCHAR(100) NOT NULL COLLATE 'utf8_unicode_ci',
	`status` ENUM('a','b','c') NOT NULL COLLATE 'utf8_unicode_ci',
	`status_msg` TEXT NOT NULL COLLATE 'utf8_unicode_ci',
	`from` VARCHAR(30) NOT NULL COLLATE 'utf8_unicode_ci',
	`created_date` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
	`imported_date` TIMESTAMP NOT NULL DEFAULT '0000-00-00 00:00:00',
	PRIMARY KEY (`id`),
	INDEX `asin` (`asin`),
	INDEX `status` (`status`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ;

-- --------------------------------------------------------

--
-- Table structure for table `{wp_prefix}amz_searches`
--

CREATE TABLE `{wp_prefix}amz_searches` (
	`id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
	`code` VARCHAR(32) NOT NULL COLLATE 'utf8_unicode_ci',
	`timestamp` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
	`status` ENUM('a','b','c') NOT NULL COLLATE 'utf8_unicode_ci',
	`published` ENUM('Y','N') NOT NULL COLLATE 'utf8_unicode_ci',
	`params` TEXT NOT NULL COLLATE 'utf8_unicode_ci',
	`provider` VARCHAR(20) NOT NULL COLLATE 'utf8_unicode_ci',
	`search_title` VARCHAR(100) NOT NULL COLLATE 'utf8_unicode_ci',
	`country` VARCHAR(10) NOT NULL COLLATE 'utf8_unicode_ci',
	`recurrency` VARCHAR(10) NOT NULL COLLATE 'utf8_unicode_ci',
	PRIMARY KEY (`id`),
	UNIQUE INDEX `code` (`code`),
	INDEX `provider` (`provider`),
	INDEX `country` (`country`),
	INDEX `recurrency` (`recurrency`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ;
