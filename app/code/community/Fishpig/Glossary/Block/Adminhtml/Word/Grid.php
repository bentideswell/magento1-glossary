<?php
/**
 * @category    Fishpig
 * @package     Fishpig_Glossary
 * @license     http://fishpig.co.uk/license.txt
 * @author      Ben Tideswell <help@fishpig.co.uk>
 */

class Fishpig_Glossary_Block_Adminhtml_Word_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
	public function __construct()
	{
		parent::__construct();
		
		$this->setId('glossary_word_grid');
		$this->setDefaultSort('first_character');
		$this->setDefaultDir('asc');
		$this->setSaveParametersInSession(false);
		$this->setUseAjax(true);
	}

	/**
	 * Insert the Add New button
	 *
	 * @return $this
	 */
	protected function _prepareLayout()
	{
		$this->setChild('add_button',
			$this->getLayout()->createBlock('adminhtml/widget_button')
				->setData(array(
					'label'     => Mage::helper('adminhtml')->__('Add New'),
					'class' => 'add',
					'onclick'   => "setLocation('" . $this->getUrl('*/glossary_word/new') . "');",
				))
		);
				
		return parent::_prepareLayout();
	}
	
	/**
	 * Retrieve the main buttons html
	 *
	 * @return string
	 */
	public function getMainButtonsHtml()
	{
		return parent::getMainButtonsHtml() . $this->getChildHtml('add_button');
	}

	/**
	 * Initialise and set the collection for the grid
	 *
	 */
	protected function _prepareCollection()
	{
		$this->setCollection(
			Mage::getResourceModel('glossary/word_collection')
		);
	
		return parent::_prepareCollection();
	}

	/**
	 * Add store information to words
	 *
	 * @return $this
	 */
	protected function _afterLoadCollection()
	{
		$this->getCollection()->walk('afterLoad');

		parent::_afterLoadCollection();
	}
	
	/**
	 * Apply the store filter
	 *
	 * @param $collection
	 * @param $column
	 * @return void
	 */
    protected function _filterStoreCondition($collection, $column)
    {
        if (!$value = $column->getFilter()->getValue()) {
            return;
        }

        $this->getCollection()->addStoreFilter($value);
    }
    	
	/**
	 * Add the columns to the grid
	 *
	 */
	protected function _prepareColumns()
	{
		$this->addColumn('word_id', array(
			'header'	=> $this->__('ID'),
			'align'		=> 'right',
			'width'     => 1,
			'index'		=> 'word_id',
		));
		
		$this->addColumn('first_character', array(
			'header'	=> $this->__('First Character'),
			'align'		=> 'left',
			'index'		=> 'first_character',
			'width'		=> 1,
		));
		
		$this->addColumn('name', array(
			'header'	=> $this->__('Word'),
			'align'		=> 'left',
			'index'		=> 'name',
		));

		if (!Mage::app()->isSingleStoreMode()) {
			$this->addColumn('store_ids', array(
				'header'	=> $this->__('Store'),
				'align'		=> 'left',
				'index'		=> 'store_ids',
                'type'          => 'store',
                'store_all'     => true,
                'store_view'    => true,
                'sortable'      => false,
                'filter_condition_callback' => array($this, '_filterStoreCondition'),
				'options' 	=> $this->getStores(),
			));
		}
		
		$this->addColumn('is_enabled', array(
			'width'     => 1,
			'header'	=> $this->__('Status'),
			'index'		=> 'is_enabled',
			'type'		=> 'options',
			'options'	=> array(
				Fishpig_Glossary_Model_Word::STATUS_ENABLED => $this->__('Enabled'),
				Fishpig_Glossary_Model_Word::STATUS_DISABLED => $this->__('Disabled'),
			),
		));
	
		$this->addColumn('action', array(
			'type'      => 'action',
			'getter'     => 'getId',
			'actions'   => array(array(
				'caption' => Mage::helper('catalog')->__('Edit'),
				'url'     => array(
				'base'=>'*/glossary_word/edit',
				),
				'field'   => 'id'
			)),
			'filter'    => false,
			'sortable'  => false,
			'align' 	=> 'center',
		));

		return parent::_prepareColumns();
	}

	protected function _prepareMassaction()
	{
		$this->setMassactionIdField('word_id');
		$this->getMassactionBlock()->setFormFieldName('word');
	
		$this->getMassactionBlock()->addItem('delete', array(
			'label'=> $this->__('Delete'),
			'url'  => $this->getUrl('*/glossary_word/massDelete'),
			'confirm' => Mage::helper('catalog')->__('Are you sure?')
		));
	}
	
	/**
	 * Retrieve the URL used to modify the grid via AJAX
	 *
	 * @return string
	 */
	public function getGridUrl()
	{
		return $this->getUrl('*/*/wordGrid');
	}
	
	/**
	 * Retrieve the URL for the row
	 *
	 */
	public function getRowUrl($row)
	{
		return $this->getUrl('*/glossary_word/edit', array('id' => $row->getId()));
	}
	
	/**
	 * Retrieve an array of all of the stores
	 *
	 * @return array
	 */
	protected function getStores()
	{
		$options = array(0 => $this->__('Global'));
		$stores = Mage::getResourceModel('core/store_collection')->load();
		
		foreach($stores as $store) {
			$options[$store->getId()] = $store->getWebsite()->getName() . ' &gt; ' . $store->getName();
		}

		return $options;
	}
}
