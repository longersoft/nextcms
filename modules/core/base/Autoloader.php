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
 * Loads the file based on the class name automatically
 */
class Core_Base_Autoloader extends Zend_Loader_Autoloader_Resource
{
    public function __construct($options)
    {
        parent::__construct($options);
    }
    
    /**
     * @see Zend_Loader_Autoloader_Resource::autoload()
     */
	public function autoload($class)
    {
    	$prefix = APP_ROOT_DIR . DS;
    	$paths  = explode('_', $class);
    	switch (strtolower($paths[0])) {
    		case 'plugins':
    		case 'hooks':
    			$prefix .= '';
    			break;
    		default:
    			$prefix .= 'modules' . DS;
    			break;
    	}
    	    	
		$className = $paths[count($paths) - 1];
		$classFile = substr($class, 0, -strlen($className));
		$classFile = $prefix . strtolower(str_replace('_', DS, $classFile)) . $className . '.php';
		
		if (file_exists($classFile)) {
			return require_once $classFile;
		}
    	
        return false;
    }
}
