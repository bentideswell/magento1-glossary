<?php
/**
 * @category    Fishpig
 * @package     Fishpig_Glossary
 * @license     http://fishpig.co.uk/license.txt
 * @author      Ben Tideswell <help@fishpig.co.uk>
 */

class Fishpig_Glossary_Block_Word extends Mage_Core_Block_Template
{
	/**
	 * Retrieve the current word
	 *
	 * @return null|Fishpig_Glossary_Model_Word
	 */
	public function getWord()
	{
		if (!$this->hasWord()) {
			$this->setWord(Mage::registry('glossary_word'));
		}
		
		return $this->_getData('word');
	}
	
	/*
	 *
	 *
	 * @return string
	 */
	public function getGlossaryUrl()
	{
		return Mage::helper('glossary')->getIndexUrl();
	}
}
