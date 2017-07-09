<?php

class Fishpig_Glossary_Model_System_Config_Source_Autolink_Library
{
	/**
	 * Library flags
	 *
	 * @var const string
	 */
	const LIBRARY_WZ = 'wz_tooltip';
	
	/**
	 * Retrieve an array of options
	 *
	 * @return array
	 */
	public function getOptions()
	{
		return array(
			self::LIBRARY_WZ => 'WZ Tooltip',
		);
	}
	
	/**
	 * Generate an option array of all options
	 *
	 * @return array
	 */
	public function toOptionArray()
	{
		$options = array();
		
		foreach($this->getOptions() as $value => $label) {
			$options[] = array(
				'value' => $value,
				'label' => $label,
			);
		}
		
		return $options;
	}
}