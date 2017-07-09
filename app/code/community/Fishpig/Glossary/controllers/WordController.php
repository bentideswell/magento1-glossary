<?php
/**
 * @category    Fishpig
 * @package     Fishpig_Glossary
 * @license     http://fishpig.co.uk/license.txt
 * @author      Ben Tideswell <help@fishpig.co.uk>
 */

class Fishpig_Glossary_WordController extends Mage_Core_Controller_Front_Action
{
	/**
	 * Display the splash word
	 *
	 * @return void
	 */
	public function viewAction()
	{
		if (($word = $this->_initWord()) === false) {
			return $this->_forward('noRoute');
		}
		
		$this->loadLayout();
		$this->_applyViewUpdates();
		$this->renderLayout();
	}
	
	
	protected function _applyViewUpdates()
	{
		$word = $this->_initWord();
		$helper = Mage::helper('glossary');

		if ($head = $this->getLayout()->getBlock('head')) {
			if ($word->getPageTitle()) {
				$head->setTitle($word->getPageTitle());
			}
			else {
				$this->_title($helper->getPageTitle());
				$this->_title($word->getWord());
			}
			
			if ($word->getMetaDefinition()) {
				$head->setDefinition($word->getMetaDefinition());
			}
			else if ($word->getShortDefinition()) {
				$head->setDefinition(strip_tags($word->getShortDefinition()));
			}
			
			if ($word->getMetaDescription()) {
				$head->setDescription($word->getMetaDescription());
			}
			
			if ($word->getMetaKeywords()) {
				$head->setKeywords($word->getMetaKeywords());
			}
			
			$head->addItem('link_rel', $word->getUrl(), 'rel="canonical"');
		}
		
		if ($helper->isBreadcrumbsEnabled()) {
			if ($crumbs = $this->getLayout()->getBlock('breadcrumbs')) {
				$crumbs->addCrumb('home', array(
					'link' => Mage::getUrl(),
					'label' => $this->__('Home'),
				));
				
				$crumbs->addCrumb('glossary', array(
					'label' => $this->__($helper->getBreadcrumbLabel()),
					'link' => Mage::helper('glossary')->getIndexUrl(),
				));
			
				$crumbs->addCrumb('glossary_word', array(
					'label' => $word->getWord(),
				));
			}
		}

		return $this;
	}

	/**
	 * Initialise the Glossary Word model
	 *
	 * @return false|Fishpig_Glossary_Model_Word
	 */
	protected function _initWord()
	{
		if (($object = Mage::registry('glossary_word')) !== null) {
			return $object;
		}

		$object = Mage::getModel('glossary/word')
			->setStoreId(Mage::app()->getStore()->getId())
			->load((int) $this->getRequest()->getParam('id', false));

		if (!$object->getIsEnabled()) {
			return false;
		}
		
		Mage::register('glossary_word', $object);

		return $object;
	}
}
