Table,Create Table
jos_whelpdesk_access_controls,CREATE TABLE `jos_whelpdesk_access_controls` (
  `grp` varchar(100) NOT NULL,
  `type` varchar(100) NOT NULL,
  `control` varchar(100) NOT NULL,
  `description` text,
  PRIMARY KEY  (`grp`,`type`,`control`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC

0 rows affected
Table,Create Table
jos_whelpdesk_access_map,CREATE TABLE `jos_whelpdesk_access_map` (
  `grp` varchar(100) NOT NULL,
  `request_type` varchar(100) NOT NULL,
  `request_identifier` varchar(100) NOT NULL,
  `target_type` varchar(100) NOT NULL,
  `target_identifier` varchar(100) NOT NULL,
  `allow` tinyint(1) unsigned default '0',
  `control` varchar(100) NOT NULL,
  `type` varchar(100) NOT NULL,
  PRIMARY KEY  USING BTREE (`grp`,`request_type`,`request_identifier`,`target_type`,`target_identifier`,`control`,`type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC

0 rows affected
Table,Create Table
jos_whelpdesk_data_fields,CREATE TABLE `jos_whelpdesk_data_fields` (
  `group` int(10) unsigned NOT NULL,
  `name` varchar(100) NOT NULL,
  `label` varchar(255) NOT NULL,
  `description` varchar(255) NOT NULL,
  `default` varchar(255) NOT NULL,
  `params` text NOT NULL,
  `system` tinyint(1) unsigned NOT NULL default '0',
  `type` varchar(20) NOT NULL,
  `ordering` int(10) unsigned NOT NULL default '1',
  `list` tinyint(1) unsigned NOT NULL default '0',
  `checked_out` int(10) unsigned NOT NULL,
  `checked_out_time` datetime NOT NULL,
  `version` int(10) unsigned NOT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY  (`group`,`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC

0 rows affected
Table,Create Table
jos_whelpdesk_data_groups,CREATE TABLE `jos_whelpdesk_data_groups` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `name` varchar(45) NOT NULL,
  `ordering` varchar(45) NOT NULL,
  `system` tinyint(3) unsigned default '0',
  `description` text NOT NULL,
  `table` varchar(100) NOT NULL,
  `label` varchar(45) NOT NULL,
  `version` int(10) unsigned NOT NULL,
  `checked_out` int(10) unsigned NOT NULL,
  `checked_out_time` datetime NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC

0 rows affected
Table,Create Table
jos_whelpdesk_data_tables,CREATE TABLE `jos_whelpdesk_data_tables` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `table` varchar(50) NOT NULL,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC

0 rows affected
Table,Create Table
jos_whelpdesk_document_containers,CREATE TABLE `jos_whelpdesk_document_containers` (
  `alias` varchar(100) NOT NULL,
  `name` varchar(255) NOT NULL,
  `created` datetime NOT NULL,
  `checked_out` int(10) unsigned NOT NULL,
  `checked_out_time` datetime NOT NULL,
  `hits` int(10) unsigned NOT NULL,
  `id` int(10) unsigned NOT NULL auto_increment,
  `description` text,
  `parent` int(10) unsigned NOT NULL default '0',
  `field_metadata_author` varchar(45) NOT NULL,
  `field_metadata_description` varchar(255) NOT NULL,
  `modified` datetime NOT NULL,
  `creator` int(10) unsigned NOT NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `UNIQUEALIAS` USING BTREE (`alias`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC

0 rows affected
Table,Create Table
jos_whelpdesk_request_categories,CREATE TABLE `jos_whelpdesk_request_categories` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(500) NOT NULL,
  `description` text NOT NULL,
  `published` tinyint(1) unsigned NOT NULL,
  `checked_out` int(10) unsigned NOT NULL,
  `checked_out_time` datetime NOT NULL,
  `created_by` int(10) unsigned NOT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  `revised` int(10) unsigned NOT NULL default '0',
  `alias` varchar(500) NOT NULL,
  `parent_id` int(10) unsigned NOT NULL,
  `level` int(10) unsigned NOT NULL,
  `lft` int(10) unsigned NOT NULL,
  `rgt` int(10) unsigned NOT NULL,
  `path` text NOT NULL,
  `state` tinyint(1) unsigned NOT NULL default '0',
  `ordering` int(10) unsigned NOT NULL,
  `access` int(10) unsigned NOT NULL,
  `metakey` text,
  `metadesc` text,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC

0 rows affected
Table,Create Table
jos_whelpdesk_request_priorities,CREATE TABLE `jos_whelpdesk_request_priorities` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(500) NOT NULL,
  `description` text NOT NULL,
  `published` tinyint(1) unsigned NOT NULL default '1',
  `checked_out` int(10) unsigned NOT NULL,
  `checked_out_time` datetime NOT NULL,
  `created_by` int(10) unsigned NOT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  `revised` int(10) unsigned NOT NULL default '0',
  `ordering` int(10) unsigned NOT NULL,
  `access` int(10) unsigned NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC

0 rows affected
Table,Create Table
jos_whelpdesk_request_replies,CREATE TABLE `jos_whelpdesk_request_replies` (
  `id` int(11) NOT NULL auto_increment,
  `description` text NOT NULL,
  `created_by` int(10) unsigned NOT NULL,
  `created` datetime NOT NULL,
  `request_id` int(10) unsigned NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC

0 rows affected
Table,Create Table
jos_whelpdesk_requests,CREATE TABLE `jos_whelpdesk_requests` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(500) NOT NULL,
  `description` text NOT NULL,
  `checked_out` int(10) unsigned NOT NULL,
  `checked_out_time` datetime NOT NULL,
  `created_by` int(10) unsigned NOT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  `revised` int(10) unsigned NOT NULL default '0',
  `alias` varchar(500) NOT NULL,
  `category_id` int(10) unsigned NOT NULL,
  `status` tinyint(1) unsigned NOT NULL,
  `priority` int(10) unsigned NOT NULL,
  `assignee` int(10) unsigned NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC

0 rows affected
Table,Create Table
jos_whelpdesk_tree,CREATE TABLE `jos_whelpdesk_tree` (
  `grp` varchar(100) NOT NULL default '',
  `type` varchar(100) NOT NULL default '',
  `identifier` varchar(100) NOT NULL default '0',
  `parent_type` varchar(100) default NULL,
  `parent_identifier` varchar(100) default NULL,
  `lft` int(11) default NULL,
  `rgt` int(11) default NULL,
  `description` varchar(255) default NULL,
  PRIMARY KEY  (`grp`,`type`,`identifier`),
  KEY `PARENT` (`parent_type`,`parent_identifier`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8

0 rows affected
Table,Create Table
jos_whelpdesk_tree_groups,CREATE TABLE `jos_whelpdesk_tree_groups` (
  `grp` varchar(100) NOT NULL default 'system',
  `description` text,
  PRIMARY KEY  (`grp`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC

0 rows affected
