<?php
/**
 * @category    Fishpig
 * @package     Fishpig_Glossary
 * @license     http://fishpig.co.uk/license.txt
 * @author      Ben Tideswell <help@fishpig.co.uk>
 */

class Fishpig_Glossary_Block_Adminhtml_Word extends Mage_Adminhtml_Block_Widget_Grid_Container
{
	public function __construct()
	{	
		parent::__construct();
		
		$this->_controller = 'adminhtml_word';
		$this->_blockGroup = 'glossary';
		$this->_headerText = 'Glossary: ' . $this->__('Words');

		$this->_removeButton('add');
	}
}