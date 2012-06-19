/**
 * NextCMS
 * 
 * @author		Nguyen Huu Phuoc <thenextcms@gmail.com>
 * @copyright	Copyright (c) 2011 - 2012, Nguyen Huu Phuoc
 * @license		http://nextcms.org/license.txt	(GNU GPL version 2 or later)
 * @link		http://nextcms.org
 * @category	modules
 * @package		message
 * @subpackage	js
 * @since		1.0
 * @version		2012-02-28
 */

dojo.provide("message.js.views.FolderContextMenu");

dojo.require("dijit.Menu");
dojo.require("dijit.MenuItem");
dojo.require("dijit.MenuSeparator");

dojo.require("core.js.base.controllers.ActionProvider");
dojo.require("core.js.base.I18N");

dojo.declare("message.js.views.FolderContextMenu", null, {
	// _contextMenu: dijit.Menu
	_contextMenu: null,
	
	// _i18n: Object
	_i18n: null,
	
	// _deleteMenuItem: dijit.MenuItem
	_deleteMenuItem: null,
	
	// _renameMenuItem: dijit.MenuItem
	_renameMenuItem: null,
	
	constructor: function() {
		core.js.base.I18N.requireLocalization("message/languages");
		this._i18n = core.js.base.I18N.getLocalization("message/languages");
	},

	show: function(/*message.js.views.FolderItemView*/ folderItemView) {
		// summary:
		//		Show menu context for selected folder item
		var _this = this;
		
		// Create menu
		this._contextMenu = new dijit.Menu({
			targetNodeIds: [ dojo.attr(folderItemView.getDomNode(), "id") ]
		});
		
		// "Delete" menu item
		this._deleteMenuItem = new dijit.MenuItem({
			label: this._i18n.global._share.deleteAction,
			iconClass: "appIcon appDeleteIcon",
			disabled: !core.js.base.controllers.ActionProvider.get("message_folder_delete").isAllowed,
			onClick: function() {
				_this.onDeleteFolder(folderItemView);
			}
		});
		this._contextMenu.addChild(this._deleteMenuItem);
		
		// "Rename" item
		this._renameMenuItem = new dijit.MenuItem({
			label: this._i18n.global._share.renameAction,
			iconClass: "appIcon appRenameIcon",
			disabled: !core.js.base.controllers.ActionProvider.get("message_folder_rename").isAllowed,
			onClick: function() {
				_this.onRenameFolder(folderItemView);
			}
		});
		this._contextMenu.addChild(this._renameMenuItem);
		
		this._contextMenu.startup();
		
		// Extension point
		this.onContextMenu(folderItemView);
	},
	
	////////// CONTROL STATE OF MENU ITEMS //////////
	
	allowToDelete: function(/*Boolean*/ isAllowed) {
		// summary:
		//		Allows/disallows to delete the message folder
		isAllowed = isAllowed && core.js.base.controllers.ActionProvider.get("message_folder_delete").isAllowed;
		this._deleteMenuItem.set("disabled", !isAllowed);
		return this;	// message.js.views.FolderContextMenu
	},
	
	allowToRename: function(/*Boolean*/ isAllowed) {
		// summary:
		//		Allows/disallows to rename the message folder
		isAllowed = isAllowed && core.js.base.controllers.ActionProvider.get("message_folder_rename").isAllowed;
		this._renameMenuItem.set("disabled", !isAllowed);
		return this;	// message.js.views.FolderContextMenu
	},
	
	////////// CALLBACKS //////////
	
	onContextMenu: function(/*message.js.views.FolderItemView*/ folderItemView) {
		// summary:
		//		Called after showing the context menu
		// tags:
		//		callback
	},
	
	onDeleteFolder: function(/*message.js.views.FolderItemView*/ folderItemView) {
		// summary:
		//		Deletes folder
		// tags:
		//		callback
	},
	
	onRenameFolder: function(/*message.js.views.FolderItemView*/ folderItemView) {
		// summary:
		//		Renames folder
		// tags:
		//		callback
	}
});
