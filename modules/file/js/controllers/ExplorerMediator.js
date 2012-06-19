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
 * @version		2012-05-16
 */

dojo.provide("file.js.controllers.ExplorerMediator");

dojo.require("core.js.base.Config");
dojo.require("core.js.base.controllers.Subscriber");

dojo.declare("file.js.controllers.ExplorerMediator", null, {
	// _connectionContextMenu: file.js.views.ConnectionContextMenu
	_connectionContextMenu: null,
	
	// _directoryTreeView: file.js.views.DirectoryTreeView
	_directoryTreeView: null,
	
	// _sourceItem: dojo.data.Item
	//		The source item in the cut/copy actions
	_sourceItem: null,
	
	// _fileToolbar: file.js.views.FileToolbar
	_fileToolbar: null,
	
	// _fileDataGrid: file.js.views.FileDataGrid
	_fileDataGrid: null,
	
	// _bookmarkListView: file.js.views.BookmarkListView
	_bookmarkListView: null,
	
	// TOPIC_GROUP: [const] String
	TOPIC_GROUP: "/file/js/controllers/ExplorerMediator",
	
	constructor: function() {
		core.js.base.controllers.Subscriber.unsubscribe(this.TOPIC_GROUP);
		
		core.js.base.controllers.Subscriber.subscribe(this.TOPIC_GROUP, "/app/file/connection/connect/onConnected", this, "onConnected");
		core.js.base.controllers.Subscriber.subscribe(this.TOPIC_GROUP, "/app/file/connection/disconnect/onDisconnected", this, "onDisconnected");
	},
	
	setConnectionContextMenu: function(/*file.js.views.ConnectionContextMenu*/ connectionContextMenu) {
		// summary:
		//		Sets the connection's context menu
		this._connectionContextMenu = connectionContextMenu;
		
		dojo.connect(connectionContextMenu, "onContextMenu", this, function(connectionItemView) {
			var isConnected = connectionItemView.getConnection().is_connected;
			this._connectionContextMenu.allowToDelete(!isConnected);
		});
	},
	
	setDirectoryTreeView: function(/*file.js.views.DirectoryTreeView*/ directoryTreeView) {
		// summary:
		//		Sets the directory tree view
		this._directoryTreeView = directoryTreeView;
		
		dojo.connect(directoryTreeView, "onCutDirectory", this, function(sourceItem) {
			this._sourceItem = sourceItem;
			this._directoryTreeView.allowToPaste(true, "move");
		});
		dojo.connect(directoryTreeView, "onCopyDirectory", this, function(sourceItem) {
			this._sourceItem = sourceItem;
			this._directoryTreeView.allowToPaste(true, "copy");
		});
		dojo.connect(directoryTreeView, "onNodeContextMenu", this, function(item) {
			var canPaste = this._canPaste(item);
			this._directoryTreeView.allowToPaste(canPaste, "copy")
								   .allowToPaste(canPaste, "move")
								   // Don't allow to some actions to root
								   .allowToCut(!item.root)
								   .allowToCopy(!item.root)
								   .allowToRename(!item.root)
								   .allowToDelete(!item.root)
								   .allowToChangePermissions(!item.root);
		});
	},
	
	setFileToolbar: function(/*file.js.views.FileToolbar*/ fileToolbar) {
		// summary:
		//		Sets the file toolbar
		this._fileToolbar = fileToolbar;
	},
	
	setFileDataGrid: function(/*file.js.views.FileDataGrid*/ fileDataGrid) {
		// summary:
		//		Sets the file grid
		this._fileDataGrid = fileDataGrid;
		
		dojo.connect(fileDataGrid, "onCutFile", this, function(sourceItem) {
			this._sourceItem = sourceItem;
			this._fileDataGrid.allowToPaste(true, "move");
		});
		dojo.connect(fileDataGrid, "onCopyFile", this, function(sourceItem) {
			this._sourceItem = sourceItem;
			this._fileDataGrid.allowToPaste(true, "copy");
		});
		
		dojo.connect(fileDataGrid, "onRowContextMenu", this, function(item) {
			var canPaste = this._canPaste(item);
			
			var editableExts = core.js.base.Config.get("file", "editable_files");
			editableExts	 = editableExts.split(",");
			
			var viewableExts = core.js.base.Config.get("file", "viewable_files");
			viewableExts	 = viewableExts.split(",");
			
			var compressableExts = core.js.base.Config.get("file", "compressable_files", "");
			var decompressableExts = core.js.base.Config.get("file", "decompressable_files", "");
			var isExtractAllowed   = !item.directory && decompressableExts != "" && dojo.indexOf(decompressableExts.split(","), item.extension) != -1;
			
			this._fileDataGrid.allowToCreateDirectory(item.directory)
							  .allowToPaste(canPaste, "copy")
							  .allowToPaste(canPaste, "move")
							  .allowToEdit(!item.directory && dojo.indexOf(editableExts, item.extension) != -1)
							  .allowToView(!item.directory && dojo.indexOf(viewableExts, item.extension) != -1)
							  .allowToCompress(compressableExts != "")
							  .allowToExtract(isExtractAllowed)
							  .allowToDownload(!item.directory)
							  .allowToUpload(item.directory);
		});
	},
	
	setBookmarkListView: function(/*file.js.views.BookmarkListView*/ bookmarkListView) {
		// summary:
		//		Sets the bookmarks list view
		this._bookmarkListView = bookmarkListView;
		
		if (this._fileDataGrid) {
			dojo.connect(this._fileDataGrid, "onRowContextMenu", this, function(item) {
				if (!item.directory) {
					this._fileDataGrid.allowToBookmark(false);
				} else {
					var bookmarkedPaths = this._bookmarkListView.getBookmarkedPaths();
					if (dojo.indexOf(bookmarkedPaths, item.path) == -1) {
						this._fileDataGrid.allowToBookmark(true);
					} else {
						this._fileDataGrid.allowToUnbookmark();
					}
				}
			});
		}
		
		if (this._directoryTreeView) {
			dojo.connect(this._directoryTreeView, "onNodeContextMenu", this, function(item) {
				if (!item.directory) {
					this._directoryTreeView.allowToBookmark(false);
				} else {
					var bookmarkedPaths = this._bookmarkListView.getBookmarkedPaths();
					if (dojo.indexOf(bookmarkedPaths, item.path) == -1) {
						this._directoryTreeView.allowToBookmark(true);
					} else {
						this._directoryTreeView.allowToUnbookmark();
					}
				}
			});
		}
	},
	
	_canPaste: function(/*dojo.data.Item*/ targetItem) {
		// summary:
		//		Check whether it is possible to perform pasting action or not
		if (!this._sourceItem
			|| this._sourceItem.path == "" || this._sourceItem.path == "."							// Source is the root
			|| !targetItem.directory																// Target is not a directory
			|| this._sourceItem.parentDir == targetItem.path										// Target is already the parent directory of the source
			|| this._sourceItem.path == targetItem.path												// Don't allow to paste to itself
			|| targetItem.path.substr(0, this._sourceItem.path.length) == this._sourceItem.path)	// Target is sub-directory of the source 
		{
			return false;
		}
		return true;
	},
	
	onConnected: function(/*Object*/ data) {
		// summary:
		//		Called after connecting
		if (this._fileToolbar) {
			this._fileToolbar.allowToList(true)
							 .allowToUpload(true);
		}
	},
	
	onDisconnected: function(/*String*/ connectionId) {
		// summary:
		//		Called after disconnecting
		if (this._fileToolbar) {
			this._fileToolbar.allowToList(false)
							 .allowToUpload(false);
		}
	}
});
