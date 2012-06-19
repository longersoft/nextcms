/**
 * NextCMS
 * 
 * @author		Nguyen Huu Phuoc <thenextcms@gmail.com>
 * @copyright	Copyright (c) 2011 - 2012, Nguyen Huu Phuoc
 * @license		http://nextcms.org/license.txt	(GNU GPL version 2 or later)
 * @link		http://nextcms.org
 * @category	modules
 * @package		content
 * @subpackage	js
 * @since		1.0
 * @version		2011-10-18
 */

dojo.provide("content.js.views.RevisionGrid");

dojo.require("dijit.Menu");
dojo.require("dijit.MenuItem");
dojo.require("dijit.MenuSeparator");
dojo.require("dojo.data.ItemFileReadStore");
dojo.require("dojox.grid.EnhancedGrid");
dojo.require("dojox.grid.enhanced.plugins.Menu");
dojo.require("dojox.grid.enhanced.plugins.NestedSorting");
dojo.require("dojox.widget.PlaceholderMenuItem");

dojo.declare("content.js.views.RevisionGrid", null, {
	// _id: String
	_id: null,
	
	// _i18n: Object
	_i18n: null,
	
	// _revisionGrid: dojox.grid.EnhancedGrid
	_revisionGrid: null,
	
	// _viewMenuItem: dijit.MenuItem
	_viewMenuItem: null,
	
	// _deleteMenuItem: dijit.MenuItem
	_deleteMenuItem: null,
	
	// _restoreMenuItem: dijit.MenuItem
	_restoreMenuItem: null,
	
	constructor: function(/*String*/ id) {
		this._id = id;
		
		core.js.base.I18N.requireLocalization("content/languages");
		this._i18n = core.js.base.I18N.getLocalization("content/languages");
		
		this._createGrid();
	},
	
	_createGrid: function() {
		// summary:
		//		Create the grid
		
		var _this = this;
		
		// Columns
		var layout = [{
			field: "title",
			width: "200px",
			name: this._i18n.revision._share.title
		}, {
			field: "comment",
			width: "200px",
			name: this._i18n.revision._share.comment
		}, {
			field: "versioning_date",
			width: "150px",
			name: this._i18n.revision._share.createdDate
		}];
		
		// Header menu
		var headerMenu = new dijit.Menu();
		headerMenu.addChild(new dijit.MenuItem({
			label: this._i18n.revision.list.showColumns,
			disabled: true
		}));
		headerMenu.addChild(new dijit.MenuSeparator());
		headerMenu.addChild(new dojox.widget.PlaceholderMenuItem({
			label: "GridColumns"
		}));
		headerMenu.startup();
		
		// Cell context menu
		var cellMenu = new dijit.Menu();
		
		// "View" menu item
		this._viewMenuItem = new dijit.MenuItem({
			label: this._i18n.global._share.viewAction,
			disabled: !core.js.base.controllers.ActionProvider.get("content_revision_view").isAllowed,
			onClick: function(e) {
				var rowIndex = _this._revisionGrid.selection.selectedIndex;
				var item = _this._revisionGrid.getItem(rowIndex);
				if (item) {
					_this.onViewRevision(item);
				}
			}
		});
		cellMenu.addChild(this._viewMenuItem);
		
		// "Restore" menu item
		this._restoreMenuItem = new dijit.MenuItem({
			label: this._i18n.global._share.restoreAction,
			disabled: !core.js.base.controllers.ActionProvider.get("content_revision_restore").isAllowed,
			onClick: function(e) {
				var rowIndex = _this._revisionGrid.selection.selectedIndex;
				var item = _this._revisionGrid.getItem(rowIndex);
				if (item) {
					_this.onRestoreRevision(item);
				}
			}
		});
		cellMenu.addChild(this._restoreMenuItem);
		
		// "Delete" menu item
		this._deleteMenuItem = new dijit.MenuItem({
			label: this._i18n.global._share.deleteAction,
			iconClass: "appIcon appDeleteIcon",
			disabled: !core.js.base.controllers.ActionProvider.get("content_revision_delete").isAllowed,
			onClick: function(e) {
				var rowIndex = _this._revisionGrid.selection.selectedIndex;
				var item = _this._revisionGrid.getItem(rowIndex);
				if (item) {
					_this.onDeleteRevision(item);
				}
			}
		});
		cellMenu.addChild(this._deleteMenuItem);
		
		// Create grid
		this._revisionGrid = new dojox.grid.EnhancedGrid({
			clientSort: false,
			rowSelector: "20px",
			style: "height: 100%; width: 100%; visibility: hidden",
			structure: layout,
			plugins: {
				menus: {
					cellMenu: cellMenu
				},
				nestedSorting: true
			},
			headerMenu: headerMenu,
			loadingMessage: "<span class='dojoxGridLoading'>" + this._i18n.global._share.loadingAction  + "</span>",
			errorMessage: "<span class='dojoxGridError'>" + this._i18n.revision.list.error + "</span>",
			noDataMessage: "<span class='dojoxGridNoData'>" + this._i18n.revision.list.notFound + "</span>"
			// rowsPerPage: 40
		}, dojo.create('div'));
		dojo.byId(this._id).appendChild(this._revisionGrid.domNode);
		
		dojo.connect(this._revisionGrid, "onRowContextMenu", function(e) {
			var item = this.getItem(e.rowIndex);
			if (item) {
				_this.onRowContextMenu(item);
			}
		});
	},
	
	show: function(/*Object*/ criteria) {
		// summary:
		//		Shows the list of revisions
		// criteria:
		//		Contains the following members:
		//		- article_id [String]: ID of article
		//		- keyword [String]
		var params = {
			format: "json",
			article_id: criteria.article_id,
			keyword: criteria.keyword ? criteria.keyword : null, 
		};
		var url = core.js.base.controllers.ActionProvider.get("content_revision_list").url + "?" + dojo.objectToQuery(params);
		var store = new dojo.data.ItemFileReadStore({
			url: url
		});
		dojo.style(this._revisionGrid.domNode, {
			visibility: "visible"
		});
		this._revisionGrid.setStore(store);
	},
	
	////////// UPDATE STATE OF CONTROLS //////////
	
	allowToDelete: function(/*Boolean*/ isAllowed) {
		isAllowed = isAllowed && core.js.base.controllers.ActionProvider.get("content_revision_delete").isAllowed;
		this._deleteMenuItem.set("disabled", !isAllowed);
		return this;	// content.js.views.RevisionGrid
	},
	
	allowToRestore: function(/*Boolean*/ isAllowed) {
		isAllowed = isAllowed && core.js.base.controllers.ActionProvider.get("content_revision_restore").isAllowed;
		this._restoreMenuItem.set("disabled", !isAllowed);
		return this;	// content.js.views.RevisionGrid
	},
	
	////////// CALLBACKS //////////	
	
	onDeleteRevision: function(/*dojo.data.Item*/ item) {
		// summary:
		//		Deletes given revision item
		// tags:
		//		callback
	},
	
	onRestoreRevision: function(/*dojo.data.Item*/ item) {
		// summary:
		//		Restores given revision item
		// tags:
		//		callback
	},
	
	onRowContextMenu: function(/*dojo.data.Item*/ item) {
		// summary:
		//		Called when right-click on the revision item
		// tags:
		//		callback
	},
	
	onViewRevision: function(/*dojo.data.Item*/ item) {
		// summary:
		//		Views given revision item
		// tags:
		//		callback
	}
});
