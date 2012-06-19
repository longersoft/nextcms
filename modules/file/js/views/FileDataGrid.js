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
 * @version		2011-10-18
 */

dojo.provide("file.js.views.FileDataGrid");

dojo.require("dijit.Menu");
dojo.require("dijit.MenuItem");
dojo.require("dijit.MenuSeparator");
dojo.require("dojox.grid.EnhancedGrid");
dojo.require("dojox.grid.enhanced.plugins.DnD");
dojo.require("dojox.grid.enhanced.plugins.Menu");
dojo.require("dojox.grid.enhanced.plugins.NestedSorting");
dojo.require("dojox.widget.PlaceholderMenuItem");

dojo.require("core.js.base.controllers.ActionProvider");
dojo.require("core.js.base.I18N");
dojo.require("file.js.views.FileFormatter");

dojo.declare("file.js.views.FileDataGrid", null, {
	// _id: String
	_id: null,
	
	// _i18n: Object
	_i18n: null,
	
	// _fileGrid: dojox.grid.EnhancedGrid
	_fileGrid: null,
	
	// _connectionId: String
	_connectionId: null,
	
	// _defaultCriteria: Object
	_defaultCriteria: {
		files_only: false,
		hidden_files: false,
		name: "",
		case_sensitive: false,
		regular_expression: false,
		recurse: false
	},
	
	// _searchCriteria: Object
	_searchCriteria: null,
	
	////////// MENU ITEMS OF CELL'S CONTEXT MENU //////////
	
	// _createDirectoryMenuItem: dijit.MenuItem
	_createDirectoryMenuItem: null,
	
	// _bookmarkMenuItem: dijit.MenuItem
	_bookmarkMenuItem: null,
	
	// _cutMenuItem: dijit.MenuItem
	_cutMenuItem: null,
	
	// _copyMenuItem: dijit.MenuItem
	_copyMenuItem: null,
	
	// _pasteMenuItem: dijit.MenuItem
	_pasteMenuItem: null,
	
	// _pasteWithoutOverwritingMenuItem: dijit.MenuItem
	_pasteWithoutOverwritingMenuItem: null,
	
	// _editMenuItem: dijit.MenuItem
	_editMenuItem: null,
	
	// _viewMenuItem: dijit.MenuItem
	_viewMenuItem: null,
	
	// _compressMenuItem: dijit.MenuItem
	_compressMenuItem: null,
	
	// _extractMenuItem: dijit.MenuItem
	_extractMenuItem: null,
	
	// _downloadMenuItem: dijit.MenuItem
	_downloadMenuItem: null,
	
	// _uploadMenuItem: dijit.MenuItem
	_uploadMenuItem: null,
	
	constructor: function(/*String*/ id) {
		this._id = id;
		
		// Init the search criteria
		this._searchCriteria = this._defaultCriteria;
		
		core.js.base.I18N.requireLocalization("file/languages");
		this._i18n = core.js.base.I18N.getLocalization("file/languages");
		
		this._init();
	},
	
	_init: function() {
		// summary:
		//		Creates the grid
		var _this = this;
		
		// Columns
		var layout = [{
			field: "name",
			width: "auto",
			name: this._i18n.explorer._share.fileName,
			formatter: function(name) {
				return '<span>' + name + '</span>';
			}
		}, {
			field: "readableModified",
			width: "150px",
			name: this._i18n.explorer._share.modifiedDate
		}, {
			field: "extension",
			width: "auto",
			name: this._i18n.explorer._share.fileType,
			formatter: function(extension) {
				extension = extension.toLowerCase();
				var translated = _this._i18n.explorer._share.extensions[extension];
				return (translated)
						? translated
						: dojox.string.sprintf(_this._i18n.explorer._share.extensions._unknown, extension.toUpperCase());		// Unknown extension
			}
		}, {
			field: "size",
			width: "150px",
			name: this._i18n.explorer._share.fileSize,
			formatter: file.js.views.FileFormatter.formatSize,
			styles: "text-align: right;"		// DOJO LESSON: Don't forget to put the ; at the end
		}, {
			field: "perms",
			width: "150px",
			name: this._i18n.explorer._share.filePermissions,
			formatter: function(perms) {
				return perms + " (" + file.js.views.FileFormatter.formatPermissions(perms) + ")";
				// return file.js.views.FileFormatter.formatPermissions(perms);
			}
		}, {
			field: "path",
			width: "auto",
			name: this._i18n.explorer._share.filePath
		}];
		
		// DOJO LESSON: Create a context menu in the header that allows to show/hide the columns
		var headerMenu = new dijit.Menu();
		headerMenu.addChild(new dijit.MenuItem({
			label: this._i18n.explorer.list.showColumns,
			disabled: true
		}));
		headerMenu.addChild(new dijit.MenuSeparator());
		headerMenu.addChild(new dojox.widget.PlaceholderMenuItem({
			label: "GridColumns"
		}));
		headerMenu.startup();
		
		// Cell context menu
		var cellMenu = new dijit.Menu();
		
		// "Create directory" menu item
		this._createDirectoryMenuItem = new dijit.MenuItem({
			label: this._i18n.explorer._share.createDirectoryAction,
			iconClass: "appIcon fileExplorerAddFolderIcon",
			disabled: true,
			onClick: function(e) {
				var items = _this.getSelectedItems();
				if (items.length > 0) {
					_this.onCreateDirectory(items[0]);
				}
			}
		});
		cellMenu.addChild(this._createDirectoryMenuItem);
		
		// "Open in the directory" menu item
		// Add a menu item that allows to open the containing directory of the selected file item.
		// It is useful when I want to locate the file after searching.
		cellMenu.addChild(new dijit.MenuItem({
			label: this._i18n.explorer.list.openContainingDirectory,
			iconClass: "appIcon fileExplorerOpenFolderIcon",
			disabled: !core.js.base.controllers.ActionProvider.get("file_explorer_list").isAllowed,
			onClick: function(e) {
				var items = _this.getSelectedItems();
				if (items.length > 0) {
					_this.onOpenDirectory(items[0].directory ? items[0].path : items[0].parentDir);
				}
			}
		}));
		
		// Bookmark menu item
		this._bookmarkMenuItem = new dijit.MenuItem({
			label: this._i18n.bookmark._share.bookmarkAction,
			iconClass: "appIcon appBookmarkIcon",
			disabled: !core.js.base.controllers.ActionProvider.get("file_bookmark_add").isAllowed,
			onClick: function(e) {
				var items = _this.getSelectedItems();
				if (items.length > 0 && items[0].directory) {
					_this.onBookmarkDirectory(items[0]);
				}
			}
		});
		cellMenu.addChild(this._bookmarkMenuItem);
		
		cellMenu.addChild(new dijit.MenuSeparator());
		
		// Cut, copy, paste menu items
		this._cutMenuItem = new dijit.MenuItem({
			label: this._i18n.global._share.cutAction,
	    	disabled: !core.js.base.controllers.ActionProvider.get("file_explorer_move").isAllowed,
			onClick: function(e) {
				var items = _this.getSelectedItems();
				if (items.length > 0) {
					_this.onCutFile(items[0]);
				}
			}
		});
		cellMenu.addChild(this._cutMenuItem);
		
		this._copyMenuItem = new dijit.MenuItem({
			label: this._i18n.global._share.copyAction,
			iconClass: "appIcon appCopyIcon",
			disabled: !core.js.base.controllers.ActionProvider.get("file_explorer_copy").isAllowed,
			onClick: function(e) {
				var items = _this.getSelectedItems();
				if (items.length > 0) {
					_this.onCopyFile(items[0]);
				}
			}
		});
		cellMenu.addChild(this._copyMenuItem);
		
		this._pasteMenuItem = new dijit.MenuItem({
			label: this._i18n.global._share.pasteAction,
			iconClass: "appIcon appPasteIcon",
			disabled: true,
			onClick: function(e) {
				var items = _this.getSelectedItems();
				if (items.length > 0) {
					_this.onPasteFile(items[0], true);
				}
			}
		});
		cellMenu.addChild(this._pasteMenuItem);
		
		this._pasteWithoutOverwritingMenuItem = new dijit.MenuItem({
			label: this._i18n.global._share.pasteWithoutOverwritingAction,
			iconClass: "appIcon appPasteIcon",
			disabled: true,
			onClick: function(e) {
				var items = _this.getSelectedItems();
				if (items.length > 0) {
					_this.onPasteFile(items[0], false);
				}
			}
		});
		cellMenu.addChild(this._pasteWithoutOverwritingMenuItem);
		
		cellMenu.addChild(new dijit.MenuSeparator());
		
		// "Edit" menu item
		this._editMenuItem = new dijit.MenuItem({
			label: this._i18n.global._share.editAction,
			iconClass: "appIcon fileExplorerTextEditorIcon",
			disabled: true,
			onClick: function(e) {
				var items = _this.getSelectedItems();
				if (items.length > 0) {
					_this.onEditFile(items[0]);
				}
			}
		});
		cellMenu.addChild(this._editMenuItem);
		
		// "View" menu item
		this._viewMenuItem = new dijit.MenuItem({
			label: this._i18n.global._share.viewAction,
			disabled: true,
			onClick: function(e) {
				var items = _this.getSelectedItems();
				if (items.length > 0) {
					_this.onViewFile(items[0]);
				}
			}
		});
		cellMenu.addChild(this._viewMenuItem);
		
		// "Delete" menu item
		cellMenu.addChild(new dijit.MenuItem({
			label: this._i18n.global._share.deleteAction,
			iconClass: "appIcon appDeleteIcon",
			disabled: !core.js.base.controllers.ActionProvider.get("file_explorer_delete").isAllowed,
			onClick: function(e) {
				var items = _this.getSelectedItems();
				if (items.length > 0) {
					_this.onDeleteFile(items[0]);
				}
			}
		}));
		
		// "Rename" menu item
		cellMenu.addChild(new dijit.MenuItem({
			label: this._i18n.global._share.renameAction,
			iconClass: "appIcon appRenameIcon",
			disabled: !core.js.base.controllers.ActionProvider.get("file_explorer_rename").isAllowed,
			onClick: function(e) {
				var items = _this.getSelectedItems();
				if (items.length > 0) {
					_this.onRenameFile(items[0]);
				}
			}
		}));
		
		cellMenu.addChild(new dijit.MenuSeparator());
		
		// "Compress" menu item
		this._compressMenuItem = new dijit.MenuItem({
			label: this._i18n.global._share.compressAction,
			iconClass: "appIcon fileExplorerCompressIcon",
			disabled: !core.js.base.controllers.ActionProvider.get("file_explorer_compress").isAllowed,
			onClick: function(e) {
				var items = _this.getSelectedItems();
				if (items.length > 0) {
					_this.onCompressFile(items[0]);
				}
			}
		});
		cellMenu.addChild(this._compressMenuItem);
		
		// "Extract" menu items
		this._extractMenuItem = new dijit.MenuItem({
			label: this._i18n.global._share.extractAction,
			iconClass: "appIcon fileExplorerExtractIcon",
			disabled: !core.js.base.controllers.ActionProvider.get("file_explorer_extract").isAllowed,
			onClick: function(e) {
				var items = _this.getSelectedItems();
				if (items.length > 0 && !items[0].directory) {
					_this.onExtractFile(items[0]);
				}
			}
		});
		cellMenu.addChild(this._extractMenuItem);
		
		cellMenu.addChild(new dijit.MenuSeparator());
		
		// "Download" menu item
		this._downloadMenuItem = new dijit.MenuItem({
			label: this._i18n.global._share.downloadAction,
			iconClass: "appIcon appDownloadIcon",
			disabled: !core.js.base.controllers.ActionProvider.get("file_explorer_download").isAllowed,
			onClick: function(e) {
				var items = _this.getSelectedItems();
				if (items.length > 0 && !items[0].directory) {
					_this.onDownloadFile(items[0]);
				}
			}
		});
		cellMenu.addChild(this._downloadMenuItem);
		
		// "Upload" menu item
		this._uploadMenuItem = new dijit.MenuItem({
			label: this._i18n.global._share.uploadAction,
			iconClass: "appIcon appUploadIcon",
			disabled: !core.js.base.controllers.ActionProvider.get("file_explorer_upload").isAllowed,
			onClick: function(e) {
				var items = _this.getSelectedItems();
				if (items.length > 0 && items[0].directory) {
					_this.onUploadFile(items[0]);
				}
			}
		});
		cellMenu.addChild(this._uploadMenuItem);
		
		// "Change permissions" menu item
		cellMenu.addChild(new dijit.MenuItem({
			label: this._i18n.explorer._share.changePermissionsAction,
			disabled: !core.js.base.controllers.ActionProvider.get("file_explorer_perm").isAllowed,
			onClick: function(e) {
				var items = _this.getSelectedItems();
				if (items.length > 0) {
					_this.onChangePermissions(items[0]);
				}
			}
		}));
		
		// Create grid
		this._fileGrid = new dojox.grid.EnhancedGrid({
			clientSort: false,
			rowSelector: "20px",
			// DOJO LESSON: In order to hide the grid, I have to set visibility style to hidden.
			// The grid will not be hidden if I set the display attribute of the DomNode to "none".
			style: "height: 100%; width: 100%; visibility: hidden",
			structure: layout,
			plugins: {
				dnd: true,
//				{
//					col: {
//						within: false,
//						"in": false,
//						out: false
//					},
//					cell: {
//						within: false,
//						"in": false,
//						out: false
//					}
//				},
				menus: {
					cellMenu: cellMenu
				},
				nestedSorting: true
			},
			headerMenu: headerMenu,
			loadingMessage: "<span class='dojoxGridLoading'>" + this._i18n.global._share.loadingAction  + "</span>",
			errorMessage: "<span class='dojoxGridError'>" + this._i18n.explorer.list.error + "</span>",
			noDataMessage: "<span class='dojoxGridNoData'>" + this._i18n.explorer.list.fileNotFound + "</span>"
			// rowsPerPage: 40
		}, dojo.create('div'));
		dojo.byId(this._id).appendChild(this._fileGrid.domNode);
		
		// Open the directory if the associated item has type of directory
		dojo.connect(this._fileGrid, "onRowDblClick", function(e) {
			var item = this.getItem(e.rowIndex);
			if (item.directory && e.cell.field == "name") {
				_this.onOpenDirectory(item.path);
			}
		});
		
		// Change the cursor when moving over the directory
		dojo.connect(this._fileGrid, "onMouseOver", function(e) {
			var item = this.getItem(e.rowIndex);
			if (item && item.directory && e.cell.field == "name") {
				dojo.style(e.cellNode, {
					cursor: "pointer"
				});
			}
		});
		
		// Style the row based on the type of file (directory or file)
		dojo.connect(this._fileGrid, "onStyleRow", function(row) {
			var item = this.getItem(row.index);
			if (item) {
				row.customClasses += item.directory ? " fileExplorerListFolderRow" : " fileExplorerListFileRow";
				// Style the cell that shows the file name
				var fileNameNode = dojo.query('.dojoxGridCell[idx="0"] span', row.node);
				fileNameNode.addClass("fileExplorerListFileNameCell");
				
				var extension  = item.extension.toLowerCase();
				var translated = _this._i18n.explorer._share.extensions[extension];
				var fileType   = translated ? extension : "_file";
				fileNameNode.style("background-image", "url(" + dojo.moduleUrl("file", "images/files/" + fileType + ".png").uri + ")");
			}
		});
		
		dojo.connect(this._fileGrid, "onRowContextMenu", function(e) {
			var item = this.getItem(e.rowIndex);
			if (item) {
				_this.onRowContextMenu(item);
			}
		});
	},
	
	getSelectedItems: function() {
		// summary:
		//		Gets the selected items
		
		// DOJO LESSON: To get the indexes of the selected rows of an Enhanced DataGrid widget correctly, 
		// please be aware of the different results when the Dnd plugin is used or not.
		// 1) If the Dnd plugin is used:
		// - If I don't select a row (by clicking on the row selector cell):
		//		this._fileGrid.selection.selectedIndex						=> -1
		//		this._fileGrid.plugin("selector").getSelected("cell")		=> Returns an array, which each item contains: col, id, row
		//		this._fileGrid.plugin("selector").getSelected("row")		=> []
		// - If a row is already selected:
		//		this._fileGrid.selection.selectedIndex						=> Index of the selected
		//		this._fileGrid.plugin("selector").getSelected("cell")		=> []
		//		this._fileGrid.plugin("selector").getSelected("row")		=> Returns an array, which each item is index of a selected row
		//
		// 2) If Dnd plugin is not used:
		//		this._fileGrid.selection.selectedIndex						=> Index of selected row
		//		this._fileGrid.plugin("selector")							=> null
		
		var selectedRowIndexes = [];
		if (this._fileGrid.plugin("dnd") == null) {
			selectedRowIndexes.push(this._fileGrid.selection.selectedIndex);
		} else {
			var selected = this._fileGrid.plugin("selector")._selected;
			if (selected.cell.length > 0) {
				dojo.forEach(selected.cell, function(cellData, index) {
					selectedRowIndexes.push(cellData.row);
				});
			} else if (selected.row.length > 0) {
				dojo.forEach(selected.row, function(rowData, index) {
					selectedRowIndexes.push(rowData.row);
				});
			}
		}

		var _this = this;
		var selectedItems = [];
		dojo.forEach(selectedRowIndexes, function(rowIndex, index) {
			selectedItems[index] = _this._fileGrid.getItem(rowIndex);
		});
		return selectedItems;	// Array
	},
	
	disconnect: function() {
		// summary:
		//		Disconnects. It should be called after disconnecting
		// Empty the list
		var params = {
			empty: true
		};
		var url = core.js.base.controllers.ActionProvider.get("file_explorer_list").url + "?" + dojo.objectToQuery(params);
		var store = new dojox.data.FileStore({
			url: url,
			pathAsQueryParam: true
		});
		this._fileGrid.setStore(store);
	},
	
	setConnectionId: function(/*String*/ connectionId) {
		this._connectionId = connectionId;
	},
	
	resetSearchCriteria: function() {
		this.setSearchCriteria(this._defaultCriteria);
	},
	
	setSearchCriteria: function(/*Object*/ searchCriteria) {
		dojo.mixin(this._searchCriteria, searchCriteria);
	},
	
	show: function(/*String*/ path) {
		// summary:
		//		Show the list of files
		var url = core.js.base.controllers.ActionProvider.get("file_explorer_list").url;
		var params = {
			format: "json",
			dirs_only: null,
			target: "datagrid",
			connection_id: this._connectionId
		};
		dojo.mixin(params, this._searchCriteria);
		params.dirs_only = params.files_only ? false : null;
		
		if (path) {
			params.path = path;
		}
		url += "?" + dojo.objectToQuery(params);
		var store = new dojox.data.FileStore({
			url: url,
			pathAsQueryParam: true
		});
		dojo.style(this._fileGrid.domNode, {
			visibility: "visible"
		});
		this._fileGrid.setStore(store);
	},
	
	////////// UPDATE STATE OF CONTROLS //////////

	allowToBookmark: function(/*Boolean*/ isAllowed) {
		// summary:
		//		Allows/disallows to bookmark file
		var _this = this;
		isAllowed = isAllowed && core.js.base.controllers.ActionProvider.get("file_bookmark_add").isAllowed;
		this._bookmarkMenuItem.set("label", this._i18n.bookmark._share.bookmarkAction);
		this._bookmarkMenuItem.set("disabled", !isAllowed);
		this._bookmarkMenuItem.onClick = function(e) {
			var items = _this.getSelectedItems();
			if (items.length > 0 && items[0].directory) {
				_this.onBookmarkDirectory(items[0]);
			}
		};
		return this;	// file.js.views.FileDataGrid
	},
	
	allowToCompress: function(/*Boolean*/ isAllowed) {
		// summary:
		//		Allows/disallows to compress file
		isAllowed = isAllowed && core.js.base.controllers.ActionProvider.get("file_explorer_compress").isAllowed;
		this._compressMenuItem.set("disabled", !isAllowed);
		return this;	// file.js.views.FileDataGrid
	},
	
	allowToCreateDirectory: function(/*Boolean*/ isAllowed) {
		// summary:
		//		Allows/disallows to create new directory
		isAllowed = isAllowed && core.js.base.controllers.ActionProvider.get("file_explorer_add").isAllowed;
		this._createDirectoryMenuItem.set("disabled", !isAllowed);
		return this;	// file.js.views.FileDataGrid
	},
	
	allowToDownload: function(/*Boolean*/ isAllowed) {
		// summary:
		//		Allows/disallows to download the file
		isAllowed = isAllowed && core.js.base.controllers.ActionProvider.get("file_explorer_download").isAllowed;
		this._downloadMenuItem.set("disabled", !isAllowed);
		return this;	// file.js.views.FileDataGrid
	},
	
	allowToEdit: function(/*Boolean*/ isAllowed) {
		// summary:
		//		Allows/disallows to edit the file
		isAllowed = isAllowed && core.js.base.controllers.ActionProvider.get("file_explorer_edit").isAllowed;
		this._editMenuItem.set("disabled", !isAllowed);
		return this;	// file.js.views.FileDataGrid
	},
	
	allowToExtract: function(/*Boolean*/ isAllowed) {
		// summary:
		//		Allows/disallows to extract compressed file
		isAllowed = isAllowed && core.js.base.controllers.ActionProvider.get("file_explorer_extract").isAllowed;
		this._extractMenuItem.set("disabled", !isAllowed);
		return this;	// file.js.views.FileDataGrid
	},
	
	allowToPaste: function(/*Boolean*/ isAllowed, /*String*/ action) {
		// summary:
		//		Allows/disallows to copy/move file
		// action:
		//		Can be "copy" or "move"
		isAllowed = isAllowed && ((action == "copy") 
									? core.js.base.controllers.ActionProvider.get("file_explorer_copy").isAllowed
									: core.js.base.controllers.ActionProvider.get("file_explorer_move").isAllowed);
		this._pasteMenuItem.set("disabled", !isAllowed);
		this._pasteWithoutOverwritingMenuItem.set("disabled", !isAllowed);
		return this;	// file.js.views.FileDataGrid
	},
	
	allowToUnbookmark: function() {
		// summary:
		//		Allows/disallows to remove bookmark of file
		var _this = this;
		var isAllowed = core.js.base.controllers.ActionProvider.get("file_bookmark_delete").url;
		this._bookmarkMenuItem.set("label", this._i18n.bookmark._share.unbookmarkAction);
		this._bookmarkMenuItem.set("disabled", !isAllowed);
		this._bookmarkMenuItem.onClick = function(e) {
			var items = _this.getSelectedItems();
			if (items.length > 0 && items[0].directory) {
				_this.onUnbookmarkDirectory(items[0]);
			}
		};
		return this;	// file.js.views.FileDataGrid
	},
	
	allowToUpload: function(/*Boolean*/ isAllowed) {
		// summary:
		//		Allows/disallows to upload file
		isAllowed = isAllowed && core.js.base.controllers.ActionProvider.get("file_explorer_upload").isAllowed;
		this._uploadMenuItem.set("disabled", !isAllowed);
		return this;	// file.js.views.FileDataGrid
	},
	
	allowToView: function(/*Boolean*/ isAllowed) {
		// summary:
		//		Allows/disallows to view file
		isAllowed = isAllowed && core.js.base.controllers.ActionProvider.get("file_explorer_view").isAllowed;
		this._viewMenuItem.set("disabled", !isAllowed);
		return this;	// file.js.views.FileDataGrid
	},
	
	////////// CALLBACKS //////////

	onBookmarkDirectory: function(/*dojo.data.Item*/ item) {
		// tags:
		//		callback
	},
	
	onChangePermissions: function(/*dojo.data.Item*/ item) {
		// tags:
		//		callback
	},
	
	onCompressFile: function(/*dojo.data.Item*/ item) {
		// tags:
		//		callback
	},
	
	onCopyFile: function(/*dojo.data.Item*/ sourceItem) {
		// tags:
		//		callback
	},
	
	onCreateDirectory: function(/*dojo.data.Item*/ item) {
		// tags:
		//		callback
	},
	
	onCutFile: function(/*dojo.data.Item*/ sourceItem) {
		// tags:
		//		callback
	},
	
	onDeleteFile: function(/*dojo.data.Item*/ item) {
		// summary:
		//		This method is called when the Delete menu item is clicked
		// tags:
		//		callback
	},
	
	onDownloadFile: function(/*dojo.data.Item*/ item) {
		// summary:
		//		This method is called when the Download menu item is clicked
		// tags:
		//		callback
	},
	
	onEditFile: function(/*dojo.data.Item*/ item) {
		// summary:
		//		This method is called when the Edit menu item is clicked
		// tags:
		//		callback
	},
	
	onExtractFile: function(/*dojo.data.Item*/ item) {
		// tags:
		//		callback
	},
	
	onOpenDirectory: function(/*String*/ path) {
		// summary:
		//		This method is called when user click on a directory cell in the list
		// tags:
		//		callback
	},
	
	onPasteFile: function(/*dojo.data.Item*/ targetItem, /*Boolean*/ overwrite) {
		// summary:
		//		This method is called when the Paste menu item is clicked
		// overwrite:
		//		If true, the action will overwrite the existent files
		// tags:
		//		callback
	},
	
	onRenameFile: function(/*dojo.data.Item*/ item) {
		// summary:
		//		This method is called when user click on Rename menu item from the context menu
		// tags:
		//		callback
	},
	
	onRowContextMenu: function(/*dojo.data.Item*/ item) {
		// tags:
		//		callback
	},
	
	onUnbookmarkDirectory: function(/*dojo.data.Item*/ item) {
		// tags:
		//		callback
	},
	
	onUploadFile: function(/*dojo.data.Item*/ item) {
		// summary:
		//		This method is called when the Upload menu item is clicked
		// tags:
		//		callback
	},
	
	onViewFile: function(/*dojo.data.Item*/ item) {
		// summary:
		//		This method is called when the View menu item is clicked
		// tags:
		//		callback
	}
});
