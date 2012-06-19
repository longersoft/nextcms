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
 * @subpackage	controllers
 * @since		1.0
 * @version		2012-02-05
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

class Core_TemplateController extends Zend_Controller_Action
{
	/**
	 * Array of special templates which cannot be activated
	 * 
	 * @var array
	 */
	public static $NOT_ACTIVABLE_TEMPLATES = array(
		'admin',		// The back-end template
	);
	
	/**
	 * Inits controller
	 * 
	 * @see Zend_Controller_Action::init()
	 * @return void
	 */
	public function init()
	{
		$ajaxContext = $this->_helper->getHelper('AjaxContext');
		$ajaxContext->addActionContext('list', 'html')
					->initContext();
	}
	
	////////// BACKEND ACTIONS //////////

	/**
	 * Activates the template
	 * 
	 * @return void
	 */
	public function activateAction()
	{
		Core_Services_Db::connect('master');
		
		$request  = $this->getRequest();
		$template = $request->getParam('template');
		$result	  = false; 
		
		if ($template && !in_array($template, self::$NOT_ACTIVABLE_TEMPLATES)) {
			Core_Services_Config::set('core', 'template', $template);
			$result = true;
		}
		$this->_helper->json(array(
			'result'   => $result ? 'APP_RESULT_OK' : 'APP_RESULT_ERROR',
			'template' => $template,
		));
	}
	
	/**
	 * Installs a template
	 * 
	 * @return void
	 */
	public function installAction()
	{
		Core_Services_Db::connect('master');
		
		$request  = $this->getRequest();
		$template = $request->getParam('template');
		$result   = Core_Services_Template::install($template);
		
		$this->_helper->json(array(
			'result' => $result ? 'APP_RESULT_OK' : 'APP_RESULT_ERROR',
		));
	}
	
	/**
	 * Lists templates
	 * 
	 * @return void
	 */
	public function listAction()
	{
		Core_Services_Db::connect('master');
		
		$request = $this->getRequest();
		$format	 = $request->getParam('format');
		
		switch ($format) {
			case 'html':
				$this->view->assign('template', $request->getParam('template'));
				break;
			default:
				$this->view->assign(array(
					'notActivableTemplates' => self::$NOT_ACTIVABLE_TEMPLATES,
					// Array of available templates
					'templates'				=> Core_Base_File::getSubDirectories(APP_ROOT_DIR . DS . 'templates'),
					// The current template
					'currentTemplate'		=> Core_Services_Config::get('core', 'template', 'default'),		
				));
				break;
		}
	}
	
	/**
	 * Uninstalls a template
	 * 
	 * @return void
	 */
	public function uninstallAction()
	{
		Core_Services_Db::connect('master');
		
		$request  = $this->getRequest();
		$template = $request->getParam('template');
		$result   = Core_Services_Template::uninstall($template);
		
		$this->_helper->json(array(
			'result' => $result ? 'APP_RESULT_OK' : 'APP_RESULT_ERROR',
		));
	}
}
