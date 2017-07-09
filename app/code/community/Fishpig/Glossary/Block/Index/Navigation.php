<?php
/**
 * @category    Fishpig
 * @package     Fishpig_Glossary
 * @license     http://fishpig.co.uk/license.txt
 * @author      Ben Tideswell <help@fishpig.co.uk>
 */

class Fishpig_Glossary_Block_Index_Navigation extends Mage_Core_Block_Template
{
	/**
	 * Set the words and generate the letters list
	 *
	 * @param Fishpig_Glossary_Model_Resource_Word_Collection $words
	 * @return $this
	 */
	public function setWords(Fishpig_Glossary_Model_Resource_Word_Collection $words)
	{
		parent::setWords($words);
		
		$letters = array_flip(range('A', 'Z'));
		$used = array();
		
		foreach($words as $word) {
			$used[$word->getFirstCharacter()] = true;
		}
		
		if ($used && count(array_intersect(array_flip($letters), array_flip(array_keys($used)))) === 0) {
			// Must be a different language
			$letters = $used;
		}

		$this->setLetters($letters);
		
		return $this;
	}
}
