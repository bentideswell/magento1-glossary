<?php
/**
 * @category    Fishpig
 * @package    Fishpig_Glossary
 * @license      http://fishpig.co.uk/license.txt
 * @author       Ben Tideswell <ben@fishpig.co.uk>
 */

class Fishpig_Glossary_Helper_Data extends Mage_Core_Helper_Abstract
{
	/**
	 * Retrieve the URL to the index page
	 *
	 * @return string
	 */
	public function getIndexUrl()
	{
		return Mage::getUrl('', array(
			'_direct' => $this->getFrontName() . $this->getUrlSuffix(),
			'_secure' 	=> false,
			'_nosid' 	=> true,
			'_store' => Mage::app()->getStore()->getId(),
		));
	}

	/**
	 * Retrieve the front name of the extension
	 *
	 * @return string
	 */
	public function getFrontName()
	{
		return $this->_getConfigValue('glossary/seo/front_name', '/');
	}

	/**
	 * Retrieve the URL suffix
	 *
	 * @return string
	 */
	public function getUrlSuffix()
	{
		return Mage::getStoreConfig('glossary/seo/url_suffix');
	}
	
	/**
	 * Retrieve the reged used to match a word page
	 *
	 * @return string
	 */
	public function getWordPageRegex()
	{
		return '/^' . preg_quote($this->getFrontName(), '/') . '\/([^\/]+)' . preg_quote(rtrim($this->getUrlSuffix(), '/'), '/') . '$/';
	}
	
	/**
	 * Determine whether the breadcrumbs are enabled
	 *
	 * @return bool
	 */
	public function isBreadcrumbsEnabled()
	{
		return Mage::getStoreConfigFlag('glossary/breadcrumb/enabled');
	}
	
	/**
	 * Retrieve the breadcrumb label
	 *
	 * @return string
	 */
	public function getBreadcrumbLabel()
	{
		return $this->_getConfigValue('glossary/breadcrumb/label', '/');
	}
	
	/**
	 * Retrieve the index page title
	 *
	 * @return string
	 */
	public function getPageTitle()
	{
		return $this->_getConfigValue('glossary/seo/page_title');
	}
	
	/**
	 * Retrieve the index page meta description
	 *
	 * @return string
	 */
	public function getMetaDescription()
	{
		return $this->_getConfigValue('glossary/seo/meta_description');
	}
	
	/**
	 * Determine whether autolinking is enabled
	 *
	 * @return bool
	 */
	public function canAutolink()
	{
		return Mage::getStoreConfig('glossary/autolink/enabled');
	}
	
	/**
	 * Retrieve a list of allowed front names
	 *
	 * @return array
	 */
	public function getAutolinkAllowedModules()
	{
		return explode(',', trim(Mage::getStoreConfig('glossary/autolink/allowed_modules'), ','));
	}
	
	/**
	 * Retrieve a config value
	 *
	 * @param string $key
	 * @param string $trimChars = ' '
	 * @return string
	 */
	protected function _getConfigValue($key, $trimChars = ' ')
	{
		return ($value = trim(Mage::getStoreConfig($key), $trimChars)) !== ''
			? $value
			: (string)Mage::app()->getConfig()->getNode('default/' . $key);
	}
}
