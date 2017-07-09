<?php
/**
 * @category    Fishpig
 * @package     Fishpig_Glossary
 * @license     http://fishpig.co.uk/license.txt
 * @author      Ben Tideswell <help@fishpig.co.uk>
 */

class Fishpig_Glossary_Adminhtml_GlossaryController extends Mage_Adminhtml_Controller_Action
{
	/**
	 * Display a grid of splash groups
	 *
	 */
	public function indexAction()
	{
		$this->loadLayout();
		$this->_title('FishPig');
		$this->_title($this->__('Glossary'));
		$this->_setActiveMenu('cms/glossary');
		$this->renderLayout();
	}

	/**
	 * Display the grid of splash words without the container (header, footer etc)
	 * This is used to modify the grid via AJAX
	 *
	 */
	public function wordGridAction()
	{
		$this->getResponse()
			->setBody(
				$this->getLayout()->createBlock('glossary/adminhtml_word_grid')->toHtml()
			);
	}
	
	/**
	 * Display the Extend tab
	 *
	 * @return void
	 */
	public function extendAction()
	{
		$block = $this->getLayout()
			->createBlock('glossary/adminhtml_extend')
			->setModule('Fishpig_Glossary')
			->setMedium('Add-On Tab')
			->setTemplate('large.phtml')
			->setLimit(4)
			->setPreferred(array('Fishpig_Glossary_Addon_QuickCreate', 'Fishpig_Glossary_Addon_XmlSitemap', 'Fishpig_CrossLink', 'Fishpig_GlossaryPro', 'Fishpig_NoBots'));
			
		$this->getResponse()
			->setBody(
				$block->toHtml()
			);
	}

	/**
	 * Determine ACL permissions
	 *
	 * @return bool
	 */
	protected function _isAllowed()
	{
		return true;
	}
}
