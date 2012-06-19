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

dojo.provide("file.js.controllers.ExplorerController");

dojo.require("dijit.InlineEditBox");
dojo.require("dijit.form.TextBox");
dojo.require("dojo.io.iframe");
dojo.require("dojox.image.Lightbox");

dojo.require("core.js.base.controllers.ActionProvider");
dojo.require("core.js.base.controllers.Subscriber");
dojo.require("core.js.base.I18N");
dojo.require("core.js.base.views.Helper");
dojo.require("file.js.controllers.ExplorerMediator");

dojo.declare("file.js.controllers.ExplorerController", null, {
	// _id: String
	_id: null,
	
	// _i18n: Object
	_i18n: null,
	
	// _helper: core.js.base.views.Helper
	_helper: null,
	
	// _path: String
	//		Store the current path
	_path: ".",
	
	// _connectionId: String
	_connectionId: null,
	
	// _copyData: Object
	//		Store the information for moving/copying actions. It contains two members:
	//		- items: Array, which each item contains the file data stored by each row in the grid
	//		- action: Can be "move", "copy"
	_copyData: {
		items: [],
		action: "copy"
	},
	
	// _mediator: file.js.controllers.ExplorerMediator
	_mediator: null,
	
	// _imageLightboxDialog: dojox.image.LightboxDialog
	//		It is used to preview the image
	_imageLightboxDialog: null,
	
	// TOPIC_GROUP: [const] String
	TOPIC_GROUP: "/file/js/controllers/ExplorerController",
	
	constructor: function(/*String*/ id) {
		this._id = id;
		
		core.js.base.I18N.requireLocalization("file/languages");
		this._i18n = core.js.base.I18N.getLocalization("file/languages");
		
		// Create helper instance
		this._helper = new core.js.base.views.Helper(id);
		this._helper.setLanguageData(this._i18n);
		
		core.js.base.controllers.Subscriber.unsubscribe(this.TOPIC_GROUP);
		
		this._mediator = new file.js.controllers.ExplorerMediator();
		
		this._imageLightboxDialog = new dojox.image.LightboxDialog({});
		this._imageLightboxDialog.startup();
	},
	
	////////// MANAGE CONNECTIONS //////////
	
	// _connectionToolbar: file.js.views.ConnectionToolbar
	_connectionToolbar: null,
	
	// _connectionListView: file.js.views.ConnectionListView
	_connectionListView: null,
	
	// _connectionContextMenu: file.js.views.ConnectionContextMenu
	_connectionContextMenu: null,
	
	setConnectionToolbar: function(/*file.js.views.ConnectionToolbar*/ connectionToolbar) {
		// summary:
		//		Sets the connection toolbar
		this._connectionToolbar = connectionToolbar;
		
		// Add connection handler
		dojo.connect(connectionToolbar, "onAddConnection", this, "addConnection");
		core.js.base.controllers.Subscriber.subscribe(this.TOPIC_GROUP, "/app/file/connection/add/onCancel", this, function() {
			this._helper.removePane();
		});
		core.js.base.controllers.Subscriber.subscribe(this.TOPIC_GROUP, "/app/file/connection/add/onSuccess", this, function() {
			dojo.publish("/app/global/notification", [{ message: this._i18n.connection.add.success }]);
			this.reloadConnections();
		});
		
		return this;	// file.js.controllers.ExplorerController
	},
	
	setConnectionListView: function(/*file.js.views.ConnectionListView*/ connectionListView) {
		// summary:
		//		Sets the connections list view
		this._connectionListView = connectionListView;
		
		// Show the context menu
		dojo.connect(connectionListView, "onMouseDown", this, function(connectionItemView) {
			if (this._connectionContextMenu) {
				this._connectionContextMenu.show(connectionItemView);
			}
		});
		
		return this;	// file.js.controllers.ExplorerController
	},
	
	setConnectionContextMenu: function(/*file.js.views.ConnectionContextMenu*/ connectionContextMenu) {
		// summary:
		//		Sets the connection's context menu
		this._connectionContextMenu = connectionContextMenu;
		this._mediator.setConnectionContextMenu(connectionContextMenu);
		
		// Edit handler
		dojo.connect(connectionContextMenu, "onEditConnection", this, "editConnection");
		core.js.base.controllers.Subscriber.subscribe(this.TOPIC_GROUP, "/app/file/connection/edit/onCancel", this, function() {
			this._helper.removePane();
		});
		core.js.base.controllers.Subscriber.subscribe(this.TOPIC_GROUP, "/app/file/connection/edit/onComplete", this, function(data) {
			dojo.publish("/app/global/notification", [{
				message: this._i18n.connection.edit[(data.result == "APP_RESULT_OK") ? "success" : "error"],
				type: (data.result == "APP_RESULT_OK") ? "message" : "error"
			}]);
			if (data.result == "APP_RESULT_OK") {
				this.reloadConnections();
			}
		});
		
		// Delete handler
		dojo.connect(connectionContextMenu, "onDeleteConnection", this, "deleteConnection");
		core.js.base.controllers.Subscriber.subscribe(this.TOPIC_GROUP, "/app/file/connection/delete/onSuccess", this, function() {
			this._helper.closeDialog();
			dojo.publish("/app/global/notification", [{ message: this._i18n.connection["delete"].success }]);
			this.reloadConnections();
		});
		core.js.base.controllers.Subscriber.subscribe(this.TOPIC_GROUP, "/app/file/connection/delete/onCancel", this, function() {
			this._helper.closeDialog();
		});
		
		// Rename
		dojo.connect(connectionContextMenu, "onRenameConnection", this, "renameConnection");
		
		// Connect handler
		dojo.connect(connectionContextMenu, "onConnect", this, "connect");
		core.js.base.controllers.Subscriber.subscribe(this.TOPIC_GROUP, "/app/file/connection/connect/onConnected", this, "onConnected");

		// Disconnect handler
		dojo.connect(connectionContextMenu, "onDisconnect", this, "disconnect");
		core.js.base.controllers.Subscriber.subscribe(this.TOPIC_GROUP, "/app/file/connection/disconnect/onDisconnected", this, "onDisconnected");
		
		return this;	// file.js.controllers.ExplorerController
	},
	
	addConnection: function() {
		// summary:
		//		Adds new connection
		this._helper.showPane(core.js.base.controllers.ActionProvider.get("file_connection_add").url);
	},
	
	connect: function(/*media.js.views.ConnectionItemView*/ connectionItemView) {
		// summary:
		//		Connects to the server using information of given connection item
		this._connectionListView.setSelectedConnection(connectionItemView);
		
		var _this = this;
		var connectionId = connectionItemView.getConnection().connection_id;
		this._helper.showStandby();
		dojo.xhrPost({
			url: core.js.base.controllers.ActionProvider.get("file_connection_connect").url,
			content: {
				connection_id: connectionId
			},
			handleAs: "json",
			load: function(data) {
				_this._helper.hideStandby();
				if (data.result == "APP_RESULT_OK") {
					connectionItemView.getConnection().is_connected = true;
					dojo.publish("/app/global/notification", [{ message: _this._i18n.connection.connect.success }]);
					dojo.publish("/app/file/connection/connect/onConnected", [{ connection_id: connectionId, path: data.path }]);
				} else {
					dojo.publish("/app/global/notification", [{
						message: _this._i18n.connection.connect.error,
						type: "error"
					}]);
				}
			}
		});
	},
	
	deleteConnection: function(/*media.js.views.ConnectionItemView*/ connectionItemView) {
		// summary:
		//		Deletes given connection item
		var params = {
			connection_id: connectionItemView.getConnection().connection_id
		};
		var url = core.js.base.controllers.ActionProvider.get("file_connection_delete").url + "?" + dojo.objectToQuery(params);
		this._helper.showDialog(url, {
			title: this._i18n.connection["delete"].title,
			style: "width: 250px",
			refreshOnShow: true
		});
	},
	
	editConnection: function(/*media.js.views.ConnectionItemView*/ connectionItemView) {
		// summary:
		//		Edits given connection item
		var params = {
			connection_id: connectionItemView.getConnection().connection_id
		};
		var url = core.js.base.controllers.ActionProvider.get("file_connection_edit").url + "?" + dojo.objectToQuery(params);
		this._helper.showPane(url);
	},
	
	disconnect: function(/*media.js.views.ConnectionItemView*/ connectionItemView) {
		// summary:
		//		Disconnects given connection
		this._connectionListView.setSelectedConnection(null);
		
		var _this = this;
		var connectionId = connectionItemView.getConnection().connection_id;
		this._helper.showStandby();
		dojo.xhrPost({
			url: core.js.base.controllers.ActionProvider.get("file_connection_disconnect").url,
			content: {
				connection_id: connectionId
			},
			handleAs: "json",
			load: function(data) {
				_this._helper.hideStandby();
				if (data.result == "APP_RESULT_OK") {
					connectionItemView.getConnection().is_connected = false;
					dojo.publish("/app/global/notification", [{ message: _this._i18n.connection.disconnect.success }]);
					dojo.publish("/app/file/connection/disconnect/onDisconnected", [ connectionId ]);
				} else {
					dojo.publish("/app/global/notification", [{
						message: _this._i18n.connection.disconnect.error,
						type: "error"
					}]);
				}
			}
		});
	},
	
	reloadConnections: function() {
		// summary:
		//		Reloads the list of connections
		var _this = this;
		dojo.xhrPost({
			url: core.js.base.controllers.ActionProvider.get("file_connection_list").url,
			load: function(data) {
				_this._connectionListView.setContent(data);
			}
		});
	},
	
	renameConnection: function(/*media.js.views.ConnectionItemView*/ connectionItemView) {
		// summary:
		//		Renames given connection item
		var _this = this;
		var connectionId = connectionItemView.getConnection().connection_id;
		
		if (!connectionItemView.nameEditBox) {
			connectionItemView.nameEditBox = new dijit.InlineEditBox({
				editor: "dijit.form.TextBox", 
				autoSave: true, 
				disabled: false,
				editorParams: {
					required: true
				},
				onChange: function(newName) {
					this.set("disabled", true);
					if (newName != "") {
						dojo.xhrPost({
							url: core.js.base.controllers.ActionProvider.get("file_connection_rename").url,
							content: {
								connection_id: connectionId,
								name: newName
							},
							handleAs: "json",
							load: function(data) {
								if (data.result == "APP_RESULT_OK") {
									connectionItemView.getConnection().name = newName;
									dojo.publish("/app/global/notification", [{ message: _this._i18n.connection.rename.success }]);
								}
							}
						});
					}
				}, 
				onCancel: function() {
					this.set("disabled", true);
				}
			}, connectionItemView.getNameNode());
		}
		connectionItemView.nameEditBox.set("disabled", false);
		connectionItemView.nameEditBox.startup();
		connectionItemView.nameEditBox.edit();
	},
	
	////////// MANAGE DIRECTORIES //////////
	
	// _directoryTreeView: file.js.views.DirectoryTreeView
	_directoryTreeView: null,
	
	setDirectoryTreeView: function(/*file.js.views.DirectoryTreeView*/ directoryTreeView) {
		// summary:
		//		Sets the directory tree view
		this._directoryTreeView = directoryTreeView;
		this._mediator.setDirectoryTreeView(directoryTreeView);
		
		dojo.connect(directoryTreeView, "onOpenDirectory", this, function(item) {
			// When click on the directory to browse, reset the search criteria
			if (this._fileDataGrid) {
				this._fileDataGrid.resetSearchCriteria();
			}
			this.gotoPath(item.path);
		});
		
		// Create new directory handler
		dojo.connect(directoryTreeView, "onCreateDirectory", this, "createDirectory");
		core.js.base.controllers.Subscriber.subscribe(this.TOPIC_GROUP, "/app/file/explorer/add/onCancel", this, function() {
			this._helper.closeDialog();
		});
		core.js.base.controllers.Subscriber.subscribe(this.TOPIC_GROUP, "/app/file/explorer/add/onError", this, function(data) {
			dojo.publish("/app/global/notification", [{
				message: this._i18n.explorer.add.error,
				type: "error"
			}]);
		});
		core.js.base.controllers.Subscriber.subscribe(this.TOPIC_GROUP, "/app/file/explorer/add/onSuccess", this, function(data) {
			this._helper.closeDialog();
			dojo.publish("/app/global/notification", [{
				message: this._i18n.explorer.add.success,
				type: "message"
			}]);
			this.gotoPath(data.path, true);
		});
		
		// Add, remove bookmark handler
		dojo.connect(directoryTreeView, "onBookmarkDirectory", this, "bookmarkDirectory");
		dojo.connect(directoryTreeView, "onUnbookmarkDirectory", this, function(item) {
			if (item.directory) {
				this.unbookmarkDirectory(item.path);
			}
		});
		
		// Cut, copy, paste handlers
		dojo.connect(directoryTreeView, "onCutDirectory", this, "cutFile");
		dojo.connect(directoryTreeView, "onCopyDirectory", this, "copyFile");
		dojo.connect(directoryTreeView, "onPasteDirectory", this, "pasteFile");
		dojo.connect(directoryTreeView, "onMoveDirectories", this, function(sourceItems, targetItem) {
			this._copyData = {
				items: sourceItems,
				action: "move"
			};
			this.pasteFile(targetItem, true);
		});
		
		// Delete directory handler
		dojo.connect(directoryTreeView, "onDeleteDirectory", this, "deleteFile");
		core.js.base.controllers.Subscriber.subscribe(this.TOPIC_GROUP, "/app/file/explorer/delete/onCancel", this, function() {
			this._helper.closeDialog();
		});
		core.js.base.controllers.Subscriber.subscribe(this.TOPIC_GROUP, "/app/file/explorer/delete/onError", this, function(data) {
			dojo.publish("/app/global/notification", [{
				message: this._i18n.explorer["delete"].error,
				type: "error"
			}]);
		});
		core.js.base.controllers.Subscriber.subscribe(this.TOPIC_GROUP, "/app/file/explorer/delete/onSuccess", this, function(data) {
			this._helper.closeDialog();
			dojo.publish("/app/global/notification", [{
				message: this._i18n.explorer["delete"].success,
				type: "message"
			}]);
			this.gotoPath(data.path, true);
		});
		
		// Rename handler
		dojo.connect(directoryTreeView, "onRenameDirectory", this, "renameFile");
		core.js.base.controllers.Subscriber.subscribe(this.TOPIC_GROUP, "/app/file/explorer/rename/onCancel", this, function() {
			this._helper.closeDialog();
		});
		core.js.base.controllers.Subscriber.subscribe(this.TOPIC_GROUP, "/app/file/explorer/rename/onError", this, function(data) {
			dojo.publish("/app/global/notification", [{
				message: this._i18n.explorer.rename.error,
				type: "error"
			}]);
		});
		core.js.base.controllers.Subscriber.subscribe(this.TOPIC_GROUP, "/app/file/explorer/rename/onSuccess", this, function(/*Object*/ data) {
			this._helper.closeDialog();
			dojo.publish("/app/global/notification", [{
				message: this._i18n.explorer.rename.success,
				type: "message"
			}]);
			
			var path = (data.directory) 
						? data.path + "/" + data.name		// If I have just renamed a directory
						: data.path;						// If I have just renamed a file
			this.gotoPath(path, true);
		});
		
		// Upload handler
		dojo.connect(directoryTreeView, "onUploadFile", this, "uploadFile");
		core.js.base.controllers.Subscriber.subscribe(this.TOPIC_GROUP, "/app/file/explorer/upload/onCancel", this, function() {
			this._helper.closeDialog();
		});
		core.js.base.controllers.Subscriber.subscribe(this.TOPIC_GROUP, "/app/file/explorer/upload/onSuccess", this, function(data) {
			this._helper.closeDialog();
			dojo.publish("/app/global/notification", [{
				message: this._i18n.explorer.upload.success,
				type: "message"
			}]);
			if (data.length > 0) {
				this.gotoPath(data[0].path);
			}
		});
		
		// Change permissions handler
		dojo.connect(directoryTreeView, "onChangePermissions", this, "changePermissions");
		core.js.base.controllers.Subscriber.subscribe(this.TOPIC_GROUP, "/app/file/explorer/perm/onCancel", this, function() {
			this._helper.closeDialog();
		});
		core.js.base.controllers.Subscriber.subscribe(this.TOPIC_GROUP, "/app/file/explorer/perm/onStart", this, function() {
			this._helper.showStandby();
		});
		core.js.base.controllers.Subscriber.subscribe(this.TOPIC_GROUP, "/app/file/explorer/perm/onError", this, function(data) {
			this._helper.closeDialog();
			this._helper.hideStandby();
			dojo.publish("/app/global/notification", [{
				message: this._i18n.explorer.perm.error,
				type: "error"
			}]);
			this.gotoPath(this._path);
		});
		core.js.base.controllers.Subscriber.subscribe(this.TOPIC_GROUP, "/app/file/explorer/perm/onSuccess", this, function(data) {
			this._helper.closeDialog();
			this._helper.hideStandby();
			dojo.publish("/app/global/notification", [{
				message: this._i18n.explorer.perm.success,
				type: "message"
			}]);
			this.gotoPath(this._path);
		});
		
		return this;	// file.js.controllers.ExplorerController
	},
	
	createDirectory: function(/*dojo.data.Item*/ item) {
		// summary:
		//		Creates new directory
		var params = {
			connection_id: this._connectionId,
			path: item.path
		};
		var url = core.js.base.controllers.ActionProvider.get("file_explorer_add").url + "?" + dojo.objectToQuery(params);
		this._helper.showDialog(url, {
			title: this._i18n.explorer.add.title,
			style: "width: 300px",
			refreshOnShow: true
		});
	},
	
	////////// MANAGE FILES //////////
	
	// _fileDataGrid: file.js.views.FileDataGrid
	_fileDataGrid: null,
	
	// _fileToolbar: file.js.views.FileToolbar
	_fileToolbar: null,
	
	// _pathBreadcrumb:file.js.views.PathBreadcrumb
	_pathBreadcrumb: null,
	
	setFileDataGrid: function(/*file.js.views.FileDataGrid*/ fileDataGrid) {
		// summary:
		//		Sets the file grid
		this._fileDataGrid = fileDataGrid;
		this._mediator.setFileDataGrid(fileDataGrid);
		
		dojo.connect(fileDataGrid, "onOpenDirectory", this, "gotoPath");
		
		// Create directory handler
		dojo.connect(fileDataGrid, "onCreateDirectory", this, "createDirectory");
		
		// Bookmark handler
		dojo.connect(fileDataGrid, "onBookmarkDirectory", this, "bookmarkDirectory");
		dojo.connect(fileDataGrid, "onUnbookmarkDirectory", this, function(item) {
			if (item.directory) {
				this.unbookmarkDirectory(item.path);
			}
		});
		
		// Cut/copy/paste handler
		dojo.connect(fileDataGrid, "onCutFile", this, "cutFile");
		dojo.connect(fileDataGrid, "onCopyFile", this, "copyFile");
		dojo.connect(fileDataGrid, "onPasteFile", this, "pasteFile");
		
		// Edit handler
		dojo.connect(fileDataGrid, "onEditFile", this, "editFile");
		core.js.base.controllers.Subscriber.subscribe(this.TOPIC_GROUP, "/app/file/explorer/edit/onCancel", this, function() {
			this._helper.removePane();
		});
		core.js.base.controllers.Subscriber.subscribe(this.TOPIC_GROUP, "/app/file/explorer/edit/onError", this, function(data) {
			dojo.publish("/app/global/notification", [{
				message: this._i18n.explorer.edit.error,
				type: "error"
			}]);
		});
		core.js.base.controllers.Subscriber.subscribe(this.TOPIC_GROUP, "/app/file/explorer/edit/onSuccess", this, function(data) {
			dojo.publish("/app/global/notification", [{
				message: this._i18n.explorer.edit.success,
				type: "message"
			}]);
		});
		
		// View file handler
		dojo.connect(fileDataGrid, "onViewFile", this, "viewFile");
		core.js.base.controllers.Subscriber.subscribe(this.TOPIC_GROUP, "/app/file/explorer/view/onCancel", this, function() {
			this._helper.removePane();
		});
		
		// Delete handler
		dojo.connect(fileDataGrid, "onDeleteFile", this, "deleteFile");
		
		// Rename handler
		dojo.connect(fileDataGrid, "onRenameFile", this, "renameFile");
		
		// Compress handler
		dojo.connect(fileDataGrid, "onCompressFile", this, "compressFile");
		core.js.base.controllers.Subscriber.subscribe(this.TOPIC_GROUP, "/app/file/explorer/compress/onCancel", this, function() {
			this._helper.closeDialog();
		});
		core.js.base.controllers.Subscriber.subscribe(this.TOPIC_GROUP, "/app/file/explorer/compress/onStart", this, function() {
			this._helper.showStandby();
		});
		core.js.base.controllers.Subscriber.subscribe(this.TOPIC_GROUP, "/app/file/explorer/compress/onComplete", this, function(data) {
			this._helper.closeDialog();
			this._helper.hideStandby();
			dojo.publish("/app/global/notification", [{
				message: this._i18n.explorer.compress[(data.result == "APP_RESULT_OK") ? "success" : "error"],
				type: (data.result == "APP_RESULT_OK") ? "message" : "error"
			}]);
			
			this.gotoPath(data.path, true);
		});
		
		// Extract handler
		dojo.connect(fileDataGrid, "onExtractFile", this, "extractFile");
		core.js.base.controllers.Subscriber.subscribe(this.TOPIC_GROUP, "/app/file/explorer/extract/onCancel", this, function() {
			this._helper.closeDialog();
		});
		core.js.base.controllers.Subscriber.subscribe(this.TOPIC_GROUP, "/app/file/explorer/extract/onStart", this, function() {
			this._helper.showStandby();
		});
		core.js.base.controllers.Subscriber.subscribe(this.TOPIC_GROUP, "/app/file/explorer/extract/onComplete", this, function(data) {
			this._helper.closeDialog();
			this._helper.hideStandby();
			dojo.publish("/app/global/notification", [{
				message: this._i18n.explorer.extract[(data.result == "APP_RESULT_OK") ? "success" : "error"],
				type: (data.result == "APP_RESULT_OK") ? "message" : "error"
			}]);
			this.gotoPath(data.path, true);
		});
		
		// Download handler
		dojo.connect(fileDataGrid, "onDownloadFile", this, "downloadFile");
		
		// Upload handler
		dojo.connect(fileDataGrid, "onUploadFile", this, "uploadFile");
		
		// Change permissions handler
		dojo.connect(fileDataGrid, "onChangePermissions", this, "changePermissions");
		
		return this;	// file.js.controllers.ExplorerController
	},
	
	setFileToolbar: function(/*file.js.views.FileToolbar*/ fileToolbar) {
		// summary:
		//		Sets the file toolbar
		this._fileToolbar = fileToolbar;
		this._mediator.setFileToolbar(fileToolbar);
		
		dojo.connect(fileToolbar, "onGotoPath", this, "gotoPath");
		
		// Refresh handler
		dojo.connect(fileToolbar, "onRefresh", this, function() {
			this.gotoPath(this._path);
		});
		
		// Upload handler
		dojo.connect(fileToolbar, "onUploadFile", this, function() {
			this.uploadFile({ path: this._path });
		});
		
		// Filter handler
		dojo.connect(fileToolbar, "onSetShowingFilesOnly", this, function(showFilesOnly) {
			if (this._fileDataGrid) {
				this._fileDataGrid.setSearchCriteria({ files_only: showFilesOnly });
				this.gotoPath(this._path);
			}
		});
		dojo.connect(fileToolbar, "onSetShowingHiddenFiles", this, function(showHiddenFiles) {
			if (this._fileDataGrid) {
				this._fileDataGrid.setSearchCriteria({ hidden_files: showHiddenFiles });
				this.gotoPath(this._path);
			}
		});
		
		// Search handler
		dojo.connect(fileToolbar, "onSearchFiles", this, function(searchCriteria) {
			if (this._fileDataGrid) {
				this._fileDataGrid.setSearchCriteria(searchCriteria);
				this.gotoPath(this._path);
			}
		});
		
		return this;	// file.js.controllers.ExplorerController
	},
	
	setPathBreadcrumb: function(/*file.js.views.PathBreadcrumb*/ pathBreadcrumb) {
		// summary:
		//		Sets the path breadcrumb
		this._pathBreadcrumb = pathBreadcrumb;
		dojo.connect(pathBreadcrumb, "onGotoPath", this, "gotoPath");
		
		return this;	// file.js.controllers.ExplorerController
	},
	
	changePermissions: function(/*dojo.data.Item*/ item) {
		// summary:
		//		Sets the permissions to given file/directory
		var params = {
			connection_id: this._connectionId,
			path: item.path,
			perms: item.perms
		};
		var url = core.js.base.controllers.ActionProvider.get("file_explorer_perm").url + "?" + dojo.objectToQuery(params);
		this._helper.showDialog(url, {
			title: this._i18n.explorer.perm.title,
			style: "width: 350px",
			refreshOnShow: true
		});
	},
	
	compressFile: function(/*dojo.data.Item*/ item) {
		// summary:
		//		Compress given file
		var params = {
			connection_id: this._connectionId,
			path: item.path
		};
		var url = core.js.base.controllers.ActionProvider.get("file_explorer_compress").url + "?" + dojo.objectToQuery(params);
		this._helper.showDialog(url, {
			title: this._i18n.explorer.compress.title,
			style: "width: 300px",
			refreshOnShow: true
		});
	},
	
	copyFile: function(/*dojo.data.Item*/ sourceItem) {
		// summary:
		//		Copies file
		this._copyData = {
			items: [ sourceItem ],
			action: "copy"
		};
	},
	
	cutFile: function(/*dojo.data.Item*/ sourceItem) {
		// summary:
		//		Moves file
		this._copyData = {
			items: [ sourceItem ],
			action: "move"
		};
	},
	
	deleteFile: function(/*dojo.data.Item*/ item) {
		// summary:
		//		Deletes file
		var params = {
			connection_id: this._connectionId,
			path: item.path,
			directory: item.directory
		};
		var url = core.js.base.controllers.ActionProvider.get("file_explorer_delete").url + "?" + dojo.objectToQuery(params);
		this._helper.showDialog(url, {
			title: this._i18n.explorer["delete"].title,
			style: "width: 250px",
			refreshOnShow: true
		});
	},
	
	downloadFile: function(/*dojo.data.Item*/ item) {
		// summary:
		//		Downloads file
		if (item.directory) {
			return;
		}
		// FIXME: There is an loading issue in the console
		dojo.io.iframe.send({
			url: core.js.base.controllers.ActionProvider.get("file_explorer_download").url,
			method: "GET",
			content: {
				connection_id: this._connectionId,
				path: item.path
			}
		});
	},
	
	editFile: function(/*dojo.data.Item*/ item) {
		// summary:
		//		Edits file
		if (item.directory) {
			return;
		}
		var params = {
			connection_id: this._connectionId,
			path: item.path
		};
		var url = core.js.base.controllers.ActionProvider.get("file_explorer_edit").url + "?" + dojo.objectToQuery(params);
		this._helper.showPane(url);
	},
	
	extractFile: function(/*dojo.data.Item*/ item) {
		// summary:
		//		Extracts compressed file
		var params = {
			connection_id: this._connectionId,
			path: item.path
		};
		var url = core.js.base.controllers.ActionProvider.get("file_explorer_extract").url + "?" + dojo.objectToQuery(params);
		this._helper.showDialog(url, {
			title: this._i18n.explorer.extract.title,
			style: "width: 300px",
			refreshOnShow: true
		});
	},
	
	pasteFile: function(/*dojo.data.Item*/ targetItem, /*Boolean*/ overwrite) {
		// summary:
		//		Copies or moves file
		var sourceItems = this._copyData.items;
		if (!sourceItems || sourceItems.length == 0) {
			return;
		}
		
		// Make a client check to determine which file can be copied/moved
		if (!targetItem.directory) {
			// Target is not a directory
			return;
		}
		
		var sourcePaths = [];
		dojo.forEach(sourceItems, function(item, index) {
			if (item.path == "" || item.path == "."								// Source is the root directory
				|| item.parentDir == targetItem.path							// Target is already the parent directory of the source
				|| item.path == targetItem.path									// The source and the target are the same
				|| targetItem.path.substr(0, item.path.length) == item.path)	// Target is sub-directory of the source 
			{
				// It is not able to copy/move the source item
			} else {
				sourcePaths.push(item.path);
			}
		});
		if (sourcePaths.length == 0) {
			return;
		}
		
		var action = this._copyData.action;
		var route  = (action == "copy") ? "file_explorer_copy" : "file_explorer_move";
		var url	   = core.js.base.controllers.ActionProvider.get(route).url;
		var _this  = this;
		this._helper.showStandby();
		dojo.xhrPost({
			url: url,
			content: {
				connection_id: this._connectionId,
				"source_paths[]": sourcePaths,
				target_path: targetItem.path,
				overwrite: overwrite
			},
			handleAs: "json",
			load: function(data) {
				_this._helper.hideStandby();
				dojo.publish("/app/global/notification", [{
					message: _this._i18n.explorer[action][(data.result == "APP_RESULT_OK") ? "success" : "error"],
					type: (data.result == "APP_RESULT_OK") ? "message" : "error"
				}]);
				
				if (data.result == "APP_RESULT_OK") {
					_this.gotoPath(targetItem.path, true);
				}
			}
		});
	},
	
	renameFile: function(/*dojo.data.Item*/ item) {
		// summary:
		//		Renames file
		var params = {
			connection_id: this._connectionId,
			directory: item.directory,
			path: item.path
		};
		var url = core.js.base.controllers.ActionProvider.get("file_explorer_rename").url + "?" + dojo.objectToQuery(params);
		this._helper.showDialog(url, {
			title: this._i18n.explorer.rename.title,
			style: "width: 300px",
			refreshOnShow: true
		});
	},
	
	uploadFile: function(/*dojo.data.Item*/ item) {
		// summary:
		//		Uploads file
		var params = {
			connection_id: this._connectionId,
			path: item.path
		};
		var url = core.js.base.controllers.ActionProvider.get("file_explorer_upload").url + "?" + dojo.objectToQuery(params);
		this._helper.showDialog(url, {
			title: this._i18n.explorer.upload.title,
			style: "width: 350px",
			refreshOnShow: true
		});
	},
	
	viewFile: function(/*dojo.data.Item*/ item) {
		// summary:
		//		Views file
		if (item.directory) {
			return;
		}
		var params = {
			connection_id: this._connectionId,
			path: item.path
		};
		var url = core.js.base.controllers.ActionProvider.get("file_explorer_view").url + "?" + dojo.objectToQuery(params);
		
		// If user view an image, show a preview dialog
		var _this = this;
		if (dojo.indexOf(["bmp", "gif", "jpeg", "jpg", "png"], item.extension.toLowerCase()) != -1) {
			this._helper.showStandby();
			dojo.xhrPost({
				url: url,
				handleAs: "json",
				load: function(data) {
					_this._helper.hideStandby();
					_this._imageLightboxDialog.show({
						title: data.title,
						href: data.url
					});
				}
			});
		} else {
			// The file might be a text file, show its content in a pane
			this._helper.showPane(url);
		}
	},
	
	////////// MANAGE BOOKMARKS //////////
	
	// _bookmarkListView: file.js.views.BookmarkListView
	_bookmarkListView: null,
	
	setBookmarkListView: function(/*file.js.views.BookmarkListView*/ bookmarkListView) {
		// summary:
		//		Sets the bookmarks list view
		this._bookmarkListView = bookmarkListView;
		this._mediator.setBookmarkListView(bookmarkListView);
		
		// Go to the associating directory when clicking on the bookmark item
		dojo.connect(bookmarkListView, "onClickBookmark", this, function(bookmarkItemView) {
			var bookmark = bookmarkItemView.getBookmark();
			this.gotoPath(bookmark.path);
		});
		
		// Remove the bookmark when clicking on the Star icon
		dojo.connect(bookmarkListView, "onUnbookmarkDirectory", this, function(bookmarkItemView) {
			this.unbookmarkDirectory(bookmarkItemView.getBookmark().path);
		});
		
		// Allow to drag the directory from the grid to the list of bookmarks
		dojo.connect(bookmarkListView, "onBookmarkDirectory", this, "bookmarkDirectory");
		
		// Rename handler
		dojo.connect(bookmarkListView, "onRenameBookmark", this, "renameBookmark");
		
		return this;	// file.js.controllers.ExplorerController
	},
	
	bookmarkDirectory: function(/*dojo.data.Item*/ item) {
		// summary:
		//		Bookmark given directory
		if (!item.directory) {
			return;
		}
		this._helper.showStandby();
		var _this = this;
		dojo.xhrPost({
			url: core.js.base.controllers.ActionProvider.get("file_bookmark_add").url,
			content: {
				connection_id: this._connectionId,
				path: item.path
			},
			handleAs: "json",
			load: function(data) {
				_this._helper.hideStandby();
				dojo.publish("/app/global/notification", [{
					message: _this._i18n.bookmark.add[(data.result == "APP_RESULT_OK") ? "success" : "error"],
					type: (data.result == "APP_RESULT_OK") ? "message" : "error"
				}]);
				if (data.result == "APP_RESULT_OK") {
					_this.reloadBookmarks();
				}
			}
		});
	},
	
	reloadBookmarks: function() {
		// summary:
		//		Reloads the list of bookmarks
		if (!this._bookmarkListView) {
			return;
		}
		var _this = this;
		dojo.xhrPost({
			url: core.js.base.controllers.ActionProvider.get("file_bookmark_list").url,
			content: {
				connection_id: this._connectionId
			},
			load: function(data) {
				_this._bookmarkListView.setContent(data);
			}
		});
	},
	
	renameBookmark: function(/*file.js.views.BookmarkItemView*/ bookmarkItemView) {
		// summary:
		//		Renames given bookmark
		var bookmark = bookmarkItemView.getBookmark();
		var _this = this;
		if (!bookmarkItemView.nameEditBox) {
			bookmarkItemView.nameEditBox = new dijit.InlineEditBox({
				editor: "dijit.form.TextBox", 
				autoSave: true, 
				disabled: false,
				editorParams: {
					required: true
				},
				onChange: function(newName) {
					this.set("disabled", true);
					if (newName != "") {
						dojo.xhrPost({
							url: core.js.base.controllers.ActionProvider.get("file_bookmark_rename").url,
							content: {
								bookmark_id: bookmark.bookmark_id,
								name: newName
							},
							handleAs: "json",
							load: function(data) {
								dojo.publish("/app/global/notification", [{
									message: _this._i18n.bookmark.rename[(data.result == "APP_RESULT_OK") ? "success" : "error"],
									type: (data.result == "APP_RESULT_OK") ? "message" : "error"
								}]);
								
								if (data.result == "APP_RESULT_OK") {
									bookmark.name = newName;
								}
							}
						});
					}
				}, 
				onCancel: function() {
					this.set("disabled", true);
				}
			}, bookmarkItemView.getBookmarkNameNode());
		}
		bookmarkItemView.nameEditBox.set("disabled", false);
		bookmarkItemView.nameEditBox.startup();
		bookmarkItemView.nameEditBox.edit();
	},
	
	unbookmarkDirectory: function(/*String*/ path) {
		// summary:
		//		Unbookmarks given directory
		this._helper.showStandby();
		var _this = this;
		dojo.xhrPost({
			url: core.js.base.controllers.ActionProvider.get("file_bookmark_delete").url,
			content: {
				connection_id: this._connectionId,
				path: path
			},
			handleAs: "json",
			load: function(data) {
				_this._helper.hideStandby();
				dojo.publish("/app/global/notification", [{
					message: _this._i18n.bookmark["delete"][(data.result == "APP_RESULT_OK") ? "success" : "error"],
					type: (data.result == "APP_RESULT_OK") ? "message" : "error"
				}]);
				if (data.result == "APP_RESULT_OK") {
					_this.reloadBookmarks();
				}
			}
		});
	},
	
	////////// SUBSCRIBE TOPICS //////////
	
	gotoPath: function(/*String*/ path, /*Boolean*/ reload) {
		// summary:
		//		Updates the directory tree, file grid and other controls when setting the current path
		// path:
		//		The sub-path to the directory
		// reload:
		//		If true, then some controls need to reload
		
		// There is another way that I don't have to check if the view instance already exist or not.
		// Instead of checking 
		//		if (this._directoryTreeView) {
		//		...
		//		}
		// I can use dojo.connect to handle the method when setting the view instance:
		//		setDirectoryTreeView: function(directoryTreeView) {
		//			this._directoryTreeView = directoryTreeView;
		//			dojo.connect(this, "gotoPath", directoryTreeView, function(path, reload) {
		//				directoryTreeView.show(path, reload);
		//			});
		//		}
		
		this._path = path;
		if (this._directoryTreeView) {
			this._directoryTreeView.show(path, reload);
		}
		if (this._fileDataGrid) {
			this._fileDataGrid.show(path);
		}
		if (this._fileToolbar) {
			this._fileToolbar.addPathToHistory(path);
		}
		if (this._pathBreadcrumb) {
			this._pathBreadcrumb.show(path);
		}
	},
	
	onConnected: function(/*Object*/ data) {
		// summary:
		//		Called after connecting
		this._connectionId = data.connection_id;
		if (this._directoryTreeView) {
			this._directoryTreeView.setConnectionId(this._connectionId);
		}
		if (this._fileDataGrid) {
			this._fileDataGrid.setConnectionId(this._connectionId);
		}
		this.reloadBookmarks();
		
		var path = data.path;
		this.gotoPath(!path ? "." : path);
	},
	
	onDisconnected: function(/*String*/ connectionId) {
		// summary:
		//		Called after disconnecting
		this._helper.removePane();
		this._helper.closeDialog();
		
		this._connectionId = null;
		this._path = ".";
		if (this._directoryTreeView) {
			this._directoryTreeView.disconnect();
		}
		if (this._fileDataGrid) {
			this._fileDataGrid.disconnect();
		}
		if (this._pathBreadcrumb) {
			this._pathBreadcrumb.disconnect();
		}
		if (this._fileToolbar) {
			this._fileToolbar.resetPathHistory();
		}
		if (this._bookmarkListView) {
			this._bookmarkListView.setContent("");
		}
	}
});
