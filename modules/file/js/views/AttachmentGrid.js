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
 * @version		2012-06-11
 */

dojo.provide("file.js.views.AttachmentGrid");

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
dojo.require("core.js.base.I18N");
dojo.require("file.js.views.FileFormatter");

dojo.declare("file.js.views.AttachmentGrid", null, {
	// _id: String
	_id: null,
	
	// _i18n: Object
	_i18n: null,
	
	// _attachmentGrid: dojox.grid.EnhancedGrid
	_attachmentGrid: null,
	
	// _editMenuItem: dijit.MenuItem
	_editMenuItem: null,
	
	// _deleteMenuItem: dijit.MenuItem
	_deleteMenuItem: null,
	
	constructor: function(/*String*/ id) {
		this._id = id;
		
		core.js.base.I18N.requireLocalization("file/languages");
		this._i18n = core.js.base.I18N.getLocalization("file/languages");
		
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
			name: this._i18n.attachment._share.title,
			formatter: function(title) {
				return '<span>' + title + '</span>';
			}
		}, {
			field: "slug",
			width: "300px",
			name: this._i18n.attachment._share.slug
		}, {
			field: "hash",
			width: "300px",
			name: this._i18n.attachment._share.hash
		}, {
			field: "size",
			width: "150px",
			name: this._i18n.attachment._share.size,
			formatter: function(size) {
				return file.js.views.FileFormatter.formatSize(size, "0 byte");
			},
			styles: "text-align: right;"
		}, {
			field: "uploaded_date",
			width: "150px",
			name: this._i18n.attachment._share.uploadedDate
		}, {
			field: "language",
			width: "150px",
			name: this._i18n.attachment._share.language,
			formatter: function(language) {
				return languages ? language + " (" + languages[language] + ")" : language;
			}
		}, {
			field: "num_downloads",
			width: "150px",
			name: this._i18n.attachment._share.numDownloads
		}];
		
		// Header menu
		var headerMenu = new dijit.Menu();
		headerMenu.addChild(new dijit.MenuItem({
			label: this._i18n.attachment.list.showColumns,
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
			disabled: !core.js.base.controllers.ActionProvider.get("file_attachment_edit").isAllowed,
			onClick: function(e) {
				var rowIndex = _this._attachmentGrid.selection.selectedIndex;
				var item = _this._attachmentGrid.getItem(rowIndex);
				if (item) {
					_this.onEditAttachment(item);
				}
			}
		});
		cellMenu.addChild(this._editMenuItem);
		
		// "Delete" menu item
		this._deleteMenuItem = new dijit.MenuItem({
			label: this._i18n.global._share.deleteAction,
			iconClass: "appIcon appDeleteIcon",
			disabled: !core.js.base.controllers.ActionProvider.get("file_attachment_delete").isAllowed,
			onClick: function(e) {
				var rowIndex = _this._attachmentGrid.selection.selectedIndex;
				var item = _this._attachmentGrid.getItem(rowIndex);
				if (item) {
					_this.onDeleteAttachment(item);
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
						var rowIndex = _this._attachmentGrid.selection.selectedIndex;
						var item = _this._attachmentGrid.getItem(rowIndex);
						if (item) {
							var translations = dojo.fromJson(item.translations[0]);
							if (translations[this.__locale]) {
								_this.onEditAttachment(translations[this.__locale]);
							} else {
								_this.onTranslateAttachment(item, this.__locale);
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
		this._attachmentGrid = new dojox.grid.EnhancedGrid({
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
			errorMessage: "<span class='dojoxGridError'>" + this._i18n.attachment.list.error + "</span>",
			noDataMessage: "<span class='dojoxGridNoData'>" + this._i18n.attachment.list.notFound + "</span>"
			// rowsPerPage: 40
		}, dojo.create('div'));
		dojo.byId(this._id).appendChild(this._attachmentGrid.domNode);
		
		dojo.connect(this._attachmentGrid, "onRowContextMenu", function(e) {
			var item = this.getItem(e.rowIndex);
			if (item) {
				_this.onRowContextMenu(item);
			}
		});
		
		// Show an icon to indicate password protected attachment
		dojo.connect(this._attachmentGrid, "onStyleRow", function(row) {
			var item = this.getItem(row.index);
			if (item) {
				// Find the cell showing the attachment's title
				var attachmentNameNode = dojo.query('.dojoxGridCell[idx="0"] span', row.node);
				if (item.password_required[0] == true) {
					attachmentNameNode.addClass("fileAttachmentPasswordRequired");
				}
			}
		});
	},
	
	showAttachments: function(/*Object*/ attachments) {
		// summary:
		//		Shows the list of attachments
		var store = new dojo.data.ItemFileReadStore({
			data: attachments
		});
		dojo.style(this._attachmentGrid.domNode, {
			visibility: "visible"
		});
		this._attachmentGrid.setStore(store);
	},
	
	////////// UPDATE STATE OF CONTROLS //////////
	
	allowToDelete: function(/*Boolean*/ isAllowed) {
		// summary:
		//		Allows/disallows to delete the attachment
		isAllowed = isAllowed && core.js.base.controllers.ActionProvider.get("file_attachment_delete").isAllowed;
		this._deleteMenuItem.set("disabled", !isAllowed);
		return this;	// file.js.views.AttachmentGrid
	},
	
	allowToEdit: function(/*Boolean*/ isAllowed) {
		// summary:
		//		Allows/disallows to edit the attachment
		isAllowed = isAllowed && core.js.base.controllers.ActionProvider.get("file_attachment_edit").isAllowed;
		this._editMenuItem.set("disabled", !isAllowed);
		return this;	// file.js.views.AttachmentGrid
	},
	
	////////// CALLBACKS //////////
	
	onDeleteAttachment: function(/*dojo.data.Item*/ item) {
		// summary:
		//		Deletes given attachment
		// tags:
		//		callback
	},
	
	onEditAttachment: function(/*dojo.data.Item|String*/ item) {
		// summary:
		//		Edits given attachment
		// tags:
		//		callback
	},
	
	onRowContextMenu: function(/*dojo.data.Item*/ item) {
		// summary:
		//		Called when right-click on attachment item
		// tags:
		//		callback
	},
	
	onTranslateAttachment: function(/*dojo.data.Item*/ item, /*String*/ language) {
		// summary:
		//		Translates given attachment item to other language
		// tags:
		//		callback
	}
});
