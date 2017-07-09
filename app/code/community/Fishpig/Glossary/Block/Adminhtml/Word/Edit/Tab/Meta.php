<?php
/**
 * @category    Fishpig
 * @package     Fishpig_Glossary
 * @license     http://fishpig.co.uk/license.txt
 * @author      Ben Tideswell <help@fishpig.co.uk>
 */

class Fishpig_Glossary_Block_Adminhtml_Word_Edit_Tab_Meta extends Fishpig_Glossary_Block_Adminhtml_Word_Edit_Tab_Abstract
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
			->addFieldset('glossary_word_information', array(
				'legend'=> $this->__('Meta'),
				'class' => 'fieldset-wide',
			));
		
		$fieldset->addField('page_title', 'text', array(
			'name' 		=> 'page_title',
			'label' 	=> $this->__('Page Title'),
			'title' 	=> $this->__('Page Title'),
		));
		
		$fields = array(
			'meta_description' => 'Meta Description',
			'meta_keywords' => 'Meta Keywords',
		);
		
		foreach($fields as $field => $label) {
			$fieldset->addField($field, 'editor', array(
				'name' => $field,
				'label' => $this->helper('adminhtml')->__($label),
				'title' => $this->helper('adminhtml')->__($label),
			));
		}

		$this->getForm()->setValues($this->_getFormData());

		return $this;
	}
}
