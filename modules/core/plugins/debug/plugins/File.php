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
 * @subpackage	plugins
 * @since		1.0
 * @version		2011-10-18
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

class Core_Plugins_Debug_Plugins_File implements Core_Plugins_Debug_Plugins_Interface
{
	/**
	 * Included files
	 * 
	 * @var array
	 */
	protected $_includedFiles = null;
	
	/**
	 * Included paths
	 * 
	 * @var array
	 */
	protected $_includedPaths = null;
	
	/**
	 * @see Core_Plugins_Debug_Plugins_Interface::getData()
	 */
	public function getData()
	{
		$files = $this->_getIncludedFiles();
		$paths = $this->_getIncludedPaths();
		$size  = 0;
		$includedFiles = array(
			'app' => array(),	// Array of included files from the app
			'lib' => array(),	// Array of included files from the included paths
		);
		
		foreach ($files as $file) {
			$size += filesize($file);
			if (substr($file, 0, strlen(APP_ROOT_DIR)) == APP_ROOT_DIR) {
				$includedFiles['app'][] = ltrim(substr($file, strlen(APP_ROOT_DIR)), DS);
			}
			foreach ($paths as $path) {
				if ($path == '' || $path == '.') {
					continue;
				}
				if (substr($file, 0, strlen($path)) == $path) {
					$f = substr($file, strlen($path));
					
					if (!isset($includedFiles['lib'][$path])) {
						$includedFiles['lib'][$path] = array();
					}
					
					if (!in_array($f, $includedFiles['lib'][$path])) {
						$includedFiles['lib'][$path][] = ltrim($f, DS);
					}
				}
			}
		}
		
		return array(
			'files'	=> $includedFiles,
			'total' => count($files),
			'size'	=> $size,
			'paths'	=> $this->_getIncludedPaths(),
		);
	}
	
	/**
	 * Gets included files
	 * 
	 * @return array
	 */
	protected function _getIncludedFiles()
    {
        if ($this->_includedFiles != null) {
            return $this->_includedFiles;
        }

        $this->_includedFiles = get_included_files();
        sort($this->_includedFiles);
        return $this->_includedFiles;
    }
    
	/**
	 * Gets included paths
	 * 
	 * @return array
	 */
	protected function _getIncludedPaths()
    {
        if ($this->_includedPaths != null) {
            return $this->_includedPaths;
        }
        
        $path = get_include_path();
        $this->_includedPaths = ($path == '') ? array() : explode(PS, $path);
        return $this->_includedPaths;
    }
}
