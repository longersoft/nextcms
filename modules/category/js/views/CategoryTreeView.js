/**
 * NextCMS
 * 
 * @author		Nguyen Huu Phuoc <thenextcms@gmail.com>
 * @copyright	Copyright (c) 2011 - 2012, Nguyen Huu Phuoc
 * @license		http://nextcms.org/license.txt	(GNU GPL version 2 or later)
 * @link		http://nextcms.org
 * @category	modules
 * @package		category
 * @subpackage	js
 * @since		1.0
 * @version		2012-03-30
 */

dojo.provide("category.js.views.CategoryTreeView");

dojo.require("dijit.Menu");
dojo.require("dijit.MenuItem");
dojo.require("dijit.MenuSeparator");
dojo.require("dijit.Tree");
dojo.require("dijit.tree.dndSource");
dojo.require("dijit.tree.ForestStoreModel");
dojo.require("dojo.data.ItemFileReadStore");
dojo.require("dojo.data.ItemFileWriteStore");

dojo.require("core.js.base.Config");
dojo.require("core.js.base.controllers.ActionProvider");
dojo.require("core.js.base.Encoder");
dojo.require("core.js.base.I18N");

dojo.declare("category.js.views.CategoryTreeView", null, {
	// _id: String
	_id: null,
	
	// _rootTreeId: String
	_rootTreeId: null,
	
	// _parentNode: DomNode
	_parentNode: null,
	
	// _module: String
	_module: null,
	
	// _tree: dijit.Tree
	_tree: null,
	
	// _selectedNode: dijit._TreeNode
	_selectedNode: null,
	
	// _i18n: Object
	_i18n: null,
	
	// _language: String
	_language: null,
	
	////////// CONTEXT MENU //////////
	
	// _contextMenu: dijit.Menu
	_contextMenu: null,
	
	// _addMenuItem: dijit.MenuItem
	_addMenuItem: null,
	
	// _addSubCatgeoryMenuItem: dijit.MenuItem
	_addSubCatgeoryMenuItem: null,
	
	// _editMenuItem: dijit.MenuItem
	_editMenuItem: null,
	
	// _deleteMenuItem: dijit.MenuItem
	_deleteMenuItem: null,
	
	// _renameMenuItem: dijit.MenuItem
	_renameMenuItem: null,
	
	constructor: function(/*String*/ id) {
		this._id = id;
		this._rootTreeId = id + "Root";
		this._parentNode = dojo.byId(id).parentNode;
		
		core.js.base.I18N.requireLocalization("category/languages");
		this._i18n = core.js.base.I18N.getLocalization("category/languages");
		
		this._createContextMenu();
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
		
		// "Add" menu item
		this._addMenuItem = new dijit.MenuItem({
			label: this._i18n.global._share.addAction,
			iconClass: "appIcon appAddIcon",
			disabled: !core.js.base.controllers.ActionProvider.get("category_category_add").isAllowed,
			onClick: function() {
				_this.onAddCategory(null);
			}
		});
		this._contextMenu.addChild(this._addMenuItem);
		
		// "Add sub category" menu item
		this._addSubCatgeoryMenuItem = new dijit.MenuItem({
			label: this._i18n.category._share.addSubCategoryAction,
			disabled: !core.js.base.controllers.ActionProvider.get("category_category_add").isAllowed,
			onClick: function() {
				if (_this._selectedNode) {
					_this.onAddCategory(_this._selectedNode.item);
				}
			}
		});
		this._contextMenu.addChild(this._addSubCatgeoryMenuItem);
		
		this._contextMenu.addChild(new dijit.MenuSeparator());
		
		// "Edit" menu item
		this._editMenuItem = new dijit.MenuItem({
			label: this._i18n.global._share.editAction,
			disabled: !core.js.base.controllers.ActionProvider.get("category_category_edit").isAllowed,
			onClick: function() {
				if (_this._selectedNode) {
					_this.onEditCategory(_this._selectedNode.item);
				}
			}
		});
		this._contextMenu.addChild(this._editMenuItem);
		
		// "Delete" menu item
		this._deleteMenuItem = new dijit.MenuItem({
			label: this._i18n.global._share.deleteAction,
			iconClass: "appIcon appDeleteIcon",
			disabled: !core.js.base.controllers.ActionProvider.get("category_category_delete").isAllowed,
			onClick: function() {
				if (_this._selectedNode) {
					_this.onDeleteCategory(_this._selectedNode.item);
				}
			}
		});
		this._contextMenu.addChild(this._deleteMenuItem);
		
		// "Rename" menu item
		this._renameMenuItem = new dijit.MenuItem({
			label: this._i18n.global._share.renameAction,
			iconClass: "appIcon appRenameIcon",
			disabled: !core.js.base.controllers.ActionProvider.get("category_category_rename").isAllowed,
			onClick: function() {
				if (_this._selectedNode) {
					_this.onRenameCategory(_this._selectedNode.item);
				}
			}
		});
		this._contextMenu.addChild(this._renameMenuItem);
		
		// "Localize" menu item
		var languages = core.js.base.Config.get("core", "localization_languages");
		if (languages) {
			var localizePopupMenu = new dijit.Menu();
			for (var locale in languages) {
				localizePopupMenu.addChild(new dijit.MenuItem({
					__locale: locale,
					label: languages[locale],
					iconClass: "appIcon appFlag_" + locale,
					onClick: function(e) {
						if (_this._selectedNode) {
							var item = _this._selectedNode.item;
							if (item) {
								var translations = dojo.fromJson(item.translations[0]);
								if (translations[this.__locale]) {
									_this.onEditCategory(translations[this.__locale]);
								} else {
									_this.onTranslateCategory(item, this.__locale);
								}
							}
						}
					}
				}));
			}
			
			this._contextMenu.addChild(new dijit.MenuSeparator());
			this._contextMenu.addChild(new dijit.PopupMenuItem({
				label: this._i18n.global._share.localizeAction,
				popup: localizePopupMenu
			}));
		}
		
		dojo.connect(this._contextMenu, "_openMyself", this, function(e) {
			var widget = dijit.getEnclosingWidget(e.target);
			
			if (widget.item) {
				this._selectedNode = widget;
				this.onNodeContextMenu(widget.item);
			}
		});
	},
	
	setModule: function(/*String*/ module) {
		// summary:
		//		Sets the module's name
		this._module = module;
		return this;	// category.js.views.CategoryTreeView
	},
	
	render: function(/*String?*/ language) {
		// summary:
		//		Shows the category tree
		if (language) {
			this._language = language;
		}
		
		if (this._tree) {
			this._tree.destroyRecursive();
			this._tree = null;
		}
		
		var url = core.js.base.controllers.ActionProvider.get("category_category_list").url;
		var params = { 
			format: "json", 
			mod: this._module,
			language: this._language
		};
		
		var _this = this;
		var div	  = dojo.create("div", {
			id: this._id
		}, this._parentNode);
		// var store = new dojo.data.ItemFileReadStore({
		var store = new dojo.data.ItemFileWriteStore({
			url: url + "?" + dojo.objectToQuery(params)
		});
//		store.setValues = function(newParentItem, parentAttr, childItems) {
//		};
		
		var model = new dijit.tree.ForestStoreModel({
			store: store,
			rootLabel: this._i18n.category.list.rootLabel,
			rootId: this._rootTreeId
		});
		model.newItem = function(/*dojo.dnd.Item*/ args, /*Item*/ parent, /*int?*/ insertIndex) {
			if (parent.root || args.category_id == parent.category_id[0]) {
				return;
			}
			_this.onDropExternalSource(args, parent);
		};
		dojo.connect(model, "pasteItem", this, function(/*Item*/ childItem, /*Item*/ oldParentItem, /*Item*/ newParentItem, /*Boolean*/ bCopy, /*Int?*/ insertIndex) {
			if (this._canMove(childItem, newParentItem)) {
				this.onMoveCategory(childItem, newParentItem);
			}
		});
		
		this._tree = new dijit.Tree({
			betweenThreshold: 5,
			model: model,
			showRoot: true,
			onClick: function(item, node, evt) {
				_this.onSelectCategory(item);
			},
			// Dnd
			dndController: !core.js.base.controllers.ActionProvider.get("category_category_move").isAllowed ? "dijit.tree._dndSelector" : "dijit.tree.dndSource",
			itemCreator: function(nodes, target, source) {
				// DOJO LESSON: Override the itemCreator() method when dragging
				// the external source to the tree
				// Each item data will be passed as the first parameter of the model's newItem() method
				//		model.newItem = function(/*dojo.dnd.Item*/ args, /*Item*/ parent, /*int?*/ insertIndex) {
				//			console.log(args);
				//		};
				var items = [];
				dojo.forEach(nodes, function(node) {
					var data = dojo.attr(node, "data-app-dndcategory");
					if (data) {
						data = core.js.base.Encoder.decode(data);
						items.push(data);
					}
				});
				return items;
			},
			checkAcceptance: function(source, nodes) {
				if (source.tree && source.tree.id == _this._id) {
					// Drag node inside the tree
					return true;
				}
				var allowed = true, n = nodes.length;
				for (var i = 0; i < n; i++) {
					// DOC: If you want to drag external source to the category tree,
					// define the "data-app-dndcategory" attribute to the dragged node
					if (!dojo.attr(nodes[i], "data-app-dndcategory")) {
						allowed = false;
						break;
					}
				}
				return allowed;
			},
			checkItemAcceptance: function(node, source, position) {
				return true;
//				if (!source.tree || source.tree.id != _this._id) {
//					return false;
//				}
//				return true;
			}
		}, this._id);
		
		// Add the context menu
		this._contextMenu.bindDomNode(this._tree.domNode);
	},
	
	_getParentCategories: function(/*dojo.data.Item*/ item) {
		// summary:
		//		Gets array of parent categories from the root to given item
		var nodes = this._tree.getNodesByItem(item);
		if (nodes.length == 0) {
			return [];		// Array
		}
		var parent = [];
		dojo.forEach(nodes[0].getTreePath(), function(pathItem) {
			if (!pathItem.root) {
				parent.push(pathItem.category_id[0]);
			}
		});
		return parent;	// Array
	},
	
	_canMove: function(/*dojo.data.Item*/ sourceItem, /*dojo.data.Item*/ targetItem) {
		// summary:
		//		Checks if user can move a category
		
		// Don't allow to move if:
		// - The source node is the root one
		if (sourceItem.root) {
			return false;	// Boolean
		}
		var parentOfSource = this._getParentCategories(sourceItem);
		var parentOfTarget = this._getParentCategories(targetItem);
		
		// - If the source node is already child of the target node
		if ((parentOfSource.length == parentOfTarget.length + 1) && (parentOfSource.splice(0, parentOfTarget.length).join("/") == parentOfTarget.join("/"))) {
			return false;	// Boolean
		}
		// - If the target node is child of the source node
		if ((parentOfTarget.length >= parentOfSource.length) && (parentOfTarget.join("/").substr(0, parentOfSource.join("/").length) == parentOfSource.join("/"))) {
			return false;	// Boolean
		}
		return true;
	},
	
	////////// CONTROL STATE //////////
	
	allowToAddSubCategory: function(/*Boolean*/ isAllowed) {
		// summary:
		//		Allows/disallows to add a sub category
		this._addSubCatgeoryMenuItem.set("disabled", !core.js.base.controllers.ActionProvider.get("category_category_add").isAllowed || !isAllowed);
		return this;	// category.js.views.CategoryTreeView
	},
	
	allowToDelete: function(/*Boolean*/ isAllowed) {
		// summary:
		//		Allows/disallows to delete the category
		this._deleteMenuItem.set("disabled", !core.js.base.controllers.ActionProvider.get("category_category_delete").isAllowed || !isAllowed);
		return this;	// category.js.views.CategoryTreeView
	},
	
	allowToEdit: function(/*Boolean*/ isAllowed) {
		// summary:
		//		Allows/disallows to edit the category
		this._editMenuItem.set("disabled", !core.js.base.controllers.ActionProvider.get("category_category_edit").isAllowed || !isAllowed);
		return this;	// category.js.views.CategoryTreeView
	},
	
	allowToRename: function(/*Boolean*/ isAllowed) {
		// summary:
		//		Allows/disallows to rename the category
		this._renameMenuItem.set("disabled", !core.js.base.controllers.ActionProvider.get("category_category_rename").isAllowed || !isAllowed);
		return this;	// category.js.views.CategoryTreeView
	},
	
	////////// CALLBACKS //////////
	
	onAddCategory: function(/*dojo.data.Item?*/ item) {
		// summary:
		//		Adds new category
		// item:
		//		The parent category item
		// tags:
		//		callback
	},
	
	onDeleteCategory: function(/*dojo.data.Item*/ item) {
		// summary:
		//		Deletes given category item
		// tags:
		//		callback
	},
	
	onDropExternalSource: function(/*dojo.dnd.Item*/ externalItem, /*dojo.data.Item*/ categoryItem) {
		// summary:
		//		Moves external item to the category tree
		// tags:
		//		callback
	},
	
	onEditCategory: function(/*dojo.data.Item|String*/ item) {
		// summary:
		//		Edits given category item
		// tags:
		//		callback
	},
	
	onMoveCategory: function(/*dojo.data.Item*/ sourceItem, /*dojo.data.Item*/ targetItem) {
		// summary:
		//		Moves given category item to other one
		// tags:
		//		callback
	},
	
	onNodeContextMenu: function(/*dojo.data.Item*/ item) {
		// summary:
		//		Called when right-click on the category item
		// tags:
		//		callback
	},
	
	onRenameCategory: function(/*dojo.data.Item*/ item) {
		// summary:
		//		Renames given category item
		// tags:
		//		callback
	},
	
	onSelectCategory: function(/*dojo.data.Item*/ item) {
		// summary:
		//		Called when selecting a category item
		// tags:
		//		callback
	},
	
	onTranslateCategory: function(/*dojo.data.Item*/ item, /*String*/ language) {
		// summary:
		//		Translates given category item to other language
		// tags:
		//		callback
	}
});
