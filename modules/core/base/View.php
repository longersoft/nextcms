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
 * @version		2012-04-29
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

class Core_Base_View extends Zend_View
{
	/**
	 * @see Zend_View_Abstract::_script()
	 */
	protected function _script($name)
    {
    	if (Zend_Layout::getMvcInstance() && 'admin' == Zend_Layout::getMvcInstance()->getLayout()) {
    		return parent::_script($name);
    	}
    	
        if ($this->isLfiProtectionOn() && preg_match('#\.\.[\\\/]#', $name)) {
            $e = new Zend_View_Exception('Requested scripts may not include parent directory traversal ("../", "..\\" notation)');
            $e->setView($this);
            throw $e;
        }
        
        $scripts	= $this->getScriptPaths();
        $numScripts = count($scripts);
        if (0 == $numScripts) {
            $e = new Zend_View_Exception('no view script directory set; unable to determine location for view script');
            $e->setView($this);
            throw $e;
        }

        for ($i = $numScripts - 1; $i >= 0; $i--) {
        	if (is_readable($scripts[$i] . $name)) {
                return $scripts[$i] . $name;
            }
        }

        $e = new Zend_View_Exception("script '$name' not found in path (" . implode(PATH_SEPARATOR, $scripts) . ")");
        $e->setView($this);
        throw $e;
    }
}
