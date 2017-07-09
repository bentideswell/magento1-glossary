<?php
/**
 * @category    Fishpig
 * @package     Fishpig_Glossary
 * @license     http://fishpig.co.uk/license.txt
 * @author      Ben Tideswell <help@fishpig.co.uk>
 */

class Fishpig_Glossary_Block_Adminhtml_Word_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
	/**
	 * Init the tabs block
	 *
	 * @return void
	 */
	public function __construct()
	{
		parent::__construct();
		$this->setId('glossary_word_tabs');
		$this->setDestElementId('edit_form');
		$this->setTitle($this->__('Glossary'));
	}
	
	/**
	 * Add tabs to the block
	 *
	 * @return $this
	 */
	protected function _beforeToHtml()
	{
		$tabs = array(
			'general' => 'General',
			'content' => 'Content',
			'meta' => 'Meta',
		);
		
		foreach($tabs as $alias => $label) {
			$this->addTab($alias, array(
				'label' => $this->__($label),
				'title' => $this->__($label),
				'content' => $this->getLayout()->createBlock('glossary/adminhtml_word_edit_tab_' . $alias)->toHtml(),
			));
		}
		
		return parent::_beforeToHtml();
	}
}
