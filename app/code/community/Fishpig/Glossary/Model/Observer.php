<?php
/**
 * @category    Fishpig
 * @package     Fishpig_Glossary
 * @license     http://fishpig.co.uk/license.txt
 * @author      Ben Tideswell <help@fishpig.co.uk>
 */

class Fishpig_Glossary_Model_Observer
{
	/**
	 * Multi byte encoding
	 *
	 * @const string
	 */
	 const MB_ENCODING = 'UTF-8';
	 
	/**
	 * Tags used to skip blocks
	 *
	 * @const string
	 */
	const INCLUDE_TAG_START = '<!-- Glossary Start -->';
	const INCLUDE_TAG_END = '<!-- Glossary End -->';

	/**
	 * Tags used to skip blocks
	 *
	 * @const string
	 */	
	const SKIP_TAG_START = '<!--Glossary-Skip-->';
	const SKIP_TAG_END = '<!--/Glossary-Skip-->';
	/**
	 * Allows storage of HTML in exchange for a key
	 * Key's can be swapped from HTML to reverse the process
	 *
	 * @var array
	 */
	protected $_safe = array();

	/**
	 * Add comments to blocks that should be skipped
	 *
	 * @param Varien_Event_Observer $observer
	 * @return void
	 */
	public function addBlockCommentsObserver(Varien_Event_Observer $observer)
	{
		if (!Mage::helper('glossary')->canAutolink() || !$this->isValidRoute()) {
			return $this;	
		}

		$nameInLayout = $observer->getEvent()
			->getBlock()
				->getNameInLayout();
				
		if ($nameInLayout === 'content') {
			$transport = $observer->getEvent()->getTransport();
			
			$transport->setHtml(self::INCLUDE_TAG_START . $transport->getHtml() . self::INCLUDE_TAG_END);
		}
	}
	
	/**
	 * Inject links to the HTML response
	 *
	 * @param Varien_Event_Observer $observer
	 * @return $this
	 */	
	public function injectWordLinksObserver(Varien_Event_Observer $observer)
	{
		if (!Mage::helper('glossary')->canAutolink() || !$this->isValidRoute()) {
			return $this;
		}

		if (!($words = $this->getWords())) {
			return $this;
		}

		$response = $observer->getEvent()
			->getFront()
				->getResponse();

		$html = $response->getBody();
				
		$originalEncoding = mb_internal_encoding();
		@mb_internal_encoding(self::MB_ENCODING);
			
		if ($html = $this->_injectWordLinks($html, $words)) {
			$response->setBody($html);
		}

		@mb_internal_encoding($originalEncoding);
		
		return $this;
	}
	
	/**
	 * Inject the $links into $html
	 *
	 * @param string $html
	 * @param array $links = array
	 * @return string
	 */
	protected function _injectWordLinks($html, $words)
	{
		$html = $this->_processRawHtml($html);
		$isInjected = array();
		
		$maxLinksPerWord = (int)Mage::getStoreConfig('glossary/autolink/max_links_per_word');
		
		if ($maxLinksPerWord === 0) {
			$maxLinksPerWord = 999;
		}

		foreach($words as $link => $word) {
			$maxLinksPerWordBuffer = $maxLinksPerWord;

			while ($maxLinksPerWordBuffer > 0 && $this->_injectLink($html, $link, $word, 10)) {
				$isInjected[] = $word;
				--$maxLinksPerWordBuffer;
			}
		}

		if (count($isInjected) === 0) {
			return false;
		}

		$this->emptySafe($html);
		
		$autolinkHtml = Mage::getSingleton('core/layout')
			->createBlock('glossary/autolink')
			->setTemplate('glossary/autolink.phtml')
			->setInjectedWords($isInjected)
			->toHtml();

		$html = str_replace('</body>', $autolinkHtml . '</body>', $html);

		return trim($html);
	}

	/**
	 * Wrapper for preg_replace_callback
	 *
	 * @param string $pattern
	 * @param string $callback
	 * @param string $source
	 * @return string
	 */
	protected function _pregReplaceCallback($pattern, $callback, $source)
	{
		if (preg_match_all($pattern, $source, $result)) {
			array_shift($result);
			
			foreach($result as $it => $matches) {
				foreach($matches as $match) {
					$source = str_replace($match, $this->addToSafe($match), $source);
				}
			}
		}
		
		return $source;
	}
	
