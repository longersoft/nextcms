/**
 * NextCMS
 * 
 * @author		Nguyen Huu Phuoc <thenextcms@gmail.com>
 * @copyright	Copyright (c) 2011 - 2012, Nguyen Huu Phuoc
 * @license		http://nextcms.org/license.txt	(GNU GPL version 2 or later)
 * @link		http://nextcms.org
 * @category	modules
 * @package		media
 * @subpackage	hooks
 * @since		1.0
 * @version		2011-10-18
 */

dojo.provide("media.hooks.editor.ImageContextMenu");

dojo.require("dijit.Menu");
dojo.require("dijit.MenuItem");
dojo.require("dijit.MenuSeparator");

dojo.require("core.js.base.I18N");

dojo.declare("media.hooks.editor.ImageContextMenu", null, {
	// _id: String
	_id: null,
	
	// _i18n: Object
	_i18n: null,
	
	// _menuItems: Object
	_menuItems: {},
	
	constructor: function(/*String*/ id) {
		this._id = id;
		
		core.js.base.I18N.requireLocalization("media/hooks/editor");
		this._i18n = core.js.base.I18N.getLocalization("media/hooks/editor");
		
		this._createContextMenu();
	},
	
	_createContextMenu: function() {
		// summary:
		//		Creates the context menu
		var _this = this;
		
		// Create menu
		var contextMenu = new dijit.Menu({
			targetNodeIds: [ this._id ]
		});
		
		contextMenu.addChild(new dijit.MenuItem({
			label: _this._i18n.show.selectEditSize,
			disabled: true
		}));
		contextMenu.addChild(new dijit.MenuSeparator());
		
		// Create menu items
		dojo.forEach(["square", "thumbnail", "small", "crop", "medium", "large", "original"], function(size) {
			var item = new dijit.MenuItem({
				label: _this._i18n.show[size + "Size"],
				onClick: function() {
					_this.onEditSize(size);
				}
			});
			_this._menuItems[size] = item;
			contextMenu.addChild(item);
		});
		
		dojo.connect(contextMenu, "_openMyself", this, function(e) {
			this.onContextMenu(e.target);
		});
		contextMenu.startup();
	},
	
	allowToEdit: function(/*String|null*/ size, /*Boolean*/ isAllowed) {
		// summary:
		//		Allows or disallows to edit the image in given size
		if (size == null) {
			for (var thumb in this._menuItems) {
				this._allowToEdit(thumb, isAllowed);
			}
		} else {
			this._allowToEdit(size, isAllowed);
		}
		return this;	// media.hooks.editor.ImageContextMenu
	},
	
	_allowToEdit: function(/*String*/ size, /*Boolean*/ isAllowed) {
		this._menuItems[size].set("disabled", !isAllowed);
	},
	
	////////// CALLBACKS //////////
	
	onContextMenu: function(/*DomNode*/ target) {
		// tags:
		//		callback
	},
	
	onEditSize: function(/*String*/ size) {
		// tags:
		//		callback
	}
});
