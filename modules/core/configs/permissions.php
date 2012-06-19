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
 * @subpackage	configs
 * @since		1.0
 * @version		2012-04-17
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

// Returns an array that consists of all the permissions's module.
// Each item of array has the following format:
//		'nameOfController' => array(				// nameOfController: It is exactly the name of controller
//			'translationKey' => '...'				// The value for this language key will be used to get the description of controller
//			'description'	 => '...'				// Description of controller
//			'actions'		 => array(				// Defines all the actions of the controllers
//				'nameOfAction' => array(			// nameOfAction: It is exactly the name of action in the controller
//					'translationKey' => '...',
//					'description' 	 => '...',
//				)
//				...
//			)
//		)

/**
 * Define the permissions of the Core module
 * 
 * @return array
 */
return array(
	// Manage access logs
	'accesslog' => array(
		'translationKey' => '_permission.accesslog.description',
		'description'	 => 'Manage access logs',
		'actions'		 => array(
			'delete' => array(
				'translationKey' => '_permission.accesslog.actions.delete',
				'description'	 => 'Delete access log',
			),
			'list' => array(
				'translationKey' => '_permission.accesslog.actions.list',
				'description'	 => 'List access logs',
			),
			'view' => array(
				'translationKey' => '_permission.accesslog.actions.view',
				'description'	 => 'View access log',
			),
		),
	),
	
	// Manage cache
	'cache' => array(
		'translationKey' => '_permission.cache.description',
		'description'	 => 'Manage cache',
		'actions'		 => array(
			'clean' => array(
				'translationKey' => '_permission.cache.actions.clean',
				'description'	 => 'Clean all cached data',
			),
			'page' => array(
				'translationKey' => '_permission.cache.actions.page',
				'description'	 => 'Set page cache lifetime',
			),
			'remove' => array(
				'translationKey' => '_permission.cache.actions.remove',
				'description'	 => 'Remove cache',
			),
		),
	),

	// Config module
	'config' => array(
		'translationKey' => '_permission.config.description',
		'description'	 => 'Configure module',
		'actions'		 => array(
			'config' => array(
				'translationKey' => '_permission.config.actions.config',
				'description'	 => 'Configure module',
			),
		),
	),
	
	// Manage errors
	'error' => array(
		'translationKey' => '_permission.error.description',
		'description'	 => 'Manage errors',
		'actions'		 => array(
			'delete' => array(
				'translationKey' => '_permission.error.actions.delete',
				'description'	 => 'Delete error',
			),
			'list' => array(
				'translationKey' => '_permission.error.actions.list',
				'description'	 => 'List errors',
			),
			'view' => array(
				'translationKey' => '_permission.error.actions.view',
				'description'	 => 'View error',
			),
		),
	),
	
	// Manage hooks
	'hook' => array(
		'translationKey' => '_permission.hook.description',
		'description'	 => 'Manage hooks',
		'actions'		 => array(
			'config' => array(
				'translationKey' => '_permission.hook.actions.config',
				'description'	 => 'Configure hook',
			),
			'install' => array(
				'translationKey' => '_permission.hook.actions.install',
				'description'	 => 'Install hook',
			),
			'list' => array(
				'translationKey' => '_permission.hook.actions.list',
				'description'	 => 'List hooks',
			),
			'uninstall' => array(
				'translationKey' => '_permission.hook.actions.uninstall',
				'description'	 => 'Uninstall hook',
			),
		),
	),
	
	// Manage language files
	'language' => array(
		'translationKey' => '_permission.language.description',
		'description'	 => 'Manage language files',
		'actions'		 => array(
			'add' => array(
				'translationKey' => '_permission.language.actions.add',
				'description'	 => 'Add language item',
			),
			'delete' => array(
				'translationKey' => '_permission.language.actions.delete',
				'description'	 => 'Delete language item',
			),
			'edit' => array(
				'translationKey' => '_permission.language.actions.edit',
				'description'	 => 'Edit language item',
			),
			'list' => array(
				'translationKey' => '_permission.language.actions.list',
				'description'	 => 'List language files',
			),
		),
	),	
	
	// Manage layouts
	'layout' => array(
		'translationKey' => '_permission.layout.description',
		'description'	 => 'Manage layouts',
		'actions'		 => array(
			'edit' => array(
				'translationKey' => '_permission.layout.actions.edit',
				'description'	 => 'Edit layout script',
			),
			'list' => array(
				'translationKey' => '_permission.layout.actions.list',
				'description'	 => 'List layouts',
			),
		),
	),
	
	// Manage modules
	'module' => array(
		'translationKey' => '_permission.module.description',
		'description'	 => 'Manage modules',
		'actions'		 => array(
			'install' => array(
				'translationKey' => '_permission.module.actions.install',
				'description'	 => 'Install module',
			),
			'list' => array(
				'translationKey' => '_permission.module.actions.list',
				'description'	 => 'List modules',
			),
			'uninstall' => array(
				'translationKey' => '_permission.module.actions.uninstall',
				'description'	 => 'Uninstall module',
			),
		),
	),
	
	// Manage pages
	'page' => array(
		'translationKey' => '_permission.page.description',
		'description'	 => 'Manage pages',
		'actions'		 => array(
			'add' => array(
				'translationKey' => '_permission.page.actions.add',
				'description'	 => 'Add new page',
			),
			'delete' => array(
				'translationKey' => '_permission.page.actions.delete',
				'description'	 => 'Delete page',
			),
			'edit' => array(
				'translationKey' => '_permission.page.actions.edit',
				'description'	 => 'Edit page',
			),
			'export' => array(
				'translationKey' => '_permission.page.actions.export',
				'description'	 => 'Export layout of page',
			),
			'import' => array(
				'translationKey' => '_permission.page.actions.import',
				'description'	 => 'Import layout of page',
			),
			'layout' => array(
				'translationKey' => '_permission.page.actions.layout',
				'description'	 => 'Layout page',
			),
			'list' => array(
				'translationKey' => '_permission.page.actions.list',
				'description'	 => 'List pages',
			),
			'order' => array(
				'translationKey' => '_permission.page.actions.order',
				'description'	 => 'Order pages',
			),
		),
	),
	
	// Configure permalinks
	'permalink' => array(
		'translationKey' => '_permission.permalink.description',
		'description'	 => 'Configure permalinks',
		'actions'		 => array(
			'config' => array(
				'translationKey' => '_permission.permalink.actions.config',
				'description'	 => 'Configure permalinks',
			),
		),
	),
	
	// Manage plugins
	'plugin' => array(
		'translationKey' => '_permission.plugin.description',
		'description'	 => 'Manage plugins',
		'actions'		 => array(
			'config' => array(
				'translationKey' => '_permission.plugin.actions.config',
				'description'	 => 'Configure plugin',
			),
			'disable' => array(
				'translationKey' => '_permission.plugin.actions.disable',
				'description'	 => 'Disable plugin',
			),
			'enable' => array(
				'translationKey' => '_permission.plugin.actions.enable',
				'description'	 => 'Enable plugin',
			),
			'install' => array(
				'translationKey' => '_permission.plugin.actions.install',
				'description'	 => 'Install plugin',
			),
			'list' => array(
				'translationKey' => '_permission.plugin.actions.list',
				'description'	 => 'List plugins',
			),
			'uninstall' => array(
				'translationKey' => '_permission.plugin.actions.uninstall',
				'description'	 => 'Uninstall plugin',
			),
		),
	),
	
	// Manage privileges
	'privilege' => array(
		'translationKey' => '_permission.privilege.description',
		'description'	 => 'Manage privileges',
		'actions'		 => array(
			'list' => array(
				'translationKey' => '_permission.privilege.actions.list',
				'description'	 => 'List privileges',
			),
		),
	),
	
	// Manage roles
	'role' => array(
		'translationKey' => '_permission.role.description',
		'description'	 => 'Manage roles',
		'actions'		 => array(
			'add' => array(
				'translationKey' => '_permission.role.actions.add',
				'description'	 => 'Add new role',
			),
			'delete' => array(
				'translationKey' => '_permission.role.actions.delete',
				'description'	 => 'Delete role',
			),
			'list' => array(
				'translationKey' => '_permission.role.actions.list',
				'description'	 => 'List roles',
			),
			'lock' => array(
				'translationKey' => '_permission.role.actions.lock',
				'description'	 => 'Lock or unlock role',
			),
			'rename' => array(
				'translationKey' => '_permission.role.actions.rename',
				'description'	 => 'Rename role',
			),
		),
	),
	
	// Manage permissions
	'rule' => array(
		'translationKey' => '_permission.rule.description',
		'description'	 => 'Manage permissions',
		'actions'		 => array(
			'role' => array(
				'translationKey' => '_permission.rule.actions.role',
				'description'	 => 'Manage role\'s permissions',
			),
			'user' => array(
				'translationKey' => '_permission.rule.actions.user',
				'description'	 => 'Manage user\'s permissions',
			),
		),
	),
	
	// Manage skins
	'skin' => array(
		'translationKey' => '_permission.skin.description',
		'description'	 => 'Manage skins',
		'actions'		 => array(
			'activate' => array(
				'translationKey' => '_permission.skin.actions.activate',
				'description'	 => 'Activate skin',
			),
			'edit' => array(
				'translationKey' => '_permission.skin.actions.edit',
				'description'	 => 'Edit CSS file',
			),
			'list' => array(
				'translationKey' => '_permission.skin.actions.list',
				'description'	 => 'List skins',
			),
		),
	),
	
	// Manage tasks
	'task' => array(
		'translationKey' => '_permission.task.description',
		'description'	 => 'Manage tasks',
		'actions'		 => array(
			'config' => array(
				'translationKey' => '_permission.task.actions.config',
				'description'	 => 'Configure task',
			),
			'install' => array(
				'translationKey' => '_permission.task.actions.install',
				'description'	 => 'Install task',
			),
			'list' => array(
				'translationKey' => '_permission.task.actions.list',
				'description'	 => 'List tasks',
			),
			'run' => array(
				'translationKey' => '_permission.task.actions.run',
				'description'	 => 'Run task',
			),
			'schedule' => array(
				'translationKey' => '_permission.task.actions.schedule',
				'description'	 => 'Schedule task',
			),
			'uninstall' => array(
				'translationKey' => '_permission.task.actions.uninstall',
				'description'	 => 'Uninstall task',
			),
		),
	),
	
	// Manage templates
	'template' => array(
		'translationKey' => '_permission.template.description',
		'description'	 => 'Manage templates',
		'actions'		 => array(
			'activate' => array(
				'translationKey' => '_permission.template.actions.activate',
				'description'	 => 'Activate template',
			),
			'install' => array(
				'translationKey' => '_permission.template.actions.install',
				'description'	 => 'Install template',
			),
			'list' => array(
				'translationKey' => '_permission.template.actions.list',
				'description'	 => 'List templates',
			),
			'uninstall' => array(
				'translationKey' => '_permission.template.actions.uninstall',
				'description'	 => 'Uninstall template',
			),
		),
	),
	
	// Manage users
	'user' => array(
		'translationKey' => '_permission.user.description',
		'description'	 => 'Manage users',
		'actions'		 => array(
			'activate' => array(
				'translationKey' => '_permission.user.actions.activate',
				'description'	 => 'Activate or deactivate user',
			),
			'add' => array(
				'translationKey' => '_permission.user.actions.add',
				'description'	 => 'Add new user',
			),
			'avatar' => array(
				'translationKey' => '_permission.user.actions.avatar',
				'description'	 => 'Change avatar of any user',
			),
			'delete' => array(
				'translationKey' => '_permission.user.actions.delete',
				'description'	 => 'Delete user',
			),
			'edit' => array(
				'translationKey' => '_permission.user.actions.edit',
				'description'	 => 'Edit user information',
			),
			'list' => array(
				'translationKey' => '_permission.user.actions.list',
				'description'	 => 'List users',
			),
			'move' => array(
				'translationKey' => '_permission.user.actions.move',
				'description'	 => 'Move user to other group',
			),
		),
	),
	
	// Manage widgets
	'widget' => array(
		'translationKey' => '_permission.widget.description',
		'description'	 => 'Manage widgets',
		'actions'		 => array(
			'install' => array(
				'translationKey' => '_permission.widget.actions.install',
				'description'	 => 'Install widget',
			),
			'list' => array(
				'translationKey' => '_permission.widget.actions.list',
				'description'	 => 'List widgets',
			),
			'uninstall' => array(
				'translationKey' => '_permission.widget.actions.uninstall',
				'description'	 => 'Uninstall widget',
			),
		),
	),
);