	/**
	 * Clean the HTML and removed all content that is not linkable
	 *
	 * @param string $html
	 * @return string
	 */
	protected function _processRawHtml($html)
	{
		// Remove existing comments
		$html = $this->_pregReplaceCallback('/(<!--.*-->)/Us', array($this, 'addToSafe'), trim($html));
		
		// Remove everything but the body tag
		$html = $this->_pregReplaceCallback('/^(.*<body[^>]*>)/Us', array($this, 'addToSafe'), trim($html));
		$html = $this->_pregReplaceCallback('/(<\/body>.*)$/Us', array($this, 'addToSafe'), trim($html));		


		$html = $this->_pregReplaceCallback(sprintf("/(^.*%s)/Us", preg_quote(self::INCLUDE_TAG_START)), array($this, 'addToSafe'), trim($html));
		$html = $this->_pregReplaceCallback(sprintf("/(%s.*$)/Us", preg_quote(self::INCLUDE_TAG_END)), array($this, 'addToSafe'), trim($html));

		// Remove skipped blocks
		$html = $this->_pregReplaceCallback(sprintf("/(%s.*%s)/Us", preg_quote(self::SKIP_TAG_START, '/'), preg_quote(self::SKIP_TAG_END, '/')), array($this, 'addToSafe'), trim($html));

		// Save some simple tags
		$tags = array('noscript', 'pre', 'select', 'address', 'fb:comments', 'script', 'iframe', 'a', 'select', 'button', 'textarea');
		
		foreach($tags as $tag) {
			$html = $this->_pregReplaceCallback('/(<' . $tag . '[^>]{0,}>.*<\/' . $tag . '>)/iUs', array($this, 'addToSafe'), $html);
		}

		// Clean the HTML
		$html = preg_replace("/(\n|\r|\t)/", ' ', $html);
		$html = preg_replace('/([ ]{1,})/',  ' ', $html);
        
		// Strip headings
		$html = $this->_pregReplaceCallback('/(<h[1-6]{1}[^>]{0,}>.*<\/h[1-6]{1}>)/iU', array($this, 'addToSafe'), $html);

		// Add tags with parameters to the safe
		$html = $this->_pregReplaceCallback('/(<[a-z]{1,} .*>)/iU', array($this, 'addToSafe'), $html);

		// Pad the HTML to make things a little easier
		return ' ' . $html . ' ';
	}
	
	/**
	 * Retrieve the link data
	 *
	 * @return array
	 */
	public function getWords()
	{
		$words = Mage::getResourceModel('glossary/word_collection')
			->addStoreFilter(Mage::app()->getStore()->getId())
			->addIsEnabledFilter()
			->load();
			
		if (count($words) === 0) {
			return false;
		}
		
		$links = array();
		
		foreach($words as $word) {
			$links[$word->getUrl()] = $word;
		}
		
		return $links;
	}
	
	/**
	 * Add some content to the safe
	 *
	 * @param string $html
	 * @param int $door = 0
	 * @return string 
	 */
	public function addToSafe($html, $door = 0)
	{
		if (!isset($this->_safe[$door])) {
			$this->_safe[$door] = array();
			$key = 0;
		}
		else {
			$key = count($this->_safe[$door]);
		}
		
		$html = is_array($html) ? $html[1] : $html;

		$this->_safe[$door][$key] = $html;	
		
		return $this->generateSafeKey($key, $door);
	}
	
	/**
	 * Swap keys for content in the safe
	 *
	 * @param string $content
	 * @param int $door = null
	 * @return $this
	 */
	public function emptySafe(&$content, $door = null)
	{
		if (is_null($door)) {
			$safe = array_reverse($this->_safe, true);
			
			foreach($safe as $door => $values) {
				$values = array_reverse($values, true);
				
				foreach($values as $key => $value) {
					$content = str_replace($this->generateSafeKey($key, $door), $value, $content);
				}
			}
		}
		else if (isset($this->_safe[$door])) {
			foreach($this->_safe[$door] as $key => $value) {
				$content = str_replace($this->generateSafeKey($key, $door), $value, $content);
			}
		}
		
		return $this;
	}
	
	/**
	 * Generate a key for the safe
	 *
	 * @param int $i
	 * @param int $door = 0
	 * @return string
	 */
	public function generateSafeKey($i, $door = 0)
	{
		return sprintf('<!--SF%d-%d-->', $door, $i);
	}
	
