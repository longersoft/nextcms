/**
 * NextCMS
 * 
 * @author		Nguyen Huu Phuoc <thenextcms@gmail.com>
 * @copyright	Copyright (c) 2011 - 2012, Nguyen Huu Phuoc
 * @license		http://nextcms.org/license.txt	(GNU GPL version 2 or later)
 * @link		http://nextcms.org
 * @category	modules
 * @package		file
 * @subpackage	js
 * @since		1.0
 * @version		2011-10-18
 */

dojo.provide("file.js.views.ConnectionContextMenu");

dojo.require("dijit.Menu");
dojo.require("dijit.MenuItem");
dojo.require("dijit.MenuSeparator");

dojo.require("core.js.base.controllers.ActionProvider");
dojo.require("core.js.base.I18N");

dojo.declare("file.js.views.ConnectionContextMenu", null, {
	// _contextMenu: dijit.Menu
	//		The context menu for each connection item
	_contextMenu: null,
	
	// _i18n: Object
	_i18n: null,
	
	// _deleteMenuItem: dijit.MenuItem
	_deleteMenuItem: null,
	
	// _connectMenuItem: dijit.MenuItem
	_connectMenuItem: null,
	
	constructor: function() {
		core.js.base.I18N.requireLocalization("file/languages");
		this._i18n = core.js.base.I18N.getLocalization("file/languages");
	},

	show: function(/*file.js.views.ConnectionItemView*/ connectionItemView) {
		// summary:
		//		Shows menu context for selected connection item
		var _this = this;
		
		// Create menu
		this._contextMenu = new dijit.Menu({
			targetNodeIds: [ dojo.attr(connectionItemView.getDomNode(), "id") ]
		});
		
		// Edit item
		this._contextMenu.addChild(new dijit.MenuItem({
			label: this._i18n.global._share.editAction,
			disabled: !core.js.base.controllers.ActionProvider.get("file_connection_edit").isAllowed,
			onClick: function() {
				_this.onEditConnection(connectionItemView);
			}
		}));
		
		// Delete item
		this._deleteMenuItem = new dijit.MenuItem({
	    	label: this._i18n.global._share.deleteAction,
	    	iconClass: "appIcon appDeleteIcon",
	    	disabled: !core.js.base.controllers.ActionProvider.get("file_connection_delete").isAllowed,
	    	onClick: function() {
	    		_this.onDeleteConnection(connectionItemView);
	    	}
	    });
		this._contextMenu.addChild(this._deleteMenuItem);
		
		// Rename item
		this._contextMenu.addChild(new dijit.MenuItem({
	    	label: this._i18n.global._share.renameAction,
	    	iconClass: "appIcon appRenameIcon",
	    	disabled: !core.js.base.controllers.ActionProvider.get("file_connection_rename").isAllowed,
	    	onClick: function() {
	    		_this.onRenameConnection(connectionItemView);
	    	}
	    }));
		
		this._contextMenu.addChild(new dijit.MenuSeparator());
		
		// Connect item
		var isConnected = connectionItemView.getConnection().is_connected;
		this._connectMenuItem = new dijit.MenuItem({
	    	label: this._i18n.global._share[isConnected ? "disconnectAction" : "connectAction"],
	    	iconClass: "appIcon " + (isConnected ? "fileConnectionDisconnectIcon" : "fileConnectionConnectIcon"),
	    	disabled: !core.js.base.controllers.ActionProvider.get("file_connection_connect").isAllowed,
	    	onClick: function() {
	    		isConnected ? _this.onDisconnect(connectionItemView) : _this.onConnect(connectionItemView);
	    	}
	    });
		this._contextMenu.addChild(this._connectMenuItem);

		// Extension point
		this.onContextMenu(connectionItemView);
		
		this._contextMenu.startup();
	},
	
	////////// UPDATE STATE OF CONTROLS //////////
	
	allowToDelete: function(/*Boolean*/ isAllowed) {
		// summary:
		//		Allows/disallows to delete the connection
		isAllowed = isAllowed && core.js.base.controllers.ActionProvider.get("file_connection_delete").isAllowed;
		this._deleteMenuItem.set("disabled", !isAllowed);
		return this;	// file.js.views.ConnectionContextMenu
	},
	
	////////// CALLBACKS //////////
	
	onContextMenu: function(/*media.js.views.ConnectionItemView*/ connectionItemView) {
		// tags:
		//		callback
	},
	
	onRenameConnection: function(/*media.js.views.ConnectionItemView*/ connectionItemView) {
		// summary:
		//		This method is called when the rename item is clicked
		// connectionItemView:
		//		The selected connection item
		// tags:
		//		callback
	},
	
	onEditConnection: function(/*media.js.views.ConnectionItemView*/ connectionItemView) {
		// summary:
		//		This method is called when the edit item is clicked
		// connectionItemView:
		//		The selected connection item
		// tags:
		//		callback
	},
	
	onDeleteConnection: function(/*media.js.views.ConnectionItemView*/ connectionItemView) {
		// summary:
		//		This method is called when the delete item is clicked
		// connectionItemView:
		//		The selected connection item
		// tags:
		//		callback
	},
	
	onConnect: function(/*media.js.views.ConnectionItemView*/ connectionItemView) {
		// summary:
		//		This method is called when the connect item is clicked
		// connectionItemView:
		//		The selected connection item
		// tags:
		//		callback
	},
	
	onDisconnect: function(/*media.js.views.ConnectionItemView*/ connectionItemView) {
		// summary:
		//		This method is called when the Disconnect item is clicked
		// connectionItemView:
		//		The selected connection item
		// tags:
		//		callback
	}
});
