<?php
/**
 * @category    Fishpig
 * @package     Fishpig_Glossary
 * @license     http://fishpig.co.uk/license.txt
 * @author      Ben Tideswell <help@fishpig.co.uk>
 */

	$file = Mage::getBaseDir() . DS . implode(DS, array('app', 'code', 'core', 'Mage', 'Core', 'Model', 'Resource', 'Db', 'Abstract.php'));
	
	if (is_file($file)) {
		abstract class Fishpig_Glossary_Model_Resource_Word_Collection_Hack extends Mage_Core_Model_Resource_Db_Collection_Abstract {}
	}
	else {
		abstract class Fishpig_Glossary_Model_Resource_Word_Collection_Hack extends Mage_Core_Model_Mysql4_Collection_Abstract {}
	}

class Fishpig_Glossary_Model_Resource_Word_Collection extends Fishpig_Glossary_Model_Resource_Word_Collection_Hack
{
	/**
	 * Setup the collection model
	 *
	 * @return $this
	 */
	public function _construct()
	{
		$this->_init('glossary/word');

		$this->_map['fields']['word_id'] = 'main_table.word_id';
		$this->_map['fields']['store'] = 'store_table.store_id';
		
		return parent::_construct();
	}

	/**
	 * Init collection select
	 *
	 * @return Mage_Core_Model_Resource_Collection_Abstract
	*/
	protected function _initSelect()
	{
		$this->getSelect()->from(array('main_table' => $this->getResource()->getMainTable()));
		
		$idFieldName = $this->getResource()->getIdFieldName();

		$this->getSelect()->join(
			array('store_table' => $this->getResource()->getStoreTable()),
			'main_table.' . $idFieldName . ' = store_table.' . $idFieldName,
			array()
		)
		->order('store_table.store_id DESC')
		->order('main_table.first_character ASC')
		->order('main_table.name ASC')
		->group('main_table.word_id');

		return $this->getSelect();	
	}

	/**
	 * Add filter by store
	 *
	 * @param int|Mage_Core_Model_Store $store
	 * @param bool $withAdmin
	 * @return Mage_Cms_Model_Resource_Word_Collection
	*/
	public function addStoreFilter($store, $withAdmin = true)
	{
		if ($store instanceof Mage_Core_Model_Store) {
			$store = array($store->getId());
		}

		if (!is_array($store)) {
			$store = array($store);
		}

		if ($withAdmin) {
			$store[] = Mage_Core_Model_App::ADMIN_STORE_ID;
		}

		$this->addFieldtoFilter('store_id', array('in' => $store));

		$this->_select = $this->getConnection()
			->select()
			->from(array('main_table' => new Zend_Db_Expr('(' . str_replace('main_table', 'main_table_tmp', (string)$this->_select) . ')')))
			->group('main_table.name');

		return $this;
	}
	
	/**
	 * Filter the collection so only enabled words are returned
	 *
	 * @param int $value = 1
	 * @return $this
	 */
	public function addIsEnabledFilter($value = 1)
	{
		return $this->addFieldToFilter('is_enabled', $value);
	}
}
