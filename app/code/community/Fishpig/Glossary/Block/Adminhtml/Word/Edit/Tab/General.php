<?php
/**
 * @category    Fishpig
 * @package     Fishpig_Glossary
 * @license     http://fishpig.co.uk/license.txt
 * @author      Ben Tideswell <help@fishpig.co.uk>
 */

class Fishpig_Glossary_Block_Adminhtml_Word_Edit_Tab_General extends Fishpig_Glossary_Block_Adminhtml_Word_Edit_Tab_Abstract
{
	/**
	 * Setup the form fields
	 *
	 * @return $this
	 */
	protected function _prepareForm()
	{
		parent::_prepareForm();

		$fieldset = $this->getForm()
			->addFieldset('glossary_word_information', array('legend'=> $this->__('General')));
		
		$fieldset->addField('name', 'text', array(
			'name' 		=> 'name',
			'label' 	=> $this->__('Word'),
			'title' 	=> $this->__('Word'),
			'required'	=> true,
			'class'		=> 'required-entry',
		));
		
		/*
		$fieldset->addField('word_title', 'text', array(
			'name' 		=> 'word_title',
			'label' 	=> $this->__('Word Title'),
			'title' 	=> $this->__('Word Title'),
		));
		*/
		
		$field = $fieldset->addField('url_key', 'text', array(
			'name' => 'url_key',
			'label' => $this->__('URL Key'),
			'title' => $this->__('URL Key'),
		));

		if (!Mage::app()->isSingleStoreMode()) {
			$field = $fieldset->addField('store_ids', 'multiselect', array(
				'name' => 'store_ids[]',
				'label' => Mage::helper('cms')->__('Store View'),
				'title' => Mage::helper('cms')->__('Store View'),
				'required' => true,
				'values' => Mage::getSingleton('adminhtml/system_store')->getStoreValuesForForm(false, true),
			));

			$renderer = $this->getLayout()->createBlock('adminhtml/store_switcher_form_renderer_fieldset_element');
			
			if ($renderer) {
				$field->setRenderer($renderer);
			}
		}

		$fieldset->addField('is_enabled', 'select', array(
			'name' => 'is_enabled',
			'title' => $this->__('Is Enabled'),
			'label' => $this->__('Is Enabled'),
			'values' => Mage::getModel('adminhtml/system_config_source_yesno')->toOptionArray(),
		));

		$this->getForm()->setValues($this->_getFormData());

		return $this;
	}
}
