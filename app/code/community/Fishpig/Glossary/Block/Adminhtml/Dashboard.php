<?php
/**
 * @category    Fishpig
 * @package     Fishpig_Glossary
 * @license     http://fishpig.co.uk/license.txt
 * @author      Ben Tideswell <help@fishpig.co.uk>
 */

class Fishpig_Glossary_Block_Adminhtml_Dashboard extends Mage_Adminhtml_Block_Widget_Tabs
{
	public function __construct()
	{
		parent::__construct();
		
		$this->setId('glossary_dashboard_tabs');
        $this->setDestElementId('glossary_tab_content');
		$this->setTitle($this->__('Glossary'));
		$this->setTemplate('widget/tabshoriz.phtml');
	}
	
	protected function _prepareLayout()
	{
		$tabs = array(
			'word' => 'Words',
		);
		
		$_layout = $this->getLayout();
		
		foreach($tabs as $alias => $label) {
			$this->addTab($alias, array(
				'label'     => Mage::helper('catalog')->__($label),
				'content'   => $_layout->createBlock('glossary/adminhtml_' . $alias)->toHtml(),
				'active'    => $alias === 'word',
			));
		}

		Mage::dispatchEvent('glossary_dashboard_tabs_prepare_layout', array('tabs' => $this));
				
		return parent::_prepareLayout();
	}
}
