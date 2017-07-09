<?php
/**
 * @category    Fishpig
 * @package     Fishpig_Glossary
 * @license     http://fishpig.co.uk/license.txt
 * @author      Ben Tideswell <help@fishpig.co.uk>
 */

class Fishpig_Glossary_Block_Autolink extends Mage_Core_Block_Template
{
	/**
	 * Retrieve the applicable words as a JSON object
	 *
	 * @return string
	 */
	public function getJsonDataObject()
	{
		if (!$this->hasJsonDataObject()) {
			$data = array();
			
			foreach($this->getInjectedWords() as $word) {
				$data[$word->getId()]	= ($word->getShortDefinition());
			}

			$this->setJsonDataObject(json_encode($data));
		}
		
		return $this->_getData('json_data_object');
	}
	
	/**
	 * Retrieve the string of params for the Tip method call
	 *
	 * @return string
	 */
	public function getWzTooltipExtra()
	{
		$extra = @unserialize(Mage::getStoreConfig('glossary/autolink/wz_tooltip_extra'));
		
		if (!$extra) {
			return '';
		}
		
		$jsString = ', ';
		
		foreach($extra as $item) {
			if ($item['value'] === '') {
				continue;
			}
			
			if (!in_array($item['value'], array('true', 'false')) && !is_numeric($item['value'])) {
				$item['value'] = "'" . $item['value'] . "'";
			}
			
			$jsString .= strtoupper($item['command']) . ', ' . $item['value'] . ', ';
		}
		
		return rtrim($jsString, ', ');
	}
}
