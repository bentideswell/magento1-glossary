<?php
/**
 * @category    Fishpig
 * @package     Fishpig_Glossary
 * @license     http://fishpig.co.uk/license.txt
 * @author      Ben Tideswell <help@fishpig.co.uk>
 */

class Fishpig_Glossary_Model_Word extends Mage_Core_Model_Abstract
{
	/**
	 * `is_enabled` values
	 *
	 * @var const int
	 */
	const STATUS_DISABLED = 0;
	const STATUS_ENABLED = 1;
	
	/**
	 * Setup the model's resource
	 *
	 * @return void
	 */
	public function _construct()
	{
		$this->_init('glossary/word');
	}
	
	/**
	 * Retrieve the word
	 *
	 * @return string
	 */
	public function getWord()
	{
		return $this->getName();
	}

	/**
	 * Retrieve the definition
	 *
	 * @return string
	 */
	public function getDefinition()
	{
		if ($this->_getData('definition')) {
			return Mage::helper('cms')->getBlockTemplateProcessor()->filter($this->_getData('definition'));
		}
		
		return $this->getShortDefinition();
	}
	
	/**
	 * Retrieve the Meta definition.
	 * If empty, use the short definition
	 *
	 * @return string
	 */
	public function getMetaDefinition()
	{
		return strip_tags($this->getShortDefinition());
	}
		
	/**
	 * Retrieve the store ID of the splash word
	 * This isn't always the only store it's associated with
	 * but the current store ID
	 *
	 * @return int
	 */
	public function getStoreId()
	{
		if (!$this->hasStoreId()) {
			return (int)Mage::app()->getStore(true)->getId();
		}

		return (int)$this->_getData('store_id');
	}
	
	/**
	 * Determine whether object has a store ID of 0
	 *
	 * @return bool
	 */
	public function isGlobal()
	{
		if ($storeIds = $this->getStoreIds()) {
			foreach($storeIds as $storeId) {
				if ((int)$storeId === 0)	{
					return true;
				}
			}
		}
		
		return false;
	}
	
	/**
	 * Retrieve the URL for the splash word
	 * If cannot find rewrite, return system URL
	 *
	 * @return string
	 */
	public function getUrl()
	{
		if (!$this->hasUrl()) {
			$helper = Mage::helper('glossary');

			$url = Mage::getUrl('', array(
				'_direct' => $helper->getFrontName() . '/' . $this->getUrlKey() . $helper->getUrlSuffix(),
				'_secure' 	=> false,
				'_nosid' 	=> true,
				'_store' => $this->getStoreId(),
			));

			$this->setUrl($url);
		}
		
		return $this->_getData('url');
	}
	
	/**
	 * Determine whether the Word is enabled
	 *
	 * @return bool
	 */
	public function isEnabled()
	{
		return $this->getId() && (int)$this->getIsEnabled() === 1;
	}
	
