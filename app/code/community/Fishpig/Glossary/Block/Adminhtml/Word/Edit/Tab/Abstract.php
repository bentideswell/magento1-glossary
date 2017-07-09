<?php
/**
 * @category    Fishpig
 * @package     Fishpig_Glossary
 * @license     http://fishpig.co.uk/license.txt
 * @author      Ben Tideswell <help@fishpig.co.uk>
 */

abstract class Fishpig_Glossary_Block_Adminhtml_Word_Edit_Tab_Abstract extends Mage_Adminhtml_Block_Widget_Form
{
	/**
	 * Generate the form object
	 *
	 * @return $this
	 */
	protected function _prepareForm()
	{
		$form = new Varien_Data_Form();

        $form->setHtmlIdPrefix('word_');
        $form->setFieldNameSuffix('word');
        
		$this->setForm($form);

		return parent::_prepareForm();
	}
	
	/**
	 * Retrieve the data used for the form
	 *
	 * @return array
	 */
	protected function _getFormData()
	{
		if ($word = Mage::registry('glossary_word')) {
			$data = $word->getData();
			
			if (!isset($data['is_enabled'])) {
				$data['is_enabled'] = 1;
			}
			
			return $data;
		}
		
		return array('is_enabled' => 1, 'store_id' => array(0));
	}
}
