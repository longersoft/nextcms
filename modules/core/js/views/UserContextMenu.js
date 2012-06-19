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

dojo.provide("core.js.views.UserContextMenu");

dojo.require("dijit.Menu");
dojo.require("dijit.MenuItem");
dojo.require("dijit.MenuSeparator");

dojo.require("core.js.base.controllers.ActionProvider");
dojo.require("core.js.base.I18N");

dojo.declare("core.js.views.UserContextMenu", null, {
	// _contextMenu: dijit.Menu
	_contextMenu: null,
	
	// _i18n: Object
	_i18n: null,
	
	// _activateMenuItem: dijit.MenuItem
	_activateMenuItem: null,
	
	// _deleteMenuItem: dijit.MenuItem
	_deleteMenuItem: null,
	
	// _setPermissionMenuItem: dijit.MenuItem
	_setPermissionMenuItem: null,
	
	constructor: function() {
		core.js.base.I18N.requireLocalization("core/languages");
		this._i18n = core.js.base.I18N.getLocalization("core/languages");
	},

	show: function(/*core.js.views.UserItemView*/ userItemView) {
		var _this = this;
		
		// Get user object
		var user  = userItemView.getUser();
		
		// Create menu
		this._contextMenu = new dijit.Menu({
			targetNodeIds: [ dojo.attr(userItemView.getDomNode(), "id") ]
		});
		
		// Activate/deactivate item
		this._activateMenuItem = new dijit.MenuItem({
			label: (user.status == "activated") ? this._i18n.global._share.deactivateAction : this._i18n.global._share.activateAction,
			iconClass: "appIcon " + (user.status == "activated" ? "appDeactivateIcon" : "appActivateIcon"),
			disabled: !core.js.base.controllers.ActionProvider.get("core_user_activate").isAllowed,
			onClick: function() {
				_this.onActivateUser(userItemView);
			}
		});
		this._contextMenu.addChild(this._activateMenuItem);
		
		// "Edit" menu item
		this._contextMenu.addChild(new dijit.MenuItem({
			label: this._i18n.global._share.editAction,
			iconClass: "appIcon coreEditUserIcon",
			disabled: !core.js.base.controllers.ActionProvider.get("core_user_edit").isAllowed,
			onClick: function() {
				_this.onEditUser(userItemView);
			}
		}));
		
		// Delete user
		this._deleteMenuItem = new dijit.MenuItem({
			label: this._i18n.global._share.deleteAction,
			iconClass: "appIcon appDeleteIcon",
			disabled: !core.js.base.controllers.ActionProvider.get("core_user_delete").isAllowed,
			onClick: function() {
				_this.onDeleteUser(userItemView);
			}
		});
		this._contextMenu.addChild(this._deleteMenuItem);
		
		this._contextMenu.addChild(new dijit.MenuSeparator());
		
		// Permission item
		this._setPermissionMenuItem = new dijit.MenuItem({
			label: this._i18n.rule._share.permissionAction,
			disabled: !core.js.base.controllers.ActionProvider.get("core_rule_user").isAllowed,
			onClick: function() {
				_this.onSetUserPermissions(userItemView);
			}
		});
		this._contextMenu.addChild(this._setPermissionMenuItem);
		
		this._contextMenu.startup();
		
		// Extension point
		this.onContextMenu(userItemView);
	},
	
	////////// CONTROL STATE OF MENU ITEMS //////////
	
	allowToActivate: function(/*Boolean*/ isAllowed) {
		// summary:
		//		Allows/disallows to activate/deactivate the user
		isAllowed = isAllowed && core.js.base.controllers.ActionProvider.get("core_user_activate").isAllowed;
		this._activateMenuItem.set("disabled", !isAllowed);
		return this;	// core.js.views.UserContextMenu
	},
	
	allowToDelete: function(/*Boolean*/ isAllowed) {
		// summary:
		//		Allows/disallows to delete the user
		isAllowed = isAllowed && core.js.base.controllers.ActionProvider.get("core_user_delete").isAllowed;
		this._deleteMenuItem.set("disabled", !isAllowed);
		return this;	// core.js.views.UserContextMenu
	},
	
	allowToSetPermission: function(/*Boolean*/ isAllowed) {
		// summary:
		//		Allows/disallows to set permissions to the user
		isAllowed = isAllowed && core.js.base.controllers.ActionProvider.get("core_rule_user").isAllowed;
		this._setPermissionMenuItem.set("disabled", !isAllowed);
		return this;	// core.js.views.UserContextMenu
	},
	
	////////// CALLBACKS //////////
	
	onActivateUser: function(/*core.js.views.UserItemView*/ userItemView) {
		// summary: 
		//		This method is called when the "Activate" menu item is selected
		// userItemView:
		//		The selected user item
		// tags:
		//		callback
	},
	
	onContextMenu: function(/*core.js.views.UserItemView*/ userItemView) {
		// summary:
		//		Called when user right-click an user item
		// userItemView:
		//		The selected user item
		// tags:
		//		callback
	},
	
	onDeleteUser: function(/*core.js.views.UserItemView*/ userItemView) {
		// summary:
		//		This method is called when the "Delete" menu item is selected
		// userItemView:
		//		The selected user item
		// tags:
		//		callback
	},
	
	onEditUser: function(/*core.js.views.UserItemView*/ userItemView) {
		// summary:
		//		This method is called when the "Edit" menu item is selected
		// userItemView:
		//		The selected user item
		// tags:
		//		callback
	},
	
	onSetUserPermissions: function(/*core.js.views.UserItemView*/ userItemView) {
		// summary:
		//		This method is called when the "Set permissions" menu item is selected
		// roleItemView:
		//		The selected user item
		// tags:
		//		callback
	}
});
