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
 * @version		2012-05-12
 */

dojo.provide("core.js.views.PageGrid");

dojo.require("dijit.Menu");
dojo.require("dijit.MenuItem");
dojo.require("dijit.MenuSeparator");
dojo.require("dojo.data.ItemFileWriteStore");
dojo.require("dojox.grid.EnhancedGrid");
dojo.require("dojox.grid.enhanced.plugins.DnD");
dojo.require("dojox.grid.enhanced.plugins.Menu");
dojo.require("dojox.widget.PlaceholderMenuItem");

dojo.require("core.js.base.Config");
dojo.require("core.js.base.controllers.ActionProvider");
dojo.require("core.js.base.I18N");

dojo.declare("core.js.views.PageGrid", null, {
	// _id: String
	_id: null,
	
	// _i18n: Object
	_i18n: null,
	
	// _pageGrid: dojox.grid.EnhancedGrid
	_pageGrid: null,
	
	// _editMenuItem: dijit.MenuItem
	_editMenuItem: null,
	
	// _deleteMenuItem: dijit.MenuItem
	_deleteMenuItem: null,
	
	// _removeCacheMenuItem: dijit.MenuItem
	_removeCacheMenuItem: null,
	
	// _layoutMenuItem: dijit.MenuItem
	_layoutMenuItem: null,
	
	// _copyLayoutMenuItem: dijit.MenuItem
	_copyLayoutMenuItem: null,
	
	// _pasteLayoutMenuItem: dijit.MenuItem
	_pasteLayoutMenuItem: null,
	
	// _importLayoutMenuItem: dijit.MenuItem
	_importLayoutMenuItem: null,
	
	// _exportLayoutMenuItem: dijit.MenuItem
	_exportLayoutMenuItem: null,
	
	// _layoutData: Object
	_layoutData: {
		page_id: null,
		layout: null
	},
	
	constructor: function(/*String*/ id) {
		this._id = id;
		
		core.js.base.I18N.requireLocalization("core/languages");
		this._i18n = core.js.base.I18N.getLocalization("core/languages");
		
		this._createGrid();
	},
	
	_createGrid: function() {
		// summary:
		//		Creates the grid
		var _this = this;
		
		// Columns
		var layout = [{
			field: "name",
			width: "200px",
			name: this._i18n.page._share.name
		}, {
			field: "title",
			width: "200px",
			name: this._i18n.page._share.title
		}, {
			field: "route",
			width: "200px",
			name: this._i18n.page._share.route
		}, {
			field: "url",
			width: "200px",
			name: this._i18n.page._share.url
		}, {
			field: "ordering",
			width: "100px",
			name: this._i18n.page._share.ordering
		}, {
			field: "template",
			width: "100px",
			name: this._i18n.page._share.template
		}, {
			field: "language",
			width: "100px",
			name: this._i18n.page._share.language
		}, {
			field: "cache_lifetime",
			width: "150px",
			editable: core.js.base.controllers.ActionProvider.get("core_cache_page").isAllowed,
			name: this._i18n.page._share.cacheLifetime
		}];
		
		// Header menu
		var headerMenu = new dijit.Menu();
		headerMenu.addChild(new dijit.MenuItem({
			label: this._i18n.page.list.showColumns,
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
			disabled: !core.js.base.controllers.ActionProvider.get("core_page_edit").isAllowed,
			onClick: function(e) {
				var rowIndex = _this._pageGrid.selection.selectedIndex;
				var item = _this._pageGrid.getItem(rowIndex);
				if (item) {
					_this.onEditPage(item);
				}
			}
		});
		cellMenu.addChild(this._editMenuItem);
		
		// "Delete" menu item
		this._deleteMenuItem = new dijit.MenuItem({
			label: this._i18n.global._share.deleteAction,
			iconClass: "appIcon appDeleteIcon",
			disabled: !core.js.base.controllers.ActionProvider.get("core_page_delete").isAllowed,
			onClick: function(e) {
				var rowIndex = _this._pageGrid.selection.selectedIndex;
				var item = _this._pageGrid.getItem(rowIndex);
				if (item) {
					_this.onDeletePage(item);
				}
			}
		});
		cellMenu.addChild(this._deleteMenuItem);
		
		// "Remove cache" menu item
		this._removeCacheMenuItem = new dijit.MenuItem({
			label: this._i18n.page._share.removeCacheAction,
			disabled: !core.js.base.Config.get("core", "caching") || !core.js.base.controllers.ActionProvider.get("core_cache_remove").isAllowed,
			onClick: function(e) {
				var rowIndex = _this._pageGrid.selection.selectedIndex;
				var item = _this._pageGrid.getItem(rowIndex);
				if (item) {
					_this.onRemoveCache(item);
				}
			}
		});
		cellMenu.addChild(this._removeCacheMenuItem);
		
		cellMenu.addChild(new dijit.MenuSeparator());
		
		// "Layout" menu item
		this._layoutMenuItem = new dijit.MenuItem({
			label: this._i18n.page.list.layoutAction,
			disabled: !core.js.base.controllers.ActionProvider.get("core_page_layout").isAllowed,
			onClick: function(e) {
				var rowIndex = _this._pageGrid.selection.selectedIndex;
				var item = _this._pageGrid.getItem(rowIndex);
				if (item) {
					_this.onLayoutPage(item);
				}
			}
		});
		cellMenu.addChild(this._layoutMenuItem);
		
		// "Copy layout" menu item
		this._copyLayoutMenuItem = new dijit.MenuItem({
			label: this._i18n.page.list.copyLayoutAction,
			disabled: !core.js.base.controllers.ActionProvider.get("core_page_layout").isAllowed,
			onClick: function(e) {
				var rowIndex = _this._pageGrid.selection.selectedIndex;
				var item = _this._pageGrid.getItem(rowIndex);
				if (item) {
					_this._layoutData = {
						page_id: item.page_id[0],
						layout: item.layout[0]
					};
				}
			}
		});
		cellMenu.addChild(this._copyLayoutMenuItem);
		
		// "Paste layout" menu item
		this._pasteLayoutMenuItem = new dijit.MenuItem({
			label: this._i18n.page.list.pasteLayoutAction,
			disabled: !core.js.base.controllers.ActionProvider.get("core_page_layout").isAllowed,
			onClick: function(e) {
				var rowIndex = _this._pageGrid.selection.selectedIndex;
				var item = _this._pageGrid.getItem(rowIndex);
				if (item && _this._layoutData) {
					_this.onPasteLayout(item, _this._layoutData.layout);
				}
			}
		});
		cellMenu.addChild(this._pasteLayoutMenuItem);
		
		// "Import layout" menu item
		this._importLayoutMenuItem = new dijit.MenuItem({
			label: this._i18n.page._share.importLayoutAction,
			iconClass: "appIcon appImportIcon",
			disabled: !core.js.base.controllers.ActionProvider.get("core_page_import").isAllowed,
			onClick: function(e) {
				var rowIndex = _this._pageGrid.selection.selectedIndex;
				var item = _this._pageGrid.getItem(rowIndex);
				if (item) {
					_this.onImportLayout(item);
				}
			}
		});
		cellMenu.addChild(this._importLayoutMenuItem);
		
		// "Export layout" menu item
		this._exportLayoutMenuItem = new dijit.MenuItem({
			label: this._i18n.page._share.exportLayoutAction,
			iconClass: "appIcon appExportIcon",
			disabled: !core.js.base.controllers.ActionProvider.get("core_page_export").isAllowed,
			onClick: function(e) {
				var rowIndex = _this._pageGrid.selection.selectedIndex;
				var item = _this._pageGrid.getItem(rowIndex);
				if (item) {
					_this.onExportLayout(item);
				}
			}
		});
		cellMenu.addChild(this._exportLayoutMenuItem);
		
		// "Localize" menu item
		var languages = core.js.base.Config.get("core", "localization_languages");
		if (languages) {
			var localizePopupMenu = new dijit.Menu();
			for (var locale in languages) {
				localizePopupMenu.addChild(new dijit.MenuItem({
					__locale: locale,
					label: languages[locale],
					iconClass: "appIcon appFlag_" + locale,
					onClick: function(e) {
						var rowIndex = _this._pageGrid.selection.selectedIndex;
						var item = _this._pageGrid.getItem(rowIndex);
						if (item) {
							var translations = dojo.fromJson(item.translations[0]);
							if (translations[this.__locale]) {
								_this.onEditPage(translations[this.__locale]);
							} else {
								_this.onTranslatePage(item, this.__locale);
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
		this._pageGrid = new dojox.grid.EnhancedGrid({
			clientSort: false,
			rowSelector: "20px",
			style: "height: 100%; width: 100%; visibility: hidden",
			structure: layout,
			plugins: {
				dnd: true,
				menus: {
					cellMenu: cellMenu
				}
			},
			headerMenu: headerMenu,
			loadingMessage: "<span class='dojoxGridLoading'>" + this._i18n.global._share.loadingAction + "</span>",
			errorMessage: "<span class='dojoxGridError'>" + this._i18n.page.list.error + "</span>",
			noDataMessage: "<span class='dojoxGridNoData'>" + this._i18n.page.list.notFound + "</span>"
		}, dojo.create('div'));
		dojo.byId(this._id).appendChild(this._pageGrid.domNode);
		
		dojo.connect(this._pageGrid, "onApplyCellEdit", this, function(inValue, inRowIndex, inFieldIndex) {
			if (inFieldIndex == "cache_lifetime" && inValue) {
				inValue = parseInt(inValue);
				var item = this._pageGrid.getItem(inRowIndex);
				this.onSetCacheLifetime(item, inValue);
			}
		});
		
		dojo.connect(this._pageGrid, "onRowContextMenu", function(e) {
			var item = this.getItem(e.rowIndex);
			if (item) {
				_this.onRowContextMenu(item);
			}
		});
	},
	
	showPages: function(/*Object*/ pages) {
		// summary:
		//		Shows the list of pages
		this._layoutData = {
			page_id: null,
			layout: null
		};
		var store = new dojo.data.ItemFileWriteStore({
			data: pages
		});
		dojo.style(this._pageGrid.domNode, {
			visibility: "visible"
		});
		this._pageGrid.setStore(store);
	},
	
	getGrid: function() {
		// summary:
		//		Gets the grid instance
		return this._pageGrid;		// dojox.grid.EnhancedGrid
	},
	
	getCurrentLayoutData: function() {
		// summary:
		//		Gets the current layout data
		return this._layoutData;	// Object
	},
	
	////////// CONTROL STATE //////////
	
	allowToCopyLayout: function(/*Boolean*/ isAllowed) {
		// summary:
		//		Allows/disallows to copy the layout data
		this._copyLayoutMenuItem.set("disabled", !isAllowed);
		return this;	// core.js.views.PageGrid
	},
	
	allowToExportLayout: function(/*Boolean*/ isAllowed) {
		// summary:
		//		Allows/disallows to export the layout
		this._exportLayoutMenuItem.set("disabled", !isAllowed);
		return this;	// core.js.views.PageGrid
	},
	
	allowToPasteLayout: function(/*Boolean*/ isAllowed) {
		// summary:
		//		Allows/disallows to paste the layout data
		this._pasteLayoutMenuItem.set("disabled", !isAllowed);
		return this;	// core.js.views.PageGrid
	},
	
	////////// CALLBACKS //////////
	
	onDeletePage: function(/*dojo.data.Item*/ item) {
		// summary:
		//		Deletes given page item
		// tags:
		//		callback
	},
	
	onEditPage: function(/*dojo.data.Item|String*/ item) {
		// summary:
		//		Edits given page item
		// tags:
		//		callback
	},
	
	onExportLayout: function(/*dojo.data.Item*/ item) {
		// summary:
		//		Exports layout of given page
		// tags:
		//		callback
	},
	
	onImportLayout: function(/*dojo.data.Item*/ item) {
		// summary:
		//		Imports layout of given page
		// tags:
		//		callback
	},
	
	onLayoutPage: function(/*dojo.data.Item*/ item) {
		// summary:
		//		Layouts given page item
		// tags:
		//		callback
	},
	
	onPasteLayout: function(/*dojo.data.Item*/ item, /*String*/ layoutData) {
		// summary:
		//		Pastes layout data
		// layoutData:
		//		The layout data which will be set to the selected page
		// tags:
		//		callback
	},
	
	onRemoveCache: function(/*dojo.data.Item*/ item) {
		// summary:
		//		Removes cache of selected page
		// tags:
		//		callback
	},
	
	onRowContextMenu: function(/*dojo.data.Item*/ item) {
		// summary:
		//		Called when right-click on the page item
		// tags:
		//		callback
	},
	
	onSetCacheLifetime: function(/*dojo.data.Item*/ item, /*Integer*/ numSeconds) {
		// summary:
		//		Sets page cache lifetime
		// numSeconds:
		//		The number of seconds
		// tags:
		//		callback
	},
	
	onTranslatePage: function(/*dojo.data.Item*/ item, /*String*/ language) {
		// summary:
		//		Translates given menu to other language
		// tags:
		//		callback
	}
});
