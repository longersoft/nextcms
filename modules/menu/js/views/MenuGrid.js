/**
 * NextCMS
 * 
 * @author		Nguyen Huu Phuoc <thenextcms@gmail.com>
 * @copyright	Copyright (c) 2011 - 2012, Nguyen Huu Phuoc
 * @license		http://nextcms.org/license.txt	(GNU GPL version 2 or later)
 * @link		http://nextcms.org
 * @category	modules
 * @package		menu
 * @subpackage	js
 * @since		1.0
 * @version		2011-10-18
 */

dojo.provide("menu.js.views.MenuGrid");

dojo.require("dijit.Menu");
dojo.require("dijit.MenuItem");
dojo.require("dijit.MenuSeparator");
dojo.require("dojo.data.ItemFileReadStore");
dojo.require("dojox.grid.EnhancedGrid");
dojo.require("dojox.grid.enhanced.plugins.DnD");
dojo.require("dojox.grid.enhanced.plugins.Menu");
dojo.require("dojox.grid.enhanced.plugins.NestedSorting");
dojo.require("dojox.widget.PlaceholderMenuItem");

dojo.require("core.js.base.Config");
dojo.require("core.js.base.controllers.ActionProvider");
dojo.require("core.js.base.I18N");

dojo.declare("menu.js.views.MenuGrid", null, {
	// _id: String
	_id: null,
	
	// _i18n: Object
	_i18n: null,
	
	// _menuGrid: dojox.grid.EnhancedGrid
	_menuGrid: null,
	
	// _editMenuItem: dijit.MenuItem
	_editMenuItem: null,
	
	// _deleteMenuItem: dijit.MenuItem
	_deleteMenuItem: null,
	
	constructor: function(/*String*/ id) {
		this._id = id;
		
		core.js.base.I18N.requireLocalization("menu/languages");
		this._i18n = core.js.base.I18N.getLocalization("menu/languages");
		
		this._createGrid();
	},
	
	_createGrid: function() {
		// summary:
		//		Creates the grid
		
		var _this = this;
		var languages = core.js.base.Config.get("core", "localization_languages");
		
		// Columns
		var layout = [{
			field: "title",
			width: "300px",
			name: this._i18n.menu._share.title
		}, {
			field: "created_date",
			width: "150px",
			name: this._i18n.menu._share.createdDate
		}, {
			field: "language",
			width: "150px",
			name: this._i18n.menu._share.language,
			formatter: function(language) {
				return languages ? language + " (" + languages[language] + ")" : language;
			}
		}];
		
		// Header menu
		var headerMenu = new dijit.Menu();
		headerMenu.addChild(new dijit.MenuItem({
			label: this._i18n.menu.list.showColumns,
			disabled: true
		}));
		headerMenu.addChild(new dijit.MenuSeparator());
		headerMenu.addChild(new dojox.widget.PlaceholderMenuItem({
			label: "GridColumns"
		}));
		headerMenu.startup();
		
		// Cell context menu
		var cellMenu = new dijit.Menu();
		
		// "Edit" menu item
		this._editMenuItem = new dijit.MenuItem({
			label: this._i18n.global._share.editAction,
			disabled: !core.js.base.controllers.ActionProvider.get("menu_menu_edit").isAllowed,
			onClick: function(e) {
				var rowIndex = _this._menuGrid.selection.selectedIndex;
				var item = _this._menuGrid.getItem(rowIndex);
				if (item) {
					_this.onEditMenu(item);
				}
			}
		});
		cellMenu.addChild(this._editMenuItem);
		
		// "Delete" menu item
		this._deleteMenuItem = new dijit.MenuItem({
			label: this._i18n.global._share.deleteAction,
			iconClass: "appIcon appDeleteIcon",
			disabled: !core.js.base.controllers.ActionProvider.get("menu_menu_delete").isAllowed,
			onClick: function(e) {
				var rowIndex = _this._menuGrid.selection.selectedIndex;
				var item = _this._menuGrid.getItem(rowIndex);
				if (item) {
					_this.onDeleteMenu(item);
				}
			}
		});
		cellMenu.addChild(this._deleteMenuItem);
		
		// "Localize" menu item
		if (languages) {
			var localizePopupMenu = new dijit.Menu();
			for (var locale in languages) {
				localizePopupMenu.addChild(new dijit.MenuItem({
					__locale: locale,
					label: languages[locale],
					iconClass: "appIcon appFlag_" + locale,
					onClick: function(e) {
						var rowIndex = _this._menuGrid.selection.selectedIndex;
						var item = _this._menuGrid.getItem(rowIndex);
						if (item) {
							var translations = dojo.fromJson(item.translations[0]);
							if (translations[this.__locale]) {
								_this.onEditMenu(translations[this.__locale]);
							} else {
								_this.onTranslateMenu(item, this.__locale);
							}
						}
					}
				}));
			}
			
			cellMenu.addChild(new dijit.MenuSeparator());
			cellMenu.addChild(new dijit.PopupMenuItem({
				label: this._i18n.global._share.localizeAction,
				popup: localizePopupMenu
			}));
		}
		
		// Create grid
		this._menuGrid = new dojox.grid.EnhancedGrid({
			clientSort: false,
			rowSelector: "20px",
			style: "height: 100%; width: 100%; visibility: hidden",
			structure: layout,
			plugins: {
				dnd: true,
				menus: {
					cellMenu: cellMenu
				},
				nestedSorting: true
			},
			headerMenu: headerMenu,
			loadingMessage: "<span class='dojoxGridLoading'>" + this._i18n.global._share.loadingAction  + "</span>",
			errorMessage: "<span class='dojoxGridError'>" + this._i18n.menu.list.error + "</span>",
			noDataMessage: "<span class='dojoxGridNoData'>" + this._i18n.menu.list.notFound + "</span>"
			// rowsPerPage: 40
		}, dojo.create('div'));
		dojo.byId(this._id).appendChild(this._menuGrid.domNode);
		
		dojo.connect(this._menuGrid, "onRowContextMenu", function(e) {
			var item = this.getItem(e.rowIndex);
			if (item) {
				_this.onRowContextMenu(item);
			}
		});
	},
	
	showMenus: function(/*Object*/ menus) {
		// summary:
		//		Shows the list of menus
		// menus:
		//		Array of menus, formatted as dojo.data.ItemFileReadStore data structure
		var store = new dojo.data.ItemFileReadStore({
			data: menus
		});
		dojo.style(this._menuGrid.domNode, {
			visibility: "visible"
		});
		this._menuGrid.setStore(store);
	},
	
	////////// UPDATE STATE OF CONTROLS //////////
	
	allowToDelete: function(/*Boolean*/ isAllowed) {
		// summary:
		//		Allows/disallows to delete the menu
		isAllowed = isAllowed && core.js.base.controllers.ActionProvider.get("menu_menu_delete").isAllowed;
		this._deleteMenuItem.set("disabled", !isAllowed);
		return this;	// menu.js.views.MenuGrid
	},
	
	allowToEdit: function(/*Boolean*/ isAllowed) {
		// summary:
		//		Allows/disallows to edit the menu
		isAllowed = isAllowed && core.js.base.controllers.ActionProvider.get("menu_menu_edit").isAllowed;
		this._editMenuItem.set("disabled", !isAllowed);
		return this;	// menu.js.views.MenuGrid
	},
	
	////////// CALLBACKS //////////
	
	onDeleteMenu: function(/*dojo.data.Item*/ item) {
		// summary:
		//		Deletes given menu
		// tags:
		//		callback
	},
	
	onEditMenu: function(/*dojo.data.Item|String*/ item) {
		// summary:
		//		Edits given menu
		// tags:
		//		callback
	},
	
	onRowContextMenu: function(/*dojo.data.Item*/ item) {
		// summary:
		//		Called when right-click on menu row
		// tags:
		//		callback
	},
	
	onTranslateMenu: function(/*dojo.data.Item*/ item, /*String*/ language) {
		// summary:
		//		Translates given menu to other language
		// tags:
		//		callback
	}
});
