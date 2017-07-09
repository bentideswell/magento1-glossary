<?php
/**
 * @category    Fishpig
 * @package     Fishpig_Glossary
 * @license     http://fishpig.co.uk/license.txt
 * @author      Ben Tideswell <help@fishpig.co.uk>
 */

class Fishpig_Glossary_Block_Index extends Mage_Core_Block_Template
{
	/**
	 * Cache for word collection
	 *
	 * @var Fishpig_Glossary_Model_Resource_Word_Collection
	 */
	protected $_words = null;
	
	/**
	 * An array of available anchor characters
	 *
	 * @var array
	 */
	protected $_anchors = array();
	
	/**
	 * Retrieve a loaded collection of available words
	 *
	 * @return Fishpig_Glossary_Model_Resource_Word_Collection
	 */
	public function getWords()
	{
		if (is_null($this->_words)) {
			$this->_words = Mage::getResourceModel('glossary/word_collection')
				->addStoreFilter(Mage::app()->getStore())
				->addIsEnabledFilter()
				->load();
		}
		
		return $this->_words;
	}

	/**
	 * Pass the word collection to the navigation block
	 *
	 * @return $this
	 */
	protected function _beforeToHtml()
	{
		if ($words = $this->getWords()) {
			if ($navigation = $this->_getNavigationBlock()) {
				$navigation->setWords($words);
			}
		}
		
		return parent::_beforeToHtml();
	}
	
	/**
	 * Retrieve the navigation block
	 *
	 * @return null|Fishpig_Glossary_Block_Index_Navigation
	 */
	protected function _getNavigationBlock()
	{
		return $this->getChild('navigation');
	}
	
	/**
	 * Retrieve the navigation block HTML
	 *
	 * @return string
	 */
	public function getNavigationHtml()
	{
		return $this->getChildHtml('navigation');
	}
	
	/**
	 * Determine whether to print an anchor
	 *
	 * @param string $firstCharacter
	 * @return bool
	 */
	public function canPrintAnchor($firstCharacter)
	{
		if (!in_array($firstCharacter, $this->_anchors)) {
			$this->_anchors[] = $firstCharacter;
			
			return true;
		}
		
		return false;
	}
}
