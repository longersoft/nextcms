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
 * @version		2011-10-18
 */

dojo.provide("core.js.views.ErrorGrid");

dojo.require("dijit.Menu");
dojo.require("dijit.MenuItem");
dojo.require("dijit.MenuSeparator");
dojo.require("dojo.data.ItemFileReadStore");
dojo.require("dojox.grid.EnhancedGrid");
dojo.require("dojox.grid.enhanced.plugins.Menu");
dojo.require("dojox.widget.PlaceholderMenuItem");

dojo.require("core.js.base.controllers.ActionProvider");
dojo.require("core.js.base.I18N");

dojo.declare("core.js.views.ErrorGrid", null, {
	// _id: String
	_id: null,
	
	// _i18n: Object
	_i18n: null,
	
	// _errorGrid: dojox.grid.EnhancedGrid
	_errorGrid: null,
	
	// _deleteMenuItem: dijit.MenuItem
	_deleteMenuItem: null,
	
	// _viewMenuItem: dijit.MenuItem
	_viewMenuItem: null,
	
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
			field: "uri",
			width: "200px",
			name: this._i18n.error._share.uri
		}, {
			field: "module",
			width: "100px",
			name: this._i18n.error._share.module
		}, {
			field: "controller",
			width: "100px",
			name: this._i18n.error._share.controller
		}, {
			field: "action",
			width: "100px",
			name: this._i18n.error._share.action
		}, {
			field: "file",
			width: "200px",
			name: this._i18n.error._share.file
		}, {
			field: "line",
			width: "100px",
			name: this._i18n.error._share.line
		}, {
			field: "message",
			width: "400px",
			name: this._i18n.error._share.message
		}, {
			field: "created_date",
			width: "200px",
			name: this._i18n.error._share.createdDate
		}];
		
		// Header menu
		var headerMenu = new dijit.Menu();
		headerMenu.addChild(new dijit.MenuItem({
			label: this._i18n.error.list.showColumns,
			disabled: true
		}));
		headerMenu.addChild(new dijit.MenuSeparator());
		headerMenu.addChild(new dojox.widget.PlaceholderMenuItem({
			label: "GridColumns"
		}));
		headerMenu.startup();
		
		// Cell context menu
		var cellMenu = new dijit.Menu();
		
		// "Delete" menu item
		this._deleteMenuItem = new dijit.MenuItem({
			label: this._i18n.global._share.deleteAction,
			iconClass: "appIcon appDeleteIcon",
			disabled: !core.js.base.controllers.ActionProvider.get("core_error_delete").isAllowed,
			onClick: function(e) {
				var rowIndex = _this._errorGrid.selection.selectedIndex;
				var item = _this._errorGrid.getItem(rowIndex);
				if (item) {
					_this.onDeleteError(item);
				}
			}
		});
		cellMenu.addChild(this._deleteMenuItem);
		
		// "View" menu item
		this._viewMenuItem = new dijit.MenuItem({
			label: this._i18n.global._share.viewAction,
			disabled: !core.js.base.controllers.ActionProvider.get("core_error_view").isAllowed,
			onClick: function(e) {
				var rowIndex = _this._errorGrid.selection.selectedIndex;
				var item = _this._errorGrid.getItem(rowIndex);
				if (item) {
					_this.onViewError(item);
				}
			}
		});
		cellMenu.addChild(this._viewMenuItem);
		
		// Create grid
		this._errorGrid = new dojox.grid.EnhancedGrid({
			clientSort: false,
			rowSelector: "20px",
			style: "height: 100%; width: 100%; visibility: hidden",
			structure: layout,
			plugins: {
				menus: {
					cellMenu: cellMenu
				}
			},
			headerMenu: headerMenu,
			loadingMessage: "<span class='dojoxGridLoading'>" + this._i18n.global._share.loadingAction  + "</span>",
			errorMessage: "<span class='dojoxGridError'>" + this._i18n.error.list.error + "</span>",
			noDataMessage: "<span class='dojoxGridNoData'>" + this._i18n.error.list.notFound + "</span>"
		}, dojo.create('div'));
		dojo.byId(this._id).appendChild(this._errorGrid.domNode);
		
		dojo.connect(this._errorGrid, "onRowContextMenu", function(e) {
			var item = this.getItem(e.rowIndex);
			if (item) {
				_this.onRowContextMenu(item);
			}
		});
	},
	
	showErrors: function(/*Object*/ errors) {
		// summary:
		//		Shows the list of errors
		var store = new dojo.data.ItemFileReadStore({
			data: errors
		});
		dojo.style(this._errorGrid.domNode, {
			visibility: "visible"
		});
		this._errorGrid.setStore(store);
	},
	
	////////// UPDATE STATE OF CONTROLS //////////	
	
	allowToDelete: function(/*Boolean*/ isAllowed) {
		// summary:
		//		Allows/disallows to delete the error log
		isAllowed = isAllowed && core.js.base.controllers.ActionProvider.get("core_error_delete").isAllowed;
		this._deleteMenuItem.set("disabled", !isAllowed);
		return this;	// core.js.views.ErrorGrid
	},
	
	////////// CALLBACKS //////////
	
	onDeleteError: function(/*dojo.data.Item*/ item) {
		// summary:
		//		Deletes given error item
		// tags:
		//		callback
	},
	
	onRowContextMenu: function(/*dojo.data.Item*/ item) {
		// summary:
		//		Called when right-click on the error item
		// tags:
		//		callback
	},
	
	onViewError: function(/*dojo.data.Item*/ item) {
		// summary:
		//		Views given error item
		// tags:
		//		callback
	}
});
