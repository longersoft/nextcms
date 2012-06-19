<?php
/**
 * NextCMS
 * 
 * @author		Nguyen Huu Phuoc <thenextcms@gmail.com>
 * @copyright	Copyright (c) 2011 - 2012, Nguyen Huu Phuoc
 * @license		http://nextcms.org/license.txt	(GNU GPL version 2 or later)
 * @link		http://nextcms.org
 * @category	modules
 * @package		core
 * @subpackage	base
 * @since		1.0
 * @version		2011-10-18
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

/**
 * This adapter allows to store the translated items in JSON file
 */
class Core_Base_Translate_Adapters_Json extends Zend_Translate_Adapter
{
	/**
	 * @see Zend_Translate_Adapter::_loadTranslationData()
	 */
	protected function _loadTranslationData($filename, $locale, array $options = array())
	{
		if (!file_exists($filename)) {
			require_once 'Zend/Translate/Exception.php';
			throw new Zend_Translate_Exception("JSON file '" . $filename . "' not found");
		}

		$data = file_get_contents($filename);
		try {
			$data = Zend_Json::decode($data);
		} catch (Exception $ex) {
			throw new Zend_Translate_Exception("The '" . $filename . "' file is not valid JSON file");
		}
		$dataArray = $this->_getTranslationArray($data);

		// Merge data back to the adapter
		$this->_translate[$locale] = array_merge($this->_translate[$locale], $dataArray);
	}

	/**
	 * @see Zend_Translate_Adapter::toString()
	 */
	public function toString()
	{
		return "Json";
	}

	/**
	 * Gets array of translation items.
	 * For example, if the array is
	 * 		array(
	 * 			'section1' => array(
	 * 				'subSection' => array(
	 * 					'subOfSub' => 'anyString',
	 * 				)
	 * 			),
	 * 			'section2' => 'otherString',
	 * 		)
	 *
	 * then the method will return the following array:
	 * 		array(
	 * 			'section1' 					   => 'section1',	// The value of item will be exactly the key if the item is array in the input
	 * 			'section1.subSection' 		   => 'section1.subSection',
	 * 			'section1.subSection.subOfSub' => 'anyString',
	 * 			'section2' 					   => 'otherString',
	 * 		)
	 * 
	 * @param array $data
	 * @return array
	 */
	private function _getTranslationArray($data = array())
	{
		$dataArray = array();
		foreach ($data as $key => $value) {
			if (!is_array($value)) {
				$dataArray[$key] = $value;
			} else {
				$dataArray[$key] = $key;
				$dataArray = $this->_processKey($dataArray, $key, $value, $key);
			}
		}
		return $dataArray;
	}

	/**
	 * Process each key
	 * 
	 * @param array $data
	 * @param string $keyName
	 * @param mixed $keyData
	 * @param string $prefix
	 * @return array
	 */
	private function _processKey($data, $keyName, $keyData, $prefix)
	{
		foreach ($keyData as $k => $v) {
			if (!is_array($v)) {
				$data[$prefix . '.' . $k] = $v;
			} else {
				$data = array_merge($data, $this->_processKey($data, $k, $v, $prefix . '.' . $k));
			}
		}
		return $data;
	}
}
