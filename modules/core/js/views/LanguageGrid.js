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
 * @version		2011-12-11
 */

dojo.provide("core.js.views.LanguageGrid");

dojo.require("dijit.Menu");
dojo.require("dijit.MenuItem");
dojo.require("dijit.MenuSeparator");
dojo.require("dijit.tree.ForestStoreModel");
dojo.require("dojo.data.ItemFileWriteStore");
dojo.require("dojox.grid.TreeGrid");

dojo.require("core.js.base.controllers.ActionProvider");
dojo.require("core.js.base.I18N");
dojo.require("core.js.Constant");

dojo.declare("core.js.views.LanguageGrid", null, {
	// _id: String
	_id: null,
	
	// _parentNode: DomNode
	_parentNode: null,
	
	// _i18n: Object
	_i18n: null,
	
	// _grid: dojox.grid.TreeGrid
	_grid: null,
	
	// _contextMenu: dijit.Menu
	_contextMenu: null,
	
	// _refreshMenuItem: dijit.MenuItem
	_refreshMenuItem: null,
	
	// _addMenuItem: dijit.MenuItem
	_addMenuItem: null,
	
	// _deleteMenuItem: dijit.MenuItem
	_deleteMenuItem: null,
	
	// _editMenuItem: dijit.MenuItem
	_editMenuItem: null,
	
	// _closeMenuItem: dijit.MenuItem
	_closeMenuItem: null,
	
	// _selectedItem: dojo.data.Item
	_selectedItem: null,
	
	// _file: String
	//		Selected language file
	_file: null,
	
	constructor: function(/*String*/ id) {
		this._id		 = id;
		this._parentNode = dojo.byId(id).parentNode;
		
		core.js.base.I18N.requireLocalization("core/languages");
		this._i18n = core.js.base.I18N.getLocalization("core/languages");
		
		this._createContextMenu();
	},
	
	getId: function() {
		// summary:
		//		Gets the Id of grid container
		return this._id;	// String
	},	
	
	_createContextMenu: function() {
		// summary:
		//		Creates context menu for TreeGrid
		var _this = this, 
			contextMenuId = this._id + "_ContextMenu";
		dojo.create("div", {
			id: contextMenuId
		}, this._parentNode);
		this._contextMenu = new dijit.Menu({
			targetNodeIds: [ contextMenuId ]
		});
		
		// "Refresh" menu item
		this._refreshMenuItem = new dijit.MenuItem({
			label: this._i18n.global._share.refreshAction,
			iconClass: "appIcon appRefreshIcon",
			disabled: !core.js.base.controllers.ActionProvider.get("core_language_list").isAllowed,
			onClick: function() {
				if (_this._file) {
					_this.showLanguageFile(_this._file);
				}
			}
		});
		this._contextMenu.addChild(this._refreshMenuItem);
		this._contextMenu.addChild(new dijit.MenuSeparator());
		
		// "Add" menu item
		this._addMenuItem = new dijit.MenuItem({
			label: this._i18n.global._share.addAction,
			iconClass: "appIcon appAddIcon",
			disabled: !core.js.base.controllers.ActionProvider.get("core_language_add").isAllowed,
			onClick: function() {
				if (_this._selectedItem) {
					_this.onAddItem(_this._selectedItem);
				}
			}
		});
		this._contextMenu.addChild(this._addMenuItem);
		
		// "Delete" menu item
		this._deleteMenuItem = new dijit.MenuItem({
			label: this._i18n.global._share.deleteAction,
			iconClass: "appIcon appDeleteIcon",
			disabled: !core.js.base.controllers.ActionProvider.get("core_language_delete").isAllowed,
			onClick: function() {
				if (_this._selectedItem) {
					_this.onDeleteItem(_this._selectedItem);
				}
			}
		});
		this._contextMenu.addChild(this._deleteMenuItem);
		
		// "Edit" menu item
		this._editMenuItem = new dijit.MenuItem({
			label: this._i18n.global._share.editAction,
			disabled: !core.js.base.controllers.ActionProvider.get("core_language_edit").isAllowed,
			onClick: function() {
				if (_this._selectedItem) {
					_this.onEditItem(_this._selectedItem);
				}
			}
		});
		this._contextMenu.addChild(this._editMenuItem);
		
		// "Close" menu item
		this._contextMenu.addChild(new dijit.MenuSeparator());
		this._closeMenuItem = new dijit.MenuItem({
			label: this._i18n.global._share.closeAction,
			iconClass: "appIcon appCancelIcon",
			onClick: function() {
				if (_this._grid) {
					_this._grid.destroyRecursive();
					_this._grid = null;
				}
				_this._selectedItem = null;
				_this.onClose();
			}
		});
		this._contextMenu.addChild(this._closeMenuItem);
	},
	
	_createGrid: function(/*Object*/ data) {
		// summary:
		//		Creates the grid
		var layout = [{
			field: "key",
			width: "300px",
			name: this._i18n.language._share.key
		}, {
			field: "text",
			width: "400px",
			name: this._i18n.language._share.text,
			editable: false // core.js.base.controllers.ActionProvider.get("core_language_edit").isAllowed
		}];
		
		if (this._grid) {
			this._grid.destroyRecursive();
			this._grid = null;
		}
		var div = dojo.create("div", {
			id: this._id
		}, this._parentNode);
		
		this._grid = new dojox.grid.TreeGrid({
			structure: layout,
			treeModel: new dijit.tree.ForestStoreModel({
				store: new dojo.data.ItemFileWriteStore({
					data: data
				}),
				query: {
					path: "*"
				},
				childrenAttrs: ["children"]
			}),
//			style: "height: 100%; width: 100%",
			loadingMessage: "<span class='dojoxGridLoading'>" + this._i18n.global._share.loadingAction + "</span>"
		}, this._id);
		this._grid.startup();
		
		dojo.connect(this._grid, "onRowContextMenu", this, function(e) {
			var info = {
				target: e.target,
				coords: e.keyCode !== dojo.keys.F10 && "pageX" in e ? { x: e.pageX, y: e.pageY } : null
			};
			this._selectedItem = this._grid.getItem(e.rowIndex);
			this._contextMenu._openMyself(info);
			this.onRowContextMenu(this._selectedItem);
		});
	},
	
	showLanguageFile: function(/*String*/ file) {
		// summary:
		//		Populates the content of language file
		// file:
		//		Path to the language file
		// Get the locale
		if (!file) {
			return;
		}
		this._file = file;
		var items  = file.split("/");
		var module = items[0];
		var locale = items[items.length - 1].split(".")[0];
		var path   = items.slice(0, items.length - 1).join("/");
		
		// Load the language file
		dojo.registerModulePath(module, core.js.Constant.ROOT_URL + "/modules/" + module);
		core.js.base.I18N.requireLocalization(path, locale);
		var i18n = core.js.base.I18N.getLocalization(path, locale);
		
		// Build the data for the TreeGrid
		var data = {
			identifier: "path",
			label: "key",
			items: [{
				key: this._i18n.language._share.root,
				path: ".",
				text: "",
				root: true,
				children: this._buildData("", i18n)
			}]
		};
		this._createGrid(data);
	},
	
	getLanguageFile: function() {
		// summary:
		//		Gets the path to selected language file
		return this._file;	// String
	},
	
	_buildData: function(/*String*/ parentKey, /*Object|String*/ i18n) {
		// summary:
		//		Builds data for TreeGrid
		if (dojo.isString(i18n)) {
			return [];	// Array
		}
		
		var children = [], item, path;
		for (var key in i18n) {
			path = (parentKey == "") ? key : (parentKey + "." + key); 
			item = {
				key: key,
				path: path,
				text: dojo.isString(i18n[key]) ? i18n[key] : ""
			};
			if (dojo.isObject(i18n[key])) {
				item.children = this._buildData(path, i18n[key]);
			}
			children.push(item);
		}
		return children;	// Array
	},
	
	addItem: function(/*Object*/ item) {
		// summary:
		//		Adds new item as child of current selected item
		// item:
		//		Consists of path, key and text properties
		if (this._grid && this._selectedItem) {
			this._grid.treeModel.newItem(item, this._selectedItem);
		}
	},
	
	deleteSelectedItem: function() {
		// summary:
		//		Deletes the selected item
		if (this._grid && this._selectedItem) {
			this._grid.store.deleteItem(this._selectedItem);
			this._selectedItem = null;
		}
	},
	
	updateSelectedItem: function(/*String*/ text) {
		// summary:
		//		Sets text to current selected item
		if (this._grid && this._selectedItem) {
			this._grid.store.setValue(this._selectedItem, "text", text);
		}
	},
	
	////////// CONTROL STATE //////////
	
	allowToDelete: function(/*Boolean*/ isAllowed) {
		// summary:
		//		Allows/disallows to delete a node
		this._deleteMenuItem.set("disabled", !core.js.base.controllers.ActionProvider.get("core_language_delete").isAllowed || !isAllowed);
		return this;	// core.js.views.LanguageGrid
	},
	
	allowToEdit: function(/*Boolean*/ isAllowed) {
		// summary:
		//		Allows/disallows to edit a node
		this._editMenuItem.set("disabled", !core.js.base.controllers.ActionProvider.get("core_language_edit").isAllowed || !isAllowed);
		return this;	// core.js.views.LanguageGrid
	},
	
	////////// CALLBACKS //////////
	
	onAddItem: function(/*dojo.data.Item*/ parentItem) {
		// summary:
		//		Adds new language item
		// tags:
		//		callback
	},
	
	onClose: function() {
		// summary:
		//		Closes the current file
		// tags:
		//		callback
	},
	
	onDeleteItem: function(/*dojo.data.Item*/ item) {
		// summary:
		//		Deletes a given language item
		// tags:
		//		callback
	},
	
	onEditItem: function(/*dojo.data.Item*/ item) {
		// summary:
		//		Edits a given language item
		// tags:
		//		callback
	},
	
	onRowContextMenu: function(/*dojo.data.Item*/ item) {
		// summary:
		//		Called when righ-click a given row
		// tags:
		//		callback
	}
});
