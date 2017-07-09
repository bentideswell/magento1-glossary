<?php
/**
 * @category    Fishpig
 * @package     Fishpig_Glossary
 * @license     http://fishpig.co.uk/license.txt
 * @author      Ben Tideswell <help@fishpig.co.uk>
 */

class Fishpig_Glossary_Block_Adminhtml_Word_Edit_Tab_Content extends Fishpig_Glossary_Block_Adminhtml_Word_Edit_Tab_Abstract
{
	/**
	 * Prepare the form
	 *
	 * @return $this
	 */
	protected function _prepareForm()
	{
		parent::_prepareForm();
		
		$fieldset = $this->getForm()->addFieldset('glossary_word_content', array(
			'legend'=> $this->helper('adminhtml')->__('Content'),
			'class' => 'fieldset-wide',
		));

		$htmlConfig = Mage::getSingleton('cms/wysiwyg_config')->getConfig(array(
			'add_widgets' => true,
			'add_variables' => true,
			'add_image' => true,
			'files_browser_window_url' => $this->getUrl('adminhtml/cms_wysiwyg_images/index'),
			'cleanup' => false,
		));

		$fields = array(
			'short_definition' => 'Short Definition',
			'definition' => 'Definition',
		);
		
		foreach($fields as $field => $label) {
			$fieldset->addField($field, 'editor', array(
				'name' => $field,
				'label' => $this->helper('adminhtml')->__($label),
				'title' => $this->helper('adminhtml')->__($label),
				'style' => 'width:100%; height:400px;',
				'config' => $field === 'definition' ? $htmlConfig : null,
			));
		}

		$this->getForm()->setValues($this->_getFormData());

		return $this;
	}
}