	/**
	 * Inject a link into $html using $link and $keyword
	 *
	 * @param string &$html
	 * @param string $link
	 * @param string $keyword
	 * @param int $door = 0
	 * @return bool|null
	 */
	protected function _injectLink(&$html, $link, $word, $door = 0)
	{
		$keyword = $word->getWord();
		$strpos = Mage::getStoreConfigFlag('glossary/autolink/case_sensitive') ? 'mb_strpos' : 'mb_stripos';
		$strlen = 'mb_strlen'; // 'strlen';
		$substr = 'mb_substr'; // 'mb_substr'
		$offset = 0;
		
		while($offset < $strlen($html) && ($position = $strpos($html, $keyword, $offset)) !== false) {
			$offset = $position + $strlen($keyword);
			$charAfter = $substr($html, $position+$strlen($keyword), 1);
			$charBefore = $substr($html, $position-1, 1);
			$origKeyword = $substr($html, $position, $strlen($keyword));
			$realKeyword = $origKeyword;
			$perfectMatch = true;
			
			if ($this->isAlphaNumericChar($charBefore)) {
				$perfectMatch = false;
				$beforeRealKeyword = $substr($html, 0, $position);

				if (preg_match('/([a-zA-Z0-9]{1,})$/x', $beforeRealKeyword, $matches)) {
					$realKeyword = $matches[1] . $origKeyword;
				}
			}
			
			if ($this->isAlphaNumericChar($charAfter)) {
				$perfectMatch = false;
				$afterRealKeyword = $substr($html, $position+$strlen($origKeyword));

				if (preg_match('/^(.*)[^a-zA-Z0-9]{1}/U', $afterRealKeyword, $matches)) {
					$realKeyword .= $matches[1];
				}
				
				$safeKey = $this->addToSafe($realKeyword, $door);
			}

			if ($perfectMatch !== false) {			
				$safeKey = $this->addToSafe($this->_generateLinkTag($link, $realKeyword, $word));
				$html = $substr($html, 0, $position) . $safeKey . $substr($html, $position+$strlen($realKeyword));

				return true;
			}
		}
		
		return false;
	}
	
	/**
	 * Generate a HTML link tag
	 *
	 * @param string $href
	 * @param string $anchor
	 * @return string
	 */
	protected function _generateLinkTag($href, $anchor, $word)
	{
		return sprintf('<a href="%s" class="%s">%s</a>', $href, 'gls gls-' . $word->getId(), $anchor);
	}

	/**
	 * Determine whether $char is a letter or a number
	 *
	 * @param string $char
	 * @return bool
	 */
	public function isAlphaNumericChar($char)
	{
		if (mb_strlen($char) === 2) {
			return preg_match('/[\w\p{L}\p{N}\p{Pd}]+/u', $char);
		}
		
		$char = ord(strtolower($char));

		return ($char >= ord('a') && $char <= ord('z')) || ($char >= ord('0') && $char <= ord('9'));;		
	}
	
	/**
	 * Retrieve the total number of links allowed
	 *
	 * @return int
	 */
	public function getLinkLimit($key)
	{
		return ($max = (int)Mage::getStoreConfig('glossary/autolink/' . $key)) > 0 ? $max : 9999;
	}	
	
	/**
	 * Determine whether the current route is a valid route
	 *
	 * @return bool
	 */
	public function isValidRoute()
	{
    	if ($this->isAjax()) {
        	return false;
    	}

		$request = Mage::app()->getRequest();
		$allowedModules = Mage::helper('glossary')->getAutolinkAllowedModules();
		
		if (!in_array($request->getModuleName(), $allowedModules)) {
			return false;
		}
		
		if (!($disallowedRoutes = trim(Mage::getStoreConfig('glossary/autolink/disallowed_routes')))) {
			return true;
		}
		
		$disallowedRoutes = explode("\n", strtolower($disallowedRoutes));
		$currentRoute = strtolower($request->getModuleName() . '_' . $request->getControllerName() . '_' . $request->getActionName());
				
		foreach($disallowedRoutes as $disallowedRoute) {
			if ($disallowedRoute === $currentRoute) {
				return false;
			}
		}
		
		return true;
	}
	
	/**
	 * Determine whether the current request is an ajax request
	 *
	 * @return bool
	 */
	public function isAjax()
	{
		return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
	}
}

