<?php
/**
 * @category    Fishpig
 * @package     Fishpig_Glossary
 * @license     http://fishpig.co.uk/license.txt
 * @author      Ben Tideswell <help@fishpig.co.uk>
 */

class Fishpig_Glossary_Adminhtml_Glossary_WordController extends Mage_Adminhtml_Controller_Action
{
	/**
	 * Forward the request to the dashboard
	 *
	 * @return $this
	 */
	public function indexAction()
	{
		return $this->_redirect('*/glossary');
	}

	/**
	 * Add a new splash word
	 *
	 * @return $this
	 */
	public function newAction()
	{
		return $this->_forward('edit');
	}
		
	/**
	 * Display the add/edit form for the splash word
	 *
	 */
	public function editAction()
	{
		$object = $this->_initWord();
		
		$this->loadLayout();
		$this->_setActiveMenu('cms/glossary');
		
		$this->_title('FishPig');
		$this->_title('Glossary');
		$this->_title($this->__('Word'));
		
		if ($object) {
			$this->_title($object->getName());
		}
		
		$this->renderLayout();
	}
	
	/**
	 * Save the posted data
	 *
	 */
	public function saveAction()
	{
		if ($data = $this->getRequest()->getPost('word')) {
			try {
				$word = Mage::getModel('glossary/word')
					->setData($data)
					->setId($this->getRequest()->getParam('id'));
					
				$word->save();

				$this->_getSession()->addSuccess($this->__('The word was saved'));
			}
			catch (Exception $e) {
				$this->_getSession()->addError($this->__($e->getMessage()));
			}
				
			if ($word->getId() && $this->getRequest()->getParam('back', false)) {
				return $this->_redirect('*/*/edit', array('id' => $word->getId()));
			}
		}
		else {
			$this->_getSession()->addError($this->__('There was no data to save.'));
		}

		return $this->_redirect('*/glossary');
	}

	/**
	 * Delete a splash word
	 *
	 */
	public function deleteAction()
	{
		if ($wordId = $this->getRequest()->getParam('id')) {
			$object = Mage::getModel('glossary/word')->load($wordId);
			
			if ($object->getId()) {
				try {
					$object->delete();

					$this->_getSession()->addSuccess($this->__('The word was deleted.'));
				}
				catch (Exception $e) {
					$this->_getSession()->addError($e->getMessage());
				}
			}
		}
		
		$this->_redirect('*/glossary');
	}
	
	public function massDeleteAction()
	{
		$objectIds = $this->getRequest()->getParam('word');

		if (!is_array($objectIds)) {
			$this->_getSession()->addError($this->__('Please select word(s).'));
		}
		else {
			if (!empty($objectIds)) {
				try {
					foreach ($objectIds as $objectId) {
						Mage::getSingleton('glossary/word')->load($objectId)->delete();
					}
					
					$this->_getSession()->addSuccess($this->__('Total of %d record(s) have been deleted.', count($objectIds)));
				}
				catch (Exception $e) {
					$this->_getSession()->addError($e->getMessage());
				}
			}
		}
		
		$this->_redirect('*/glossary');
	}
	
	/**
	 * Initialise the splash word model
	 *
	 * @return false|Fishpig_Glossary_Model_Word
	 */
	protected function _initWord()
	{
		if (($object = Mage::registry('glossary_word')) !== null) {
			return $object;
		}

		if ($id = $this->getRequest()->getParam('id')) {
			$object = Mage::getModel('glossary/word')->load($id);
			
			if ($object->getId()) {
				Mage::register('glossary_word', $object);

				return $object;
			}
		}
		
		return false;
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
