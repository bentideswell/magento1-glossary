<?php
/**
 * @category    Fishpig
 * @package     Fishpig_Glossary
 * @license     http://fishpig.co.uk/license.txt
 * @author      Ben Tideswell <help@fishpig.co.uk>
 */

class Fishpig_Glossary_Block_Adminhtml_Word_Helper_Image extends Varien_Data_Form_Element_Image
{
    /**
     * Prepend the base image URL to the image filename
     *
     * @return null|string
     */
    protected function _getUrl()
    {
        if ($this->getValue() && !is_array($this->getValue())) {
            return Mage::helper('glossary/image')->getImageUrl($this->getValue());
        }
        
        return null;
    }
}