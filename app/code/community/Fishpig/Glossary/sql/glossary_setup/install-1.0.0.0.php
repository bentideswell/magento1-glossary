<?php
/**
 * @category    Fishpig
 * @package    Fishpig_Glossary
 * @license      http://fishpig.co.uk/license.txt
 * @author       Ben Tideswell <ben@fishpig.co.uk>
 * @SkipObfuscation
 */

	$this->startSetup();
	
	$this->run("
		CREATE TABLE IF NOT EXISTS {$this->getTable('glossary_word')} (
			`word_id` int(11) unsigned NOT NULL auto_increment,
			`name` varchar(255) NOT NULL default '',
			`first_character` varchar(1) NOT NULL default '',
			`short_definition` TEXT NOT NULL default '',
			`definition` TEXT NOT NULL default '',
			`url_key` varchar(180) NOT NULL default '',
			`word_title` varchar(255) NOT NULL default '',
			`is_enabled` int(1) unsigned NOT NULL default 1,
			`created_at` TIMESTAMP NOT NULL DEFAULT '0000-00-00 00:00:00',
			`updated_at` TIMESTAMP NOT NULL DEFAULT '0000-00-00 00:00:00',
			PRIMARY KEY (`word_id`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Glossary: Word';
		
		CREATE TABLE IF NOT EXISTS {$this->getTable('glossary_word_store')} (
			`word_id` int(11) unsigned NOT NULL auto_increment,
			`store_id` smallint(5) unsigned NOT NULL default 0,
			PRIMARY KEY(`word_id`, `store_id`),
			KEY `FK_WORD_ID_GLOSSARY_WORD_GLOSSARY_WORD_STORE` (`word_id`),
			CONSTRAINT `FK_WORD_ID_GLOSSARY_WORD_GLOSSARY_WORD_STORE` FOREIGN KEY (`word_id`) REFERENCES `{$this->getTable('glossary_word')}` (`word_id`) ON DELETE CASCADE ON UPDATE CASCADE,
			KEY `FK_STORE_ID_GLOSSARY_WORD_CORE_STORE` (`store_id`),
			CONSTRAINT `FK_STORE_ID_GLOSSARY_WORD_CORE_STORE` FOREIGN KEY (`store_id`) REFERENCES `{$this->getTable('core_store')}` (`store_id`) ON DELETE CASCADE ON UPDATE CASCADE
		)  ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Glossary: Word Store Links';
	");

	$this->endSetup();
	