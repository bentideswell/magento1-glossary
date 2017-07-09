<?php
/**
 * @category    Fishpig
 * @package     Fishpig_Glossary
 * @license     http://fishpig.co.uk/license.txt
 * @author      Ben Tideswell <help@fishpig.co.uk>
 */

	$file = Mage::getBaseDir() . DS . implode(DS, array('app', 'code', 'core', 'Mage', 'Core', 'Model', 'Resource', 'Db', 'Abstract.php'));
	
	if (is_file($file)) {
		abstract class Fishpig_Glossary_Model_Resource_Word_Hack extends Mage_Core_Model_Resource_Db_Abstract {}
	}
	else {
		abstract class Fishpig_Glossary_Model_Resource_Word_Hack extends Mage_Core_Model_Mysql4_Abstract {}
	}

class Fishpig_Glossary_Model_Resource_Word extends Fishpig_Glossary_Model_Resource_Word_Hack
{
	public function _construct()
	{
		$this->_init('glossary/word', 'word_id');
	}

	/**
	 * Retrieve select object for load object data
	 * This gets the default select, plus the attribute id and code
	 *
	 * @param   string $field
	 * @param   mixed $value
	 * @return  Zend_Db_Select
	*/
	protected function _getLoadSelect($field, $value, $object)
	{
		$select = $this->_getReadAdapter()->select()
			->from(array('main_table' => $this->getMainTable()))
			->where("`main_table`.`{$field}` = ?", $value)
			->limit(1);
			
		$adminId = Mage_Core_Model_App::ADMIN_STORE_ID;
		
		$storeId = $object->getStoreId();
		
		if ($storeId !== $adminId) {
			$cond = $this->_getReadAdapter()->quoteInto(
				'`store`.`' . $this->getIdFieldName() . '` = `main_table`.`' . $this->getIdFieldName() . '` AND `store`.`store_id` IN (?)', array($adminId, $storeId)
			);
			
			$select->join(array('store' => $this->getStoreTable()), $cond, '')
				->order('store.store_id DESC');
		}

		return $select;
	}

	/**
	 * Get store ids to which specified item is assigned
	 *
	 * @param int $id
	 * @return array
	*/
	public function lookupStoreIds($objectId)
	{
		$select = $this->_getReadAdapter()->select()
			->from($this->getStoreTable(), 'store_id')
			->where($this->getIdFieldName() . ' = ?', (int)$objectId);
	
		return $this->_getReadAdapter()->fetchCol($select);
	}
	
	/**
	 * Determine whether the current store is the Admin store
	 *
	 * @return bool
	 */
	public function isAdmin()
	{
		return (int)Mage::app()->getStore()->getId() === Mage_Core_Model_App::ADMIN_STORE_ID;
	}
		
	/**
	 * Set required fields before saving model
	 *
	 * @param Mage_Core_Model_Abstract $object
	 * @return $this
	 */
	protected function _beforeSave(Mage_Core_Model_Abstract $object)
	{
		if (!$object->getWord()) {
			throw new Exception(Mage::helper('glossary')->__('Glossary object must have a name'));
		}
		
		$object->setData('first_character', $object->getFirstCharacter());

		if (!$object->getData('store_ids')) {
			$object->setData('store_ids', array(
				Mage::app()->getStore(true)->getId()
			));
		}

		if (!$object->getUrlKey()) {
			$object->setUrlKey($object->getWord());
		}
		
		$object->setUrlKey($this->formatUrlKey($object->getUrlKey()));
		
		$object->setUpdatedAt(now());
		
		if (!$object->getCreatedAt()) {
			$object->setCreatedAt(now());
		}
		
		return parent::_beforeSave($object);
	}

	/**
	 * Set store data after saving model
	 *
	 * @param Mage_Core_Model_Abstract $object
	 * @return $this
	 */	
	protected function _afterSave(Mage_Core_Model_Abstract $object)
	{
		if ($object->getId()) {
			if (!$this->_isUniqueForStores($object)) {
				throw new Exception('A word with same the same name or url key has already been created for one or more of the stores you selected.');
			}

			$oldStores = $this->lookupStoreIds($object->getId());
			$newStores = (array)$object->getStoreIds();

			if (empty($newStores)) {
				$newStores = (array)$object->getStoreId();
			}
	
			$table  = $this->getStoreTable();
			$insert = array_diff($newStores, $oldStores);
			$delete = array_diff($oldStores, $newStores);
			
			if ($delete) {
				$this->_getWriteAdapter()->delete($table, array($this->getIdFieldName() . ' = ?' => (int) $object->getId(), 'store_id IN (?)' => $delete));
			}
			
			if ($insert) {
				$data = array();
			
				foreach ($insert as $storeId) {
					$data[] = array(
						$this->getIdFieldName()  => (int)$object->getId(),
						'store_id' => (int)$storeId
					);
				}

				$this->_getWriteAdapter()->insertMultiple($table, $data);
			}
		}

		return parent::_afterSave($object);
	}

	/**
	 * Determine whether the word is unique for the store combo given
	 *
	 * @param Mage_Core_Model_Abstract $object
	 * @return bool
	 */
	protected function _isUniqueForStores(Mage_Core_Model_Abstract $object)
	{
		$select = $this->_getReadAdapter()
			->select()
				->distinct()
				->from(array('main_table' => $this->getMainTable()), null)
				->where('main_table.name = ? OR main_table.url_key = ?', $object->getName())
				->where('main_table.word_id <> ?', $object->getId())
				->join(
					array('store_table' => $this->getStoreTable()),
					'main_table.word_id = store_table.word_id',
					'store_id'
				)
				->where('store_id IN (?)', (array)$object->getStoreIds())
				->limit(1);

		return $this->_getReadAdapter()->fetchOne($select) === false;
	}
	
	/**
	 * Load store data after loading model
	 *
	 * @param Mage_Core_Model_Abstract $object
	 * @return $this
	 */
	protected function _afterLoad(Mage_Core_Model_Abstract $object)
	{
		if ($object->getId()) {
			$storeIds = $this->lookupStoreIds($object->getId());
			$object->setData('store_ids', $storeIds);			
			
			if (!$this->isAdmin()) {
				$object->setStoreId(Mage::app()->getStore(true)->getId());
			}
		}
		
		return parent::_afterLoad($object);
	}

	/**
	 * Format a string to a valid URL key
	 * Allow a-zA-Z0-9, hyphen and /
	 *
	 * @param string $str
	 * @return string
	 */
	public function formatUrlKey($str)
	{
		$urlKey = str_replace("'", '', $str);
		$urlKey = preg_replace('#[^0-9a-z\/]+#i', '-', Mage::helper('catalog/product_url')->format($urlKey));
		$urlKey = strtolower($urlKey);
		$urlKey = trim($urlKey, '-');
		
		return $urlKey;
	}

	/**
	 * Retrieve the store table name
	 *
	 * @return string
	 */
	public function getStoreTable()
	{
		return $this->getTable('glossary/word_store');
	}
}
