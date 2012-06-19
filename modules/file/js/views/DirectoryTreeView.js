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

dojo.provide("file.js.views.DirectoryTreeView");

dojo.require("dijit.Menu");
dojo.require("dijit.MenuItem");
dojo.require("dijit.MenuSeparator");
dojo.require("dijit.Tree");
dojo.require("dijit.tree.dndSource");
dojo.require("dijit.tree.ForestStoreModel");
dojo.require("dojox.data.FileStore");
dojo.require("dojox.grid.enhanced.plugins.GridSource");

dojo.require("core.js.base.controllers.ActionProvider");
dojo.require("core.js.base.I18N");

dojo.declare("file.js.views.DirectoryTreeView", null, {
	// _id: String
	_id: null,
	
	// _rootTreeId: [readonly] String
	//		The string that is used to identicate the root node
	_rootTreeId: "DirectoryRoot",
	
	// _currentPath: String
	_currentPath: ".",
	
	// _parentNode: DomNode
	_parentNode: null,
	
	// _i18n: Object
	_i18n: null,
	
	// __connectionId: String
	_connectionId: null,
	
	// _tree: dijit.Tree
	_tree: null,
	
	// _selectedNode: dijit._TreeNode
	_selectedNode: null,
	
	// _hoverNode: dijit._TreeNode
	//		The tree node that mouse is over on
	_hoverNode: null,
	
	////////// CONTEXT MENU //////////
	
	// _contextMenu: dijit.Menu
	_contextMenu: null,
	
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
	
	// _deleteMenuItem: dijit.Menu
	_deleteMenuItem: null,
	
	// _renameMenuItem: dijit.Menu
	_renameMenuItem: null,
	
	// _changePermissionsMenuItem: dijit.MenuItem
	_changePermissionsMenuItem: null,
	
	constructor: function(/*String*/ id) {
		this._id		 = id;
		this._parentNode = dojo.byId(id).parentNode;
		
		core.js.base.I18N.requireLocalization("file/languages");
		this._i18n = core.js.base.I18N.getLocalization("file/languages");
		
		this._createContextMenu();
	},
	
	setConnectionId: function(/*String*/ connectionId) {
		this._connectionId = connectionId;
		
		// I have to re-create the tree, because there is no way to reload the tree when the connection is changed
		this._createTree();
	},
	
	_createContextMenu: function() {
		// summary:
		//		Creates the context menu for the tree view
		var contextMenuId = this._id + "_ContextMenu";
		var div = dojo.create("div", {
			id: contextMenuId
		}, this._parentNode);
		this._contextMenu = new dijit.Menu({
			targetNodeIds: [ contextMenuId ]
		});

		var _this = this;
		
		// Add directory item
		this._createMenuItem = new dijit.MenuItem({
			label: this._i18n.explorer._share.createDirectoryAction,
			iconClass: "appIcon fileExplorerAddFolderIcon",
			disabled: !core.js.base.controllers.ActionProvider.get("file_explorer_add").isAllowed,
			onClick: function() {
				if (_this._selectedNode) {
					_this.onCreateDirectory(_this._selectedNode.item);
				}
			}
		});
		this._contextMenu.addChild(this._createMenuItem);
		
		// Bookmark menu item
		this._bookmarkMenuItem = new dijit.MenuItem({
			label: this._i18n.bookmark._share.bookmarkAction,
			iconClass: "appIcon appBookmarkIcon",
			disabled: !core.js.base.controllers.ActionProvider.get("file_bookmark_add").isAllowed,
			onClick: function(e) {
				if (_this._selectedNode && _this._selectedNode.item && _this._selectedNode.item.directory) {
					_this.onBookmarkDirectory(_this._selectedNode.item);
				}
			}
		});
		this._contextMenu.addChild(this._bookmarkMenuItem);
		
		this._contextMenu.addChild(new dijit.MenuSeparator());
		
		// Cut, copy, paste menu items
		this._cutMenuItem = new dijit.MenuItem({
			label: this._i18n.global._share.cutAction,
			disabled: !core.js.base.controllers.ActionProvider.get("file_explorer_move").isAllowed,
			onClick: function(e) {
				if (_this._selectedNode) {
					_this.onCutDirectory(_this._selectedNode.item);
				}
			}
		});
		this._contextMenu.addChild(this._cutMenuItem);
		
		this._copyMenuItem = new dijit.MenuItem({
			label: this._i18n.global._share.copyAction,
			iconClass: "appIcon appCopyIcon",
			disabled: !core.js.base.controllers.ActionProvider.get("file_explorer_copy").isAllowed,
			onClick: function(e) {
				if (_this._selectedNode) {
					_this.onCopyDirectory(_this._selectedNode.item);
				}
			}
		});
		this._contextMenu.addChild(this._copyMenuItem);
		
		this._pasteMenuItem = new dijit.MenuItem({
			label: this._i18n.global._share.pasteAction,
			iconClass: "appIcon appPasteIcon",
			disabled: true,
			onClick: function(e) {
				if (_this._selectedNode) {
					_this.onPasteDirectory(_this._selectedNode.item, true);
				}
			}
		});
		this._contextMenu.addChild(this._pasteMenuItem);
		
		this._pasteWithoutOverwritingMenuItem = new dijit.MenuItem({
			label: this._i18n.global._share.pasteWithoutOverwritingAction,
			iconClass: "appIcon appPasteIcon",
			disabled: true,
			onClick: function(e) {
				if (_this._selectedNode) {
					_this.onPasteDirectory(_this._selectedNode.item, false);
				}
			}
		});
		this._contextMenu.addChild(this._pasteWithoutOverwritingMenuItem);
		
		this._contextMenu.addChild(new dijit.MenuSeparator());
		
		// Refresh
		this._contextMenu.addChild(new dijit.MenuItem({
			label: this._i18n.global._share.refreshAction,
			iconClass: "appIcon appRefreshIcon",
			disabled: !core.js.base.controllers.ActionProvider.get("file_explorer_list").isAllowed,
			onClick: function() {
				_this.show("", true);
			}
		}));
		
		this._contextMenu.addChild(new dijit.MenuSeparator());
		
		// Delete directory item
		this._deleteMenuItem = new dijit.MenuItem({
			label: this._i18n.global._share.deleteAction,
			iconClass: "appIcon appDeleteIcon",
			disabled: !core.js.base.controllers.ActionProvider.get("file_explorer_delete").isAllowed,
			onClick: function() {
				if (_this._selectedNode) {
					_this.onDeleteDirectory(_this._selectedNode.item);
				}
			}
		});
		this._contextMenu.addChild(this._deleteMenuItem);
		
		// Rename directory item
		this._renameMenuItem = new dijit.MenuItem({
			label: this._i18n.global._share.renameAction,
	    	iconClass: "appIcon appRenameIcon",
	    	disabled: !core.js.base.controllers.ActionProvider.get("file_explorer_rename").isAllowed,
	    	onClick: function() {
	    		if (_this._selectedNode) {
	    			_this.onRenameDirectory(_this._selectedNode.item);
	    		}
	    	}
		});
		this._contextMenu.addChild(this._renameMenuItem);
		
		this._contextMenu.addChild(new dijit.MenuSeparator());
		
		// Upload item
		this._contextMenu.addChild(new dijit.MenuItem({
			label: this._i18n.global._share.uploadAction,
			iconClass: "appIcon appUploadIcon",
			disabled: !core.js.base.controllers.ActionProvider.get("file_explorer_upload").isAllowed,
			onClick: function() {
				if (_this._selectedNode) {
					_this.onUploadFile(_this._selectedNode.item);
				}
			}
		}));
		
		// Change permissions item
		this._changePermissionsMenuItem = new dijit.MenuItem({
			label: this._i18n.explorer._share.changePermissionsAction,
			disabled: true,
			onClick: function(e) {
				if (_this._selectedNode) {
					_this.onChangePermissions(_this._selectedNode.item);
				}
			}
		});
		this._contextMenu.addChild(this._changePermissionsMenuItem);
		
		dojo.connect(this._contextMenu, "_openMyself", this, function(e) {
			// DOJO LESSON: Get the enclosing widget based on the selected target
			var widget = dijit.getEnclosingWidget(e.target);
			
			if (widget.item) {
				// If user click on the root node
				if (widget.item.root) {
					widget.item.path = ".";
				}
				this._selectedNode = widget;
				
				this.onNodeContextMenu(widget.item);
			}
		});
	},
	
	_createTree: function() {
		// summary:
		//		Creates the directory tree
		var _this  = this;
		var params = {
			format: "json",
			dirs_only: true,
			connection_id: this._connectionId
		};
		
		// Create store
		var store = new dojox.data.FileStore({
			url: core.js.base.controllers.ActionProvider.get("file_explorer_list").url + "?" + dojo.objectToQuery(params),
			pathAsQueryParam: true
		});
		store.setValues = function(newParentItem, parentAttr, childItems) {
			// Do nothing
			// This method is required to make the tree draggable
		};
		
		// Create model
		var model = new dijit.tree.ForestStoreModel({
			store: store,
			query: "{}",
			rootLabel: this._i18n.explorer.list.rootDirectory,
			rootId: this._rootTreeId
		});
		
		// DOJO LESSON: After dragging a node and dropping it within the tree, the tree will call 
		// pasteItem() method of the model to update the data.
		// By this way, Dojo wants the Tree, Model, and Store to be synchronized together.
		// In my case, the store (dojox.data.FileStore) does not provide the write API, therefore I create 
		// an empty method to set the values to item as you see above:
		//
		//		store.setValues = function(newParentItem, parentAttr, childItems) {
		//			// Do nothing
		//		};
		//
		// and the data will be updated manually as follow:
		dojo.connect(model, "pasteItem", function(/*Item*/ childItem, /*Item*/ oldParentItem, /*Item*/ newParentItem, /*Boolean*/ bCopy) {
			// Do some simple logic checking before moving directory
			if (_this._canMove(childItem, newParentItem)) {
				_this.onMoveDirectories([ childItem ], newParentItem);
			}
		});
		
		// DOJO LESSON: Don't need to override this method if I want to drag and drop within the tree.
		// The reason why it is here is that I want to drag the file from the grid and drop it on the tree.
		// Due to the flow of tree widget, it will call the newItem() from the tree model, which will make a request 
		// to the server to get the information of new item.
		// In my case, the Ajax request passes an invalid path of file (the value of "path" parameter is Id of dragged item, 
		// such as dojox_grid_EnhancedGrid_0_dndItem0)
		model.newItem = function(/* dojo.dnd.Item */ args, /*Item*/ parent, /*int?*/ insertIndex) {
			// Do nothing
		};
		
		// Create tree
		if (this._tree) {
			this._tree.destroyRecursive();
			this._tree = null;
		}
		var div = dojo.create("div", {
			id: this._id
		}, this._parentNode);
		
		this._tree = new dijit.Tree({
			model: model,
			showRoot: true,
			getIconClass: function(/*dojo.data.Item*/ item, /*Boolean*/ opened) {
				return (!item || this.model.mayHaveChildren(item)) ? (opened ? "dijitFolderOpened" : "dijitFolderClosed") : "dijitLeaf";
			},
			onClick: function(/*dojo.data.Item*/ item, /*TreeNode*/ node, /*Event*/ e) {
				_this.onOpenDirectory(item);
			},
			style: "width: 100%; height: 100%",
			// DnD
			"class": "container",
			dndController: !core.js.base.controllers.ActionProvider.get("file_explorer_move").isAllowed ? "dijit.tree._dndSelector" : "dijit.tree.dndSource",
			checkAcceptance: function(source, nodes) {
				if (source instanceof dojox.grid.enhanced.plugins.GridDnDSource) {
					return true;
				}
				return (source.tree && source.tree.id == _this._id);
			},
			checkItemAcceptance: function(node, source, position) {
				if (source instanceof dojox.grid.enhanced.plugins.GridDnDSource) {
					return true;
				}
				if (!source.tree || source.tree.id != _this._id) {
					return false;
				}
				
				var targetNode = dijit.getEnclosingWidget(node);
				if (!targetNode) {
					return false;
				}
				var sourceItem = null;
				targetItem = targetNode.item;
				for (var i in source.selection) {
					sourceItem = source.selection[i].item;
				}
				
				return _this._canMove(sourceItem, targetItem);
			}
		}, this._id);
		
		// Track the hover node
		dojo.connect(this._tree, "_onNodeMouseEnter", this, function(node) {
			this._hoverNode = node;
		});
		dojo.connect(this._tree, "_onNodeMouseLeave", this, function(node) {
			this._hoverNode = null;
		});
		
		// Allow to drag file from the grid to the tree
		var treeTarget = new dojox.grid.enhanced.plugins.GridSource(dojo.byId(this._id), {
			isSource: false,
			insertNodesForGrid: false
		});
		dojo.connect(treeTarget, "onDropGridRows", this, function(grid, rowIndexes) {
			// Determine the source items from the grid view
			var sourceItems = [];
			dojo.forEach(rowIndexes, function(rowIndex, index) {
				var item = grid.getItem(rowIndex);
				if (item) {
					sourceItems.push(item);
				}
			});
			
			var _this = this;
			// The target item is already tracked by listening _onNodeMouseEnter() and _onNodeMouseLeave() events
			if (this._hoverNode) {
				var targetItem = this._hoverNode.item;
				
				var movableSourceItems = [];
				dojo.forEach(sourceItems, function(item, index) {
					if (_this._canMove(item, targetItem)) {
						movableSourceItems.push(item);
					}
				});
				
				_this.onMoveDirectories(movableSourceItems, targetItem);
			}
		});
		
		// Attach the context menu
		// DOJO LESSON: This is another way to show the context menu
		this._contextMenu.bindDomNode(this._tree.domNode);
	},
	
	disconnect: function() {
		// summary:
		//		Disconnects. It should be called after disconnecting
		if (this._tree) {
			this._tree.destroyRecursive();
			this._tree = null;
		}
	},
	
	show: function(/*String*/ path, /*Boolean*/ reload) {
		// summary:
		//		Shows the tree
		// path:
		//		If this parameter is passed, the tree will load the path
		// reload:
		//		If true, re-create the tree
		if (this._currentPath == path && reload === false) {
			return;
		}
		this._currentPath = path;
		
		if (reload) {
			this._createTree();
		}
		
		// Init the tree with given path
		// The valid call is:
		//		this._tree.attr("path", ["DirectoryRoot", "./folder", "./folder/subFolder", "./folder/subFolder/subOfSubFolder"]);
		// where "DirectoryRoot" is rootId defined by the model
		if (path) {
			while (path[0] == "." || path[0] == "/") {
				path = path.substring(1);
			}
			var paths = new Array(this._tree.model.rootId);
			if (path != "") {
				var str = ".";
				dojo.forEach(path.split('/'), function(folder, index) {
					str += "/" + folder;
					paths.push(str);
				});
			}
			this._tree.attr("path", paths);
		}
	},
	
	_canMove: function(/*dojo.data.Item*/ sourceItem, /*dojo.data.Item*/ targetItem) {
		// summary:
		//		Checks if it is possible to move item or not
		// Do not allow to move to the current parent node,
		if (sourceItem.parentDir == targetItem.path) {
			return false;
		}
		// and its chidlren node
		if (targetItem.path.substr(0, sourceItem.path.length) == sourceItem.path) {
			return false;
		}
		return true;
	},
	
	////////// UPDATE STATE OF CONTROLS //////////

	allowToBookmark: function(/*Boolean*/ isAllowed) {
		// summary:
		//		Allows/disallows to bookmark the directory
		var _this = this;
		isAllowed = isAllowed && core.js.base.controllers.ActionProvider.get("file_bookmark_add").isAllowed;
		this._bookmarkMenuItem.set("label", this._i18n.bookmark._share.bookmarkAction);
		this._bookmarkMenuItem.set("disabled", !isAllowed);
		this._bookmarkMenuItem.onClick = function(e) {
			if (_this._selectedNode && _this._selectedNode.item && _this._selectedNode.item.directory) {
				_this.onBookmarkDirectory(_this._selectedNode.item);
			}
		};
		return this;	// file.js.views.DirectoryTreeView
	},
	
	allowToChangePermissions: function(/*Boolean*/ isAllowed) {
		// summary:
		//		Allows/disallows to update the permissions on the directory
		isAllowed = isAllowed && core.js.base.controllers.ActionProvider.get("file_explorer_perm").isAllowed;
		this._changePermissionsMenuItem.set("disabled", !isAllowed);
		return this;	// file.js.views.DirectoryTreeView
	},
	
	allowToCopy: function(/*Boolean*/ isAllowed) {
		// summary:
		//		Allows/disallows to copy directory
		isAllowed = isAllowed && core.js.base.controllers.ActionProvider.get("file_explorer_copy").isAllowed;
		this._copyMenuItem.set("disabled", !isAllowed);
		return this;	// file.js.views.DirectoryTreeView
	},
	
	allowToCut: function(/*Boolean*/ isAllowed) {
		// summary:
		//		Allows/disallows to cut directory
		isAllowed = isAllowed && core.js.base.controllers.ActionProvider.get("file_explorer_move").isAllowed;
		this._cutMenuItem.set("disabled", !isAllowed);
		return this;	// file.js.views.DirectoryTreeView
	},
	
	allowToDelete: function(/*Boolean*/ isAllowed) {
		// summary:
		//		Allows/disallows to delete directory
		isAllowed = isAllowed && core.js.base.controllers.ActionProvider.get("file_explorer_delete").isAllowed;
		this._deleteMenuItem.set("disabled", !isAllowed);
		return this;	// file.js.views.DirectoryTreeView
	},
	
	allowToPaste: function(/*Boolean*/ isAllowed, /*String*/ action) {
		// summary:
		//		Allows/disallows to copy/move directory
		// action:
		//		Can be "copy" or "move"
		isAllowed = isAllowed && ((action == "copy") 
									? core.js.base.controllers.ActionProvider.get("file_explorer_copy").isAllowed
									: core.js.base.controllers.ActionProvider.get("file_explorer_move").isAllowed);
		this._pasteMenuItem.set("disabled", !isAllowed);
		this._pasteWithoutOverwritingMenuItem.set("disabled", !isAllowed);
		return this;	// file.js.views.DirectoryTreeView
	},
	
	allowToRename: function(/*Boolean*/ isAllowed) {
		// summary:
		//		Allows/disallows to rename directory
		isAllowed = isAllowed && core.js.base.controllers.ActionProvider.get("file_explorer_rename").isAllowed;
		this._renameMenuItem.set("disabled", !isAllowed);
		return this;	// file.js.views.DirectoryTreeView
	},
	
	allowToUnbookmark: function() {
		// summary:
		//		Allows/disallows to remove bookmark of directory
		var _this = this;
		var isAllowed = core.js.base.controllers.ActionProvider.get("file_bookmark_delete").url;
		this._bookmarkMenuItem.set("label", this._i18n.bookmark._share.unbookmarkAction);
		this._bookmarkMenuItem.set("disabled", !isAllowed);
		this._bookmarkMenuItem.onClick = function(e) {
			if (_this._selectedNode && _this._selectedNode.item && _this._selectedNode.item.directory) {
				_this.onUnbookmarkDirectory(_this._selectedNode.item);
			}
		};
		return this;	// file.js.views.DirectoryTreeView
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
	
	onCopyDirectory: function(/*dojo.data.Item*/ sourceItem) {
		// tags:
		//		callback
	},
	
	onCreateDirectory: function(/*dojo.data.Item*/ item) {
		// tags:
		//		callback
	},
	
	onCutDirectory: function(/*dojo.data.Item*/ sourceItem) {
		// tags:
		//		callback
	},
	
	onDeleteDirectory: function(/*dojo.data.Item*/ item) {
		// tags:
		//		callback
	},
	
	onMoveDirectories: function(/*dojo.data.Item[]*/ sourceItems, /*dojo.data.Item*/ targetItem) {
		// summary:
		//		This method is called after moving a directory node to other node
		// tags:
		//		callback
	},
	
	onNodeContextMenu: function(/*dojo.data.Item*/ item) {
		// tags:
		//		callback
	},
	
	onOpenDirectory: function(/*dojo.data.Item*/ item) {
		// tags:
		//		callback
	},

	onPasteDirectory: function(/*dojo.data.Item*/ targetItem, /*Boolean*/ overwrite) {
		// summary:
		//		This method is called when the Paste menu item is clicked
		// overwrite:
		//		If true, the action will overwrite the existent files
		// tags:
		//		callback
	},
	
	onRenameDirectory: function(/*dojo.data.Item*/ item) {
		// tags:
		//		callback
	},
	
	onUnbookmarkDirectory: function(/*dojo.data.Item*/ item) {
		// tags:
		//		callback
	},
	
	onUploadFile: function(/*dojo.data.Item*/ item) {
		// tags:
		//		callback
	}
});
