<?php
/**
 * @category  Fishpig
 * @package  Fishpig_Glossary
 * @license    http://fishpig.co.uk/license.txt
 * @author    Ben Tideswell <help@fishpig.co.uk>
 */

class Fishpig_Glossary_Block_System_Config_Form_Field_Autolink_Library_Wztooltip_Options extends Mage_Adminhtml_Block_System_Config_Form_Field_Array_Abstract
{
	/**
	 * Prepare to render
	*/
	protected function _prepareToRender()
	{
		$this->addColumn('command', array(
			'label' => $this->__('Command'),
		));
		
		$this->addColumn('value', array(
			'label' => $this->__('Value'),
		));
	
		$this->_addAfter = false;
		$this->_addButtonLabel = $this->__('Add New');
	}
}