	/**
	 * Retrieve the first character of the word
	 *
	 * @return string
	 */
	/**
	 * Retrieve the first character of the word
	 *
	 * @return string
	 */
	public function getFirstCharacter()
	{
		if (!$this->hasFirstCharacter()) {
			$originalEncoding = mb_internal_encoding();
			
			mb_internal_encoding('UTF-8');

			$char = mb_substr(trim($this->getName()), 0, 1);

			if ($charMb = $this->convertMbString($char)) {
				$this->setFirstCharacter(mb_strtoupper($char));
			}
			else if (preg_match('/([a-zA-Z0-9]{1})/', $this->getName(), $match)) {
				$firstCharacter = strtoupper($match[1]);

				if (is_numeric($firstCharacter)) {
					$firstCharacter = '#';
				}

				$this->setFirstCharacter(strtoupper($firstCharacter));
			}
			
			mb_internal_encoding($originalEncoding);
		}

		return $this->_getData('first_character');		
	}

	
	/**
	 * Convert a string
	 *
	 * @param string $char
	 * @return false|string
	 **/
	public function convertMbString($char)
	{
	    $table = array(
	    	'á' => 'a',
	    	 'Á' => 'A',
	    	 'à' => 'a',
	    	 'À' => 'A',
	    	 'ă' => 'a',
	    	 'Ă' => 'A',
	    	 'â' => 'a',
	    	 'Â' => 'A',
	    	 'å' => 'a',
	    	 'Å' => 'A',
	    	 'ã' => 'a',
	    	 'Ã' => 'A',
	    	 'ą' => 'a',
	    	 'Ą' => 'A',
	    	 'ā' => 'a',
	    	 'Ā' => 'A',
	    	 'ä' => 'ae',
	    	 'Ä' => 'AE',
	    	 'æ' => 'ae',
	    	 'Æ' => 'AE',
	    	 'ḃ' => 'b',
	    	 'Ḃ' => 'B',
	    	 'ć' => 'c',
	    	 'Ć' => 'C',
	    	 'ĉ' => 'c',
	    	 'Ĉ' => 'C',
	    	 'č' => 'c',
	    	 'Č' => 'C',
	    	 'ċ' => 'c',
	    	 'Ċ' => 'C',
	    	 'ç' => 'c',
	    	 'Ç' => 'C',
	    	 'ď' => 'd',
	    	 'Ď' => 'D',
	    	 'ḋ' => 'd',
	    	 'Ḋ' => 'D',
	    	 'đ' => 'd',
	    	 'Đ' => 'D',
	    	 'ð' => 'dh',
	    	 'Ð' => 'Dh',
	    	 'é' => 'e',
	    	 'É' => 'E',
	    	 'è' => 'e',
	    	 'È' => 'E',
	    	 'ĕ' => 'e',
	    	 'Ĕ' => 'E',
	    	 'ê' => 'e',
	    	 'Ê' => 'E',
	    	 'ě' => 'e',
	    	 'Ě' => 'E',
	    	 'ë' => 'e',
	    	 'Ë' => 'E',
	    	 'ė' => 'e',
	    	 'Ė' => 'E',
	    	 'ę' => 'e',
	    	 'Ę' => 'E',
	    	 'ē' => 'e',
	    	 'Ē' => 'E',
	    	 'ḟ' => 'f',
	    	 'Ḟ' => 'F',
	    	 'ƒ' => 'f',
	    	 'Ƒ' => 'F',
	    	 'ğ' => 'g',
	    	 'Ğ' => 'G',
	    	 'ĝ' => 'g',
	    	 'Ĝ' => 'G',
	    	 'ġ' => 'g',
	    	 'Ġ' => 'G',
	    	 'ģ' => 'g',
	    	 'Ģ' => 'G',
	    	 'ĥ' => 'h',
	    	 'Ĥ' => 'H',
	    	 'ħ' => 'h',
	    	 'Ħ' => 'H',
	    	 'í' => 'i',
	    	 'Í' => 'I',
	    	 'ì' => 'i',
	    	 'Ì' => 'I',
	    	 'î' => 'i',
	    	 'Î' => 'I',
	    	 'ï' => 'i',
	    	 'Ï' => 'I',
	    	 'ĩ' => 'i',
	    	 'Ĩ' => 'I',
	    	 'į' => 'i',
	    	 'Į' => 'I',
	    	 'ī' => 'i',
	    	 'Ī' => 'I',
	    	 'ĵ' => 'j',
	    	 'Ĵ' => 'J',
	    	 'ķ' => 'k',
	    	 'Ķ' => 'K',
	    	 'ĺ' => 'l',
	    	 'Ĺ' => 'L',
	    	 'ľ' => 'l',
	    	 'Ľ' => 'L',
	    	 'ļ' => 'l',
	    	 'Ļ' => 'L',
	    	 'ł' => 'l',
	    	 'Ł' => 'L',
	    	 'ṁ' => 'm',
	    	 'Ṁ' => 'M',
	    	 'ń' => 'n',
	    	 'Ń' => 'N',
	    	 'ň' => 'n',
	    	 'Ň' => 'N',
	    	 'ñ' => 'n',
	    	 'Ñ' => 'N',
	    	 'ņ' => 'n',
	    	 'Ņ' => 'N',
	    	 'ó' => 'o',
	    	 'Ó' => 'O',
	    	 'ò' => 'o',
	    	 'Ò' => 'O',
	    	 'ô' => 'o',
	    	 'Ô' => 'O',
	    	 'ő' => 'o',
	    	 'Ő' => 'O',
	    	 'õ' => 'o',
	    	 'Õ' => 'O',
	    	 'ø' => 'oe',
	    	 'Ø' => 'OE',
	    	 'ō' => 'o',
	    	 'Ō' => 'O',
	    	 'ơ' => 'o',
	    	 'Ơ' => 'O',
	    	 'ö' => 'oe',
	    	 'Ö' => 'OE',
	    	 'ṗ' => 'p',
	    	 'Ṗ' => 'P',
	    	 'ŕ' => 'r',
	    	 'Ŕ' => 'R',
	    	 'ř' => 'r',
	    	 'Ř' => 'R',
	    	 'ŗ' => 'r',
	    	 'Ŗ' => 'R',
	    	 'ś' => 's',
	    	 'Ś' => 'S',
	    	 'ŝ' => 's',
	    	 'Ŝ' => 'S',
	    	 'š' => 's',
	    	 'Š' => 'S',
	    	 'ṡ' => 's',
	    	 'Ṡ' => 'S',
	    	 'ş' => 's',
	    	 'Ş' => 'S',
	    	 'ș' => 's',
	    	 'Ș' => 'S',
	    	 'ß' => 'SS',
	    	 'ť' => 't',
	    	 'Ť' => 'T',
	    	 'ṫ' => 't',
	    	 'Ṫ' => 'T',
	    	 'ţ' => 't',
	    	 'Ţ' => 'T',
	    	 'ț' => 't',
	    	 'Ț' => 'T',
	    	 'ŧ' => 't',
	    	 'Ŧ' => 'T',
	    	 'ú' => 'u',
	    	 'Ú' => 'U',
	    	 'ù' => 'u',
	    	 'Ù' => 'U',
	    	 'ŭ' => 'u',
	    	 'Ŭ' => 'U',
	    	 'û' => 'u',
	    	 'Û' => 'U',
	    	 'ů' => 'u',
	    	 'Ů' => 'U',
	    	 'ű' => 'u',
	    	 'Ű' => 'U',
	    	 'ũ' => 'u',
	    	 'Ũ' => 'U',
	    	 'ų' => 'u',
	    	 'Ų' => 'U',
	    	 'ū' => 'u',
	    	 'Ū' => 'U',
	    	 'ư' => 'u',
	    	 'Ư' => 'U',
	    	 'ü' => 'ue',
	    	 'Ü' => 'UE',
	    	 'ẃ' => 'w',
	    	 'Ẃ' => 'W',
	    	 'ẁ' => 'w',
	    	 'Ẁ' => 'W',
	    	 'ŵ' => 'w',
	    	 'Ŵ' => 'W',
	    	 'ẅ' => 'w',
	    	 'Ẅ' => 'W',
	    	 'ý' => 'y',
	    	 'Ý' => 'Y',
	    	 'ỳ' => 'y',
	    	 'Ỳ' => 'Y',
	    	 'ŷ' => 'y',
	    	 'Ŷ' => 'Y',
	    	 'ÿ' => 'y',
	    	 'Ÿ' => 'Y',
	    	 'ź' => 'z',
	    	 'Ź' => 'Z',
	    	 'ž' => 'z',
	    	 'Ž' => 'Z',
	    	 'ż' => 'z',
	    	 'Ż' => 'Z',
	    	 'þ' => 'th',
	    	 'Þ' => 'Th',
	    	 'µ' => 'u',
	    	 'а' => 'a',
	    	 'А' => 'a',
	    	 'б' => 'b',
	    	 'Б' => 'b',
	    	 'в' => 'v',
	    	 'В' => 'v',
	    	 'г' => 'g',
	    	 'Г' => 'g',
	    	 'д' => 'd',
	    	 'Д' => 'd',
	    	 'е' => 'e',
	    	 'Е' => 'E',
	    	 'ё' => 'e',
	    	 'Ё' => 'E',
	    	 'ж' => 'zh',
	    	 'Ж' => 'zh',
	    	 'з' => 'z',
	    	 'З' => 'z',
	    	 'и' => 'i',
	    	 'И' => 'i',
	    	 'й' => 'j',
	    	 'Й' => 'j',
	    	 'к' => 'k',
	    	 'К' => 'k',
	    	 'л' => 'l',
	    	 'Л' => 'l',
	    	 'м' => 'm',
	    	 'М' => 'm',
	    	 'н' => 'n',
	    	 'Н' => 'n',
	    	 'о' => 'o',
	    	 'О' => 'o',
	    	 'п' => 'p',
	    	 'П' => 'p',
	    	 'р' => 'r',
	    	 'Р' => 'r',
	    	 'с' => 's',
	    	 'С' => 's',
	    	 'т' => 't',
	    	 'Т' => 't',
	    	 'у' => 'u',
	    	 'У' => 'u',
	    	 'ф' => 'f',
	    	 'Ф' => 'f',
	    	 'х' => 'h',
	    	 'Х' => 'h',
	    	 'ц' => 'c',
	    	 'Ц' => 'c',
	    	 'ч' => 'ch',
	    	 'Ч' => 'ch',
	    	 'ш' => 'sh',
	    	 'Ш' => 'sh',
	    	 'щ' => 'sch',
	    	 'Щ' => 'sch',
	    	 'ъ' => '',
	    	 'Ъ' => '',
	    	 'ы' => 'y',
	    	 'Ы' => 'y',
	    	 'ь' => '',
	    	 'Ь' => '',
	    	 'э' => 'e',
	    	 'Э' => 'e',
	    	 'ю' => 'ju',
	    	 'Ю' => 'ju',
	    	 'я' => 'ja',
	    	 'Я' => 'ja'
	    );

		foreach($table as $k => $v) {
			$k = mb_substr($k, 0, 1);

			if ($k === $char) {
				if (strlen($v) > 1) {
					return substr($v, 0, 1);
				}
				
				return $v;
			}
		}
		
		return false;
	}
}
