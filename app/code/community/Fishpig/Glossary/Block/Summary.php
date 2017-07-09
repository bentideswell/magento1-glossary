<?php
/**
 * @category    Fishpig
 * @package     Fishpig_Glossary
 * @license     http://fishpig.co.uk/license.txt
 * @author      Ben Tideswell <help@fishpig.co.uk>
 */

class Fishpig_Glossary_Block_Summary extends Mage_Core_Block_Template
{
	/**
	 * Retrieve the title
	 *
	 * @return string
	 */
	public function getTitle()
	{
		if (!$this->_getData('title')) {
			return $this->__('Definitions');
		}
		
		return $this->_getData('title');
	}

	/**
	 * Retrieve the wrapping div ID
	 *
	 * @return string
	 */
	public function getWrapperId()
	{
		if (!$this->hasWrapperId()) {
			$this->setWrapperId('gls-summary-' . rand(111, 999));
		}
		
		return $this->_getData('wrapper_id');
	}

	/**
	 * Ensure template is set correctly
	 *
	 * @return $this
	 */
	protected function _beforeToHtml()
	{
		if (!$this->hasTemplate()) {
			$this->setTemplate('glossary/summary.phtml');
		}
		
		return parent::_beforeToHtml();
	}
}
