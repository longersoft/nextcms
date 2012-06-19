/**
 * NextCMS
 * 
 * @author		Nguyen Huu Phuoc <thenextcms@gmail.com>
 * @copyright	Copyright (c) 2011 - 2012, Nguyen Huu Phuoc
 * @license		http://nextcms.org/license.txt	(GNU GPL version 2 or later)
 * @link		http://nextcms.org
 * @category	modules
 * @package		poll
 * @subpackage	js
 * @since		1.0
 * @version		2011-10-18
 */

dojo.provide("poll.js.views.PollGrid");

dojo.require("dijit.Menu");
dojo.require("dijit.MenuItem");
dojo.require("dijit.MenuSeparator");
dojo.require("dijit.PopupMenuItem");
dojo.require("dojo.data.ItemFileReadStore");
dojo.require("dojox.grid.EnhancedGrid");
dojo.require("dojox.grid.enhanced.plugins.DnD");
dojo.require("dojox.grid.enhanced.plugins.Menu");
dojo.require("dojox.grid.enhanced.plugins.NestedSorting");
dojo.require("dojox.widget.PlaceholderMenuItem");

dojo.require("core.js.base.Config");
dojo.require("core.js.base.controllers.ActionProvider");
dojo.require("core.js.base.I18N");

dojo.declare("poll.js.views.PollGrid", null, {
	// _id: String
	_id: null,
	
	// _i18n: Object
	_i18n: null,
	
	// _pollGrid: dojox.grid.EnhancedGrid
	_pollGrid: null,
	
	// _editMenuItem: dijit.MenuItem
	_editMenuItem: null,
	
	// _deleteMenuItem: dijit.MenuItem
	_deleteMenuItem: null,
	
	constructor: function(/*String*/ id) {
		this._id = id;
		
		core.js.base.I18N.requireLocalization("poll/languages");
		this._i18n = core.js.base.I18N.getLocalization("poll/languages");
		
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
			name: this._i18n.poll._share.title
		}, {
			field: "created_date",
			width: "150px",
			name: this._i18n.poll._share.createdDate
		}, {
			field: "language",
			width: "150px",
			name: this._i18n.poll._share.language,
			formatter: function(language) {
				return languages ? language + " (" + languages[language] + ")" : language;
			}
		}];
		
		// Header menu
		var headerMenu = new dijit.Menu();
		headerMenu.addChild(new dijit.MenuItem({
			label: this._i18n.poll.list.showColumns,
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
			disabled: !core.js.base.controllers.ActionProvider.get("poll_poll_edit").isAllowed,
			onClick: function(e) {
				var rowIndex = _this._pollGrid.selection.selectedIndex;
				var item = _this._pollGrid.getItem(rowIndex);
				if (item) {
					_this.onEditPoll(item);
				}
			}
		});
		cellMenu.addChild(this._editMenuItem);
		
		// "Delete" menu item
		this._deleteMenuItem = new dijit.MenuItem({
			label: this._i18n.global._share.deleteAction,
			iconClass: "appIcon appDeleteIcon",
			disabled: !core.js.base.controllers.ActionProvider.get("poll_poll_delete").isAllowed,
			onClick: function(e) {
				var rowIndex = _this._pollGrid.selection.selectedIndex;
				var item = _this._pollGrid.getItem(rowIndex);
				if (item) {
					_this.onDeletePoll(item);
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
						var rowIndex = _this._pollGrid.selection.selectedIndex;
						var item = _this._pollGrid.getItem(rowIndex);
						if (item) {
							var translations = dojo.fromJson(item.translations[0]);
							if (translations[this.__locale]) {
								// Edit the item
								_this.onEditPoll(translations[this.__locale]);
							} else {
								// Create a copy in other language
								_this.onTranslatePoll(item, this.__locale);
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
		this._pollGrid = new dojox.grid.EnhancedGrid({
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
			errorMessage: "<span class='dojoxGridError'>" + this._i18n.poll.list.error + "</span>",
			noDataMessage: "<span class='dojoxGridNoData'>" + this._i18n.poll.list.notFound + "</span>"
			// rowsPerPage: 40
		}, dojo.create('div'));
		dojo.byId(this._id).appendChild(this._pollGrid.domNode);
		
		dojo.connect(this._pollGrid, "onRowContextMenu", function(e) {
			var item = this.getItem(e.rowIndex);
			if (item) {
				_this.onRowContextMenu(item);
			}
		});
	},
	
	showPolls: function(/*Object*/ polls) {
		// summary:
		//		Shows the list of polls
		// polls:
		//		Array of polls, formatted as dojo.data.ItemFileReadStore data structure
		var store = new dojo.data.ItemFileReadStore({
			data: polls
		});
		dojo.style(this._pollGrid.domNode, {
			visibility: "visible"
		});
		this._pollGrid.setStore(store);
	},
	
	////////// UPDATE STATE OF CONTROLS //////////
	
	allowToDelete: function(/*Boolean*/ isAllowed) {
		// summary:
		//		Allows/disallows to delete the poll
		isAllowed = isAllowed && core.js.base.controllers.ActionProvider.get("poll_poll_delete").isAllowed;
		this._deleteMenuItem.set("disabled", !isAllowed);
		return this;	// poll.js.views.PollGrid
	},
	
	allowToEdit: function(/*Boolean*/ isAllowed) {
		// summary:
		//		Allows/disallows to edit the poll
		isAllowed = isAllowed && core.js.base.controllers.ActionProvider.get("poll_poll_edit").isAllowed;
		this._editMenuItem.set("disabled", !isAllowed);
		return this;	// poll.js.views.PollGrid
	},
	
	////////// CALLBACKS //////////
	
	onDeletePoll: function(/*dojo.data.Item*/ item) {
		// summary:
		//		Delete given poll item
		// tags:
		//		callback
	},
	
	onEditPoll: function(/*dojo.data.Item|String*/ item) {
		// summary:
		//		Edits given poll item
		// tags:
		//		callback
	},
	
	onRowContextMenu: function(/*dojo.data.Item*/ item) {
		// summary:
		//		Called when right-click on the poll item
		// tags:
		//		callback
	},
	
	onTranslatePoll: function(/*dojo.data.Item*/ item, /*String*/ language) {
		// summary:
		//		Translates given item to other language
		// tags:
		//		callback
	}
});
