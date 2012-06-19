/**
 * NextCMS
 * 
 * @author		Nguyen Huu Phuoc <thenextcms@gmail.com>
 * @copyright	Copyright (c) 2011 - 2012, Nguyen Huu Phuoc
 * @license		http://nextcms.org/license.txt	(GNU GPL version 2 or later)
 * @link		http://nextcms.org
 * @category	modules
 * @package		core
 * @subpackage	js
 * @since		1.0
 * @version		2012-03-28
 */

dojo.provide("core.js.controllers.UserMediator");

dojo.declare("core.js.controllers.UserMediator", null, {
	// _roleContextMenu: core.js.views.RoleContextMenu
	_roleContextMenu: null,
	
	// _userContextMenu: core.js.views.UserContextMenu
	_userContextMenu: null,
	
	setRoleContextMenu: function(/*core.js.views.RoleContextMenu*/ roleContextMenu) {
		// summary:
		//		Sets the role's context menu
		this._roleContextMenu = roleContextMenu;
		
		dojo.connect(roleContextMenu, "onContextMenu", this, function(roleItemView) {
			var role = roleItemView.getRole();
			var isRootRole = role.name == "admin";
			
			// Do not allow to delete root role or a role which have user(s)
			roleContextMenu.allowToDelete(role.num_users == 0 && !isRootRole)
						   .allowToLock(!isRootRole)
						   .allowToSetPermission(!role.locked && !isRootRole);
		});
	},
	
	setUserContextMenu: function(/*core.js.views.UserContextMenu*/ userContextMenu) {
		// summary:
		//		Sets the user's context menu
		this._userContextMenu = userContextMenu;
		
		dojo.connect(userContextMenu, "onContextMenu", this, function(userItemView) {
			var isRootUser = userItemView.getUser().user_name == "admin";
			
			// Do not allow to activate, delete and set permission root user
			userContextMenu.allowToActivate(!isRootUser)
						   .allowToDelete(!isRootUser)
						   .allowToSetPermission(!isRootUser);
		});
	}
});
