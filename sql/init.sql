CREATE TABLE `mailgun_domains` (
 `domain_id` INT(11) NOT NULL AUTO_INCREMENT,
 `domain_name` VARCHAR(255) NOT NULL,
 `hooks_json` TEXT NOT NULL,
 `domain_json` TEXT NOT NULL,
 PRIMARY KEY (`domain_id`),
 UNIQUE INDEX `domain_name` (`domain_name`)
) ENGINE=InnoDB;

CREATE TABLE `mailgun_emails` (
 `email_id`     INT(10) UNSIGNED    NOT NULL AUTO_INCREMENT,
 `email`        VARCHAR(255)        NOT NULL DEFAULT '0',
 `dated`        DATETIME            NOT NULL DEFAULT '0000-00-00 00:00:00',
 `email_status` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT '1=unsubscribed',
 PRIMARY KEY (`email_id`),
 UNIQUE INDEX `email` (`email`),
 INDEX `email_status` (`email_status`)
) ENGINE=InnoDB;

CREATE TABLE `mailgun_hook_payloads` (
 `id` INT(11) NOT NULL AUTO_INCREMENT,
 `data` TEXT NOT NULL,
 PRIMARY KEY (`id`)
) ENGINE=InnoDB ;

CREATE TABLE `mailgun_lists` (
 `list_id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
 `list_name` VARCHAR(50) NOT NULL DEFAULT '0',
 `nr_emails` INT(10) UNSIGNED NOT NULL DEFAULT '0',
 PRIMARY KEY (`list_id`),
 UNIQUE INDEX `list_name` (`list_name`)
) ENGINE=InnoDB;

CREATE TABLE `mailgun_list_emails` (
 `list_id` INT(11) NOT NULL,
 `email_id` INT(11) NOT NULL,
 PRIMARY KEY (`list_id`, `email_id`)
) ENGINE=InnoDB;

CREATE TABLE `mailgun_messages` (
 `message_id` INT(11) NOT NULL AUTO_INCREMENT,
 `name` VARCHAR(50) NOT NULL DEFAULT '0',
 `subject` VARCHAR(255) NOT NULL DEFAULT '0',
 `message` MEDIUMTEXT NOT NULL,
 `is_html` TINYINT(1) UNSIGNED NOT NULL DEFAULT '1',
 `dated` DATETIME NOT NULL,
 PRIMARY KEY (`message_id`)
) ENGINE=InnoDB;


CREATE TABLE `mailgun_sends` (
 `send_id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
 `send_domain` VARCHAR(255) NOT NULL,
 `send_from` VARCHAR(255) NOT NULL,
 `send_status` TINYINT(3) UNSIGNED NOT NULL DEFAULT '0',
 `list_id` INT(10) UNSIGNED NOT NULL,
 `message_id` INT(10) UNSIGNED NOT NULL,
 `created` DATETIME NOT NULL,
 `nr_emails` INT(10) UNSIGNED NOT NULL DEFAULT '0',
 `nr_sent_ok` INT(10) UNSIGNED NOT NULL DEFAULT '0',
 `nr_opened` INT(10) UNSIGNED NOT NULL DEFAULT '0',
 `nr_unsub` INT(10) UNSIGNED NOT NULL DEFAULT '0',
 `nr_bounce` INT(10) UNSIGNED NOT NULL DEFAULT '0',
 `nr_complaint` INT(10) UNSIGNED NOT NULL DEFAULT '0',
 `nr_failed` INT(10) UNSIGNED NOT NULL DEFAULT '0',
 PRIMARY KEY (`send_id`)
) ENGINE=InnoDB;

CREATE TABLE `mailgun_sent` (
 `sent_id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
 `send_id` INT(11) UNSIGNED NULL DEFAULT NULL,
 `email_id` INT(11) UNSIGNED NULL DEFAULT NULL,
 `sent` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0',
 `opened` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0',
 `unsubscribed` TINYINT(1) NOT NULL DEFAULT '0',
 `bounce` TINYINT(1) NOT NULL DEFAULT '0',
 `complaint` TINYINT(1) NOT NULL DEFAULT '0',
 PRIMARY KEY (`sent_id`),
 INDEX `opened` (`opened`),
 INDEX `send_id` (`send_id`),
 INDEX `email_id` (`email_id`)
) ENGINE=InnoDB;