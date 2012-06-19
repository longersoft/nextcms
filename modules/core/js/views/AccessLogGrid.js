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
 * @version		2011-12-16
 */

dojo.provide("core.js.views.AccessLogGrid");

dojo.require("dijit.Menu");
dojo.require("dijit.MenuItem");
dojo.require("dijit.MenuSeparator");
dojo.require("dojo.data.ItemFileReadStore");
dojo.require("dojox.grid.EnhancedGrid");
dojo.require("dojox.grid.enhanced.plugins.Menu");
dojo.require("dojox.widget.PlaceholderMenuItem");

dojo.require("core.js.base.controllers.ActionProvider");
dojo.require("core.js.base.I18N");

dojo.declare("core.js.views.AccessLogGrid", null, {
	// _id: String
	_id: null,
	
	// _i18n: Object
	_i18n: null,
	
	// _accessLogGrid: dojox.grid.EnhancedGrid
	_accessLogGrid: null,
	
	// _deleteMenuItem: dijit.MenuItem
	_deleteMenuItem: null,
	
	// _viewMenuItem: dijit.MenuItem
	_viewMenuItem: null,
	
	// _filterByIpMenuItem: dijit.MenuItem
	_filterByIpMenuItem: null,
	
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
			field: "url",
			width: "400px",
			name: this._i18n.accesslog._share.url
		}, {
			field: "module",
			width: "100px",
			name: this._i18n.accesslog._share.module
		}, {
			field: "title",
			width: "300px",
			name: this._i18n.accesslog._share.title
		}, {
			field: "accessed_date",
			width: "200px",
			name: this._i18n.accesslog._share.accessedDate
		}, {
			field: "ip",
			width: "200px",
			name: this._i18n.accesslog._share.ip
		}];
		
		// Header menu
		var headerMenu = new dijit.Menu();
		headerMenu.addChild(new dijit.MenuItem({
			label: this._i18n.accesslog.list.showColumns,
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
			disabled: !core.js.base.controllers.ActionProvider.get("core_accesslog_delete").isAllowed,
			onClick: function(e) {
				var rowIndex = _this._accessLogGrid.selection.selectedIndex;
				var item = _this._accessLogGrid.getItem(rowIndex);
				if (item) {
					_this.onDeleteAccessLog(item);
				}
			}
		});
		cellMenu.addChild(this._deleteMenuItem);
		
		// "View" menu item
		this._viewMenuItem = new dijit.MenuItem({
			label: this._i18n.global._share.viewAction,
			disabled: !core.js.base.controllers.ActionProvider.get("core_accesslog_view").isAllowed,
			onClick: function(e) {
				var rowIndex = _this._accessLogGrid.selection.selectedIndex;
				var item = _this._accessLogGrid.getItem(rowIndex);
				if (item) {
					_this.onViewAccessLog(item);
				}
			}
		});
		cellMenu.addChild(this._viewMenuItem);
		
		// "Filter by IP" menu item
		this._filterByIpMenuItem = new dijit.MenuItem({
			label: this._i18n.accesslog.list.filterByIpAction,
			disabled: !core.js.base.controllers.ActionProvider.get("core_accesslog_list").isAllowed,
			onClick: function(e) {
				var rowIndex = _this._accessLogGrid.selection.selectedIndex;
				var item = _this._accessLogGrid.getItem(rowIndex);
				if (item) {
					_this.onFilterByIp(item.ip[0]);
				}
			}
		});
		cellMenu.addChild(this._filterByIpMenuItem);
		
		// Create grid
		this._accessLogGrid = new dojox.grid.EnhancedGrid({
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
			errorMessage: "<span class='dojoxGridAccessLog'>" + this._i18n.accesslog.list.error + "</span>",
			noDataMessage: "<span class='dojoxGridNoData'>" + this._i18n.accesslog.list.notFound + "</span>"
		}, dojo.create('div'));
		dojo.byId(this._id).appendChild(this._accessLogGrid.domNode);
		
		dojo.connect(this._accessLogGrid, "onRowContextMenu", function(e) {
			var item = this.getItem(e.rowIndex);
			if (item) {
				_this.onRowContextMenu(item);
			}
		});
	},
	
	showAccessLogs: function(/*Object*/ logs) {
		// summary:
		//		Shows the list of logs
		var store = new dojo.data.ItemFileReadStore({
			data: logs
		});
		dojo.style(this._accessLogGrid.domNode, {
			visibility: "visible"
		});
		this._accessLogGrid.setStore(store);
	},
	
	////////// UPDATE STATE OF CONTROLS //////////	
	
	allowToDelete: function(/*Boolean*/ isAllowed) {
		// summary:
		//		Allows/disallows to delete the access log
		isAllowed = isAllowed && core.js.base.controllers.ActionProvider.get("core_accesslog_delete").isAllowed;
		this._deleteMenuItem.set("disabled", !isAllowed);
		return this;	// core.js.views.AccessLogGrid
	},
	
	////////// CALLBACKS //////////
	
	onDeleteAccessLog: function(/*dojo.data.Item*/ item) {
		// summary:
		//		Deletes given access log item
		// tags:
		//		callback
	},
	
	onFilterByIp: function(/*String*/ ipAddress) {
		// summary:
		//		Filters the list of access logs by given IP address
		// ipAddress:
		//		The IP address
		// tags:
		//		callback
	},
	
	onRowContextMenu: function(/*dojo.data.Item*/ item) {
		// summary:
		//		Called when right-click on the access log item
		// tags:
		//		callback
	},
	
	onViewAccessLog: function(/*dojo.data.Item*/ item) {
		// summary:
		//		Views given access log item
		// tags:
		//		callback
	}
});
