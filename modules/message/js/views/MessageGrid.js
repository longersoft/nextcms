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

dojo.provide("message.js.views.MessageGrid");

dojo.require("dijit.Menu");
dojo.require("dijit.MenuItem");
dojo.require("dijit.MenuSeparator");
dojo.require("dojo.data.ItemFileReadStore");
dojo.require("dojox.grid.EnhancedGrid");
dojo.require("dojox.grid.enhanced.plugins.Menu");
dojo.require("dojox.widget.PlaceholderMenuItem");

dojo.require("core.js.base.controllers.ActionProvider");
dojo.require("core.js.base.I18N");

dojo.declare("message.js.views.MessageGrid", null, {
	// _id: String
	_id: null,
	
	// _i18n: Object
	_i18n: null,
	
	// _messageGrid: dojox.grid.EnhancedGrid
	_messageGrid: null,
	
	// _viewMenuItem: dijit.MenuItem
	_viewMenuItem: null,
	
	constructor: function(/*String*/ id) {
		this._id = id;
		
		core.js.base.I18N.requireLocalization("message/languages");
		this._i18n = core.js.base.I18N.getLocalization("message/languages");
		
		this._createGrid();
	},
	
	_createGrid: function() {
		// summary:
		//		Creates the grid
		var _this = this;
		
		// Columns of the grid
		var layout = [{
			field: "subject",
			width: "300px",
			name: this._i18n.message._share.subject,
			formatter: function(subject) {
				return '<span>' + subject + '</span>';
			}
		}, {
			field: "short_content",
			width: "300px",
			name: this._i18n.message._share.content
		}, {
			field: "from_user_name",
			width: "100px",
			name: this._i18n.message._share.fromAddress
		}, {
			field: "sent_date",
			width: "150px",
			name: this._i18n.message._share.sentDate
		}];
		
		// Header menu
		var headerMenu = new dijit.Menu();
		headerMenu.addChild(new dijit.MenuItem({
			label: this._i18n.message.list.showColumns,
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
			disabled: !core.js.base.controllers.ActionProvider.get("message_message_view").isAllowed,
			onClick: function(e) {
				var rowIndex = _this._messageGrid.selection.selectedIndex;
				var item = _this._messageGrid.getItem(rowIndex);
				if (item) {
					_this.onViewMessage(item);
				}
			}
		});
		cellMenu.addChild(this._viewMenuItem);
		
		// Create grid
		this._messageGrid = new dojox.grid.EnhancedGrid({
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
			errorMessage: "<span class='dojoxGridError'>" + this._i18n.message.list.error + "</span>",
			noDataMessage: "<span class='dojoxGridNoData'>" + this._i18n.message.list.notFound + "</span>"
		}, dojo.create('div'));
		dojo.byId(this._id).appendChild(this._messageGrid.domNode);
		
		dojo.connect(this._messageGrid, "onRowContextMenu", function(e) {
			var item = this.getItem(e.rowIndex);
			if (item) {
				_this.onRowContextMenu(item);
			}
		});
		
		// Show an icon if the private message has attachments
		dojo.connect(this._messageGrid, "onStyleRow", function(row) {
			var item = this.getItem(row.index);
			if (item) {
				// Find the cell showing the message's subject
				var subjectNode = dojo.query('.dojoxGridCell[idx="0"] span', row.node);
				if (item.has_attachments[0] == true) {
					subjectNode.addClass("messageHasAttachments");
				}
				
				// Style row to indicate unread message
				if (item.unread[0] + "" == "1") {
					dojo.query(".dojoxGridRowTable", row.node).addClass("messageUnreadRow");
					// row.customStyles += "font-weight: bold;";
				}
			}
		});
	},
	
	showMessages: function(/*Object*/ messages) {
		// summary:
		//		Shows the list of messages
		var store = new dojo.data.ItemFileReadStore({
			data: messages
		});
		dojo.style(this._messageGrid.domNode, {
			visibility: "visible"
		});
		this._messageGrid.setStore(store);
	},
	
	////////// CALLBACKS //////////
	
	onRowContextMenu: function(/*dojo.data.Item*/ item) {
		// summary:
		//		Called when right-click on the message item
		// tags:
		//		callback
	},
	
	onViewMessage: function(/*dojo.data.Item*/ item) {
		// summary:
		//		Views given message item
		// tags:
		//		callback
	}
});
