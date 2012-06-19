/**
 * NextCMS
 * 
 * @author		Nguyen Huu Phuoc <thenextcms@gmail.com>
 * @copyright	Copyright (c) 2011 - 2012, Nguyen Huu Phuoc
 * @license		http://nextcms.org/license.txt	(GNU GPL version 2 or later)
 * @link		http://nextcms.org
 * @category	modules
 * @package		menu
 * @subpackage	js
 * @since		1.0
 * @version		2011-10-23
 */

dojo.provide("menu.js.views.ItemTreeView");

dojo.require("dijit.Menu");
dojo.require("dijit.MenuItem");
dojo.require("dijit.Tree");
dojo.require("dijit.tree.dndSource");
dojo.require("dijit.tree.ForestStoreModel");
dojo.require("dojo.data.ItemFileWriteStore");

dojo.require("core.js.base.I18N");

dojo.declare("menu.js.views.ItemTreeView", null, {
	// _id: String
	//		Id of container
	_id: null,
	
	// _i18n: Object
	_i18n: null,
	
	// _tree: dijit.Tree
	_tree: null,
	
	// _uniqueItemId: Integer,
	_uniqueItemId: 1,
	
	// _selectedNode: dijit._TreeNode
	_selectedNode: null,
	
	// _contextMenu: dijit.Menu
	_contextMenu: null,
	
	// _editMenuItem: dijit.MenuItem
	_editMenuItem: null,
	
	// _deleteMenuItem: dijit.MenuItem
	_deleteMenuItem: null,
	
	constructor: function(/*String*/ id) {
		this._id = id;
		
		core.js.base.I18N.requireLocalization("menu/languages");
		this._i18n = core.js.base.I18N.getLocalization("menu/languages");
		
		dojo.empty(this._id);
		this._createContextMenu();
		this._createTree();
	},
	
	_createContextMenu: function() {
		// summary:
		//		Creates the context menu for the tree view
		var contextMenuId = this._id + "_ContextMenu";
		var div = dojo.create("div", {
			id: contextMenuId
		}, this._id);
		this._contextMenu = new dijit.Menu({
			targetNodeIds: [ contextMenuId ]
		});

		var _this = this;
		
		// "Edit" menu item
		this._editMenuItem = new dijit.MenuItem({
			label: this._i18n.global._share.editAction,
			onClick: function() {
				if (_this._selectedNode) {
					_this.onEditItem(_this._selectedNode.item);
				}
			}
		});
		this._contextMenu.addChild(this._editMenuItem);
		
		// "Delete" menu item
		this._deleteMenuItem = new dijit.MenuItem({
			label: this._i18n.global._share.deleteAction,
			iconClass: "appIcon appDeleteIcon",
			onClick: function() {
				if (_this._selectedNode) {
					_this._deleteItem(_this._selectedNode.item);
				}
			}
		});
		this._contextMenu.addChild(this._deleteMenuItem);
		
		// DOJO LESSON: There is another way to get the last selected item
		//		dojo.subscribe(this._tree.id, null, function(message){
		//			if (message.event == "execute") {
		//				console.log(message.item);
		//			}
		//		});
		
		dojo.connect(this._contextMenu, "_openMyself", this, function(e) {
			// Here is a way to get the selected node
			var widget = dijit.getEnclosingWidget(e.target);
			
			if (widget.item) {
				this._selectedNode = widget;
				
				// Don't allow to edit, delete the root item
				this._editMenuItem.set("disabled", widget.item.item_id[0] == 0);
				this._deleteMenuItem.set("disabled", widget.item.item_id[0] == 0);
				
				this.onNodeContextMenu(widget.item);
			}
		});
	},
	
	_createTree: function(/*Object*/ items) {
		// summary:
		//		Creates the tree
		var children = items ? items.items : [];
		var store = new dojo.data.ItemFileWriteStore({
			data: {
				identifier: "item_id",
				label: "title",
				items: [{
					item_id: 0,
					title: this._i18n.menu._share.treeRootLabel,
					children: children
				}]
			}
		});
		
		var model = new dijit.tree.ForestStoreModel({
			store: store,
			rootLabel: this._i18n.menu._share.treeRootLabel,
			rootId: this._id + "Root",
			query: { 
				item_id: 0
			}
		});
		
		if (this._tree) {
			this._tree.destroyRecursive();
			this._tree = null;
		}
		
		var _this = this;
		var div = dojo.create("div", {}, this._id);
		this._tree = new dijit.Tree({
			model: model,
			showRoot: false,
			persist: false,
			// Dnd
			betweenThreshold: 5,
			dndController: "dijit.tree.dndSource",
			checkAcceptance: function(source, nodes) {
				return (source.tree && source.tree.id == _this._tree.id);
			},
			checkItemAcceptance: function(node, source, position) {
				if (!source.tree || source.tree.id != _this._tree.id) {
					return false;
				}
				return true;
			}
		}, div);
		this._contextMenu.bindDomNode(this._tree.domNode);
	},
	
	saveItem: function(/*Object*/ item) {
		// summary:
		//		Adds new item to the tree or updates given item if the item_id property is defined
		// item:
		//		Contains data of menu item, including the following members:
		//		title, sub_title, description, link, image, target
		if (!item.title || !item.link) {
			return;
		}
		
		var store = this._tree.model.store;
		if (!item.item_id) {
			// Add new item
			this._uniqueItemId++;
			item.item_id = "ItemTreeView_Id_" + this._uniqueItemId;
			
			var parentInfo = {
				parent: this._tree._getRootOrFirstNode().item,	// Root item
				attribute: "children"
			};
			// DOJO LESSON: Add new item
			store.newItem(item, parentInfo);
		} else {
			// Update given item
			store.fetchItemByIdentity({
				identity: item.item_id,
				onItem: function(foundItem) {
					for (var property in item) {
						// DOJO LESSON: Update item attribute
						if (property != "item_id") {
							store.setValue(foundItem, property, item[property]);
						}
					}
				}
			});
		}
	},
	
	_deleteItem: function(/*dojo.data.Item*/ item) {
		// summary:
		//		Deletes given item
		var itemId = item.item_id[0];
		var store = this._tree.model.store;
		store.fetch({ 
			query: {
				item_id: itemId
			},
			queryOptions: {
				deep: true
			},
			onComplete: function(items, request) {
				if (items) {
					for (var i = 0; i < items.length; i++) {
						// DOJO LESSON: Delete item
						store.deleteItem(items[i]);
					}
				}
			}
		});
		
		// FIXME: Set the selected node (if not, I cannot drag and drop tree node)
//		var paths = [ this._tree.model.rootId, this._tree._getRootOrFirstNode().id ];
//		console.log(paths);
//		this._tree.set("paths", paths);
	},
	
	getItems: function() {
		// summary:
		//		Gets menu items of the tree
		var menuItems = [];
		var _this = this;
		this._tree.model.store.fetch({ 
			query: null,
			onComplete: function(items, request) {
				var rootItem = items[0];
				menuItems = _this._getChildren(rootItem.children);
			}
		});
		return menuItems;	// Array
	},
	
	_getChildren: function(items) {
		// summary:
		//		Gets children item
		var result = [];
		var _this = this;
		dojo.forEach(items, function(child) {
			result.push({
				title: child.title[0],
				sub_title: child.sub_title[0],
				description: child.description[0],
				link: child.link[0],
				image: child.image[0],
				target: child.target[0],
				html_id: child.html_id[0],
				css_class: child.css_class[0],
				css_style: child.css_style[0],
				children: _this._getChildren(child.children)
			});
		});
		return result;		// Array
	},
	
	setItems: function(/*Object*/ items) {
		// summary:
		//		Sets items for the tree
		this._createTree(items);
	},
	
	////////// CALLBACKS //////////
	
	onEditItem: function(/*dojo.data.Item*/ item) {
		// summary:
		//		Edits given menu item
		// tags:
		//		callback
	},
	
	onNodeContextMenu: function(/*dojo.data.Item*/ item) {
		// summary:
		//		Called when right-click on tree node
		// tags:
		//		callback
	}
});
