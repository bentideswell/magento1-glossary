<?php
/**
 * @category    Fishpig
 * @package    Fishpig_Glossary
 * @license      http://fishpig.co.uk/license.txt
 * @author       Ben Tideswell <ben@fishpig.co.uk>
 */

class Fishpig_Glossary_Controller_Router extends Mage_Core_Controller_Varien_Router_Abstract
{
	/**
	 * Initialize Controller Router
	 *
	 * @param Varien_Event_Observer $observer
	*/
	public function initControllerRouters(Varien_Event_Observer $observer)
	{
		$observer->getEvent()->getFront()->addRouter('glossary', $this);
	}

    /**
     * Validate and Match Cms Word and modify request
     *
     * @param Zend_Controller_Request_Http $request
     * @return bool
     */
    public function match(Zend_Controller_Request_Http $request)
    {
    	$pathInfo = trim($request->getPathInfo(), '/');
		

		if ($this->_isGlossaryUrl($pathInfo) !== false) {
			$request->setModuleName($this->_getFrontName())
				->setControllerName('index')
				->setActionName('index');
		}
		else if (($word = $this->_loadWord($pathInfo)) !== false) {
			Mage::register('glossary_word', $word);
			
			$request->setModuleName($this->_getFrontName())
				->setControllerName('word')
				->setActionName('view')
				->setParam('id', $word->getId());
		}
		else {
			return false;
		}
		
		$request->setAlias(
			Mage_Core_Model_Url_Rewrite::REWRITE_REQUEST_PATH_ALIAS,
			$pathInfo
		);

		return true;
	}
	
	/**
	 * Determine whether $pathInfo is valid for the glossary index page
	 *
	 * @param string $pathInfo
	 * @return bool
	 */
	protected function _isGlossaryUrl($pathInfo)
	{
		$helper = Mage::helper('glossary');

		return $pathInfo === $helper->getFrontName() . rtrim($helper->getUrlSuffix(), '/');
	}

	
	/**
	 * Determine whether $pathInfo is valid for any word for this store
	 *
	 * @param string $pathInfo
	 * @return false|Fishpig_Glossary_Model_Word
	 */
	protected function _loadWord($pathInfo)
	{
		$helper = Mage::helper('glossary');
		
		if (!preg_match($helper->getWordPageRegex(), $pathInfo, $match)) {
			return false;
		}

    	$word = Mage::getModel('glossary/word')
    		->setStoreId(Mage::app()->getStore()->getId())
    		->load($match[1], 'url_key');
			
		if (!$word->isEnabled()) {
			return false;
		}
		
		return $word;
	}

	
	/**
	 * Retrieve the frontName used by the module
	 *
	 * @return string
	 */
	protected function _getFrontName()
	{
		return (string)Mage::getConfig()->getNode()->frontend->routers->glossary->args->frontName;
	}
}
