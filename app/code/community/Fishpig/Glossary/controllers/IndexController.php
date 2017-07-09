<?php
/**
 * @category    Fishpig
 * @package     Fishpig_Glossary
 * @license     http://fishpig.co.uk/license.txt
 * @author      Ben Tideswell <help@fishpig.co.uk>
 */

class Fishpig_Glossary_IndexController extends Mage_Core_Controller_Front_Action
{
	/**
	 * Display the splash word
	 *
	 * @return void
	 */
	public function indexAction()
	{
		$this->loadLayout();
		$this->_applyIndexUpdates();
		$this->renderLayout();
	}
	
	protected function _applyIndexUpdates()
	{	
		$helper = Mage::helper('glossary');

		if ($head = $this->getLayout()->getBlock('head')) {
			$head->addItem('link_rel', Mage::helper('glossary')->getIndexUrl(), 'rel="canonical"');
			
			if ($pageTitle = $helper->getPageTitle()) {
				$this->_title($pageTitle);
//				$head->setTitle($pageTitle);
			}
			
			if ($description = $helper->getMetaDescription()) {
				$head->setDescription($description);
			}
		}
		
		

		if ($helper->isBreadcrumbsEnabled()) {
			if ($crumbs = $this->getLayout()->getBlock('breadcrumbs')) {
				$crumbs->addCrumb('home', array(
					'link' => Mage::getUrl(),
					'label' => $this->__('Home'),
				));
				
				$crumbs->addCrumb('glossary', array(
					'label' => $this->__($helper->getBreadcrumbLabel()),
				));
			}
		}
		
		return $this;
	}
}
