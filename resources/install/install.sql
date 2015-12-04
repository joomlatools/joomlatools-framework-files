CREATE TABLE IF NOT EXISTS `#__files_containers` (
  `files_container_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `slug` varchar(255) NOT NULL,
  `title` varchar(255) NOT NULL,
  `path` varchar(255) NOT NULL,
  `parameters` text NOT NULL,
  PRIMARY KEY (`files_container_id`),
  UNIQUE KEY `slug` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__files_thumbnails` (
  `files_thumbnail_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `files_container_id` varchar(255) NOT NULL,
  `folder` varchar(255) NOT NULL,
  `filename` varchar(255) NOT NULL,
  `thumbnail` mediumtext NOT NULL,
  PRIMARY KEY (`files_thumbnail_id`)
) DEFAULT CHARSET=utf8;

CREATE TABLE `#__files_attachments` (
  `files_attachment_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `files_container_id` bigint(20) NOT NULL,
  `uuid` char(36) NOT NULL DEFAULT '',
  `path` varchar(2000) NOT NULL,
  `name` varchar(255) NOT NULL DEFAULT '',
  `description` text,
  `created_by` bigint(20) NOT NULL,
  `created_on` datetime NOT NULL,
  `modified_by` bigint(20) NOT NULL,
  `modified_on` datetime NOT NULL,
  `locked_by` bigint(20) NOT NULL,
  `locked_on` datetime NOT NULL,
  PRIMARY KEY (`files_attachment_id`),
  UNIQUE KEY `uuid` (`uuid`),
  UNIQUE KEY `files_container_id` (`files_container_id`,`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `#__files_attachments_relations` (
  `files_attachment_id` bigint(20) NOT NULL,
  `table` varchar(255) NOT NULL DEFAULT '',
  `row` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`files_attachment_id`,`table`,`row`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
