<?php
/**
 * @category    Fishpig
 * @package     Fishpig_Glossary
 * @license     http://fishpig.co.uk/license.txt
 * @author      Ben Tideswell <help@fishpig.co.uk>
 */
	
	$this->startSetup();

	$this->getConnection()->addColumn($this->getTable('glossary_word'), 'page_title', " varchar(255) NOT NULL default '' AFTER word_title");
	$this->getConnection()->addColumn($this->getTable('glossary_word'), 'meta_description', " TEXT NOT NULL default '' AFTER page_title");
	$this->getConnection()->addColumn($this->getTable('glossary_word'), 'meta_keywords', " TEXT NOT NULL default '' AFTER page_title");

	$this->endSetup();