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
 * @version		2012-06-18
 */

dojo.provide("category.js.controllers.CategoryController");

dojo.require("core.js.base.controllers.ActionProvider");
dojo.require("core.js.base.controllers.Subscriber");
dojo.require("core.js.base.I18N");
dojo.require("core.js.base.views.Helper");
dojo.require("category.js.controllers.CategoryMediator");

dojo.declare("category.js.controllers.CategoryController", null, {
	// _id: String
	_id: null,
	
	// _module: String
	//		Name of module
	_module: null,
	
	// _language: String
	_language: null,
	
	// _i18n: Object
	_i18n: null,

	// _helper: core.js.base.views.Helper
	_helper: null,
	
	// _toolbar: category.js.views.CategoryToolbar
	_toolbar: null,
	
	// _tree: category.js.views.CategoryTreeView
	_tree: null,
	
	// _mediator: category.js.controllers.CategoryMediator
	_mediator: new category.js.controllers.CategoryMediator(),
	
	// TOPIC_GROUP: [const] String
	TOPIC_GROUP: "/category/js/controllers/CategoryController",
	
	constructor: function(/*String*/ id) {
		core.js.base.controllers.Subscriber.unsubscribe(this.TOPIC_GROUP);
		
		this._id = id;
		core.js.base.I18N.requireLocalization("category/languages");
		this._i18n = core.js.base.I18N.getLocalization("category/languages");
	},
	
	setModule: function(/*String*/ module) {
		// summary:
		//		Sets the module's name
		this._module = module;
		return this;	// category.js.controllers.CategoryController
	},
	
	setLanguage: function(/*String*/ language) {
		// summary:
		//		Sets the language
		// language:
		//		The language in format of languagecode_COUNTRYCODE
		this._language = language;
		return this;	// category.js.controllers.CategoryController
	},
	
	setHelperContainer: function(/*String*/ containerId) {
		// summary:
		//		Sets the Id of helper container
		this._helper = new core.js.base.views.Helper(containerId);
		this._helper.setLanguageData(this._i18n);
		return this;	// category.js.controllers.CategoryController
	},
	
	setCategoryToolbar: function(/*category.js.views.CategoryToolbar*/ toolbar) {
		// summary:
		//		Sets the category toolbar
		this._toolbar = toolbar;
		
		// Set the language
		this._toolbar.setLanguage(this._language);
		
		// Add new category handler
		dojo.connect(toolbar, "onAddCategory", this, "addCategory");
		core.js.base.controllers.Subscriber.subscribe(this.TOPIC_GROUP, "/app/category/category/add/onCancel", this, function() {
			this._helper.removePane();
		});
		core.js.base.controllers.Subscriber.subscribe(this.TOPIC_GROUP, "/app/category/category/add/onStart", this, function() {
			this._helper.showStandby();
		});
		core.js.base.controllers.Subscriber.subscribe(this.TOPIC_GROUP, "/app/category/category/add/onComplete", this, function(data) {
			this._helper.hideStandby();
			this._helper.removePane();
			dojo.publish("/app/global/notification", [{
				message: this._i18n.category.add[data.result == "APP_RESULT_OK" ? "success" : "error"],
				type: (data.result == "APP_RESULT_OK") ? "message" : "error"
			}]);
		});
		
		// Refresh handler
		dojo.connect(toolbar, "onRefresh", this, function() {
			if (this._tree) {
				this._tree.render(this._language);
			}
		});
		
		// Switch to other language handler
		dojo.connect(toolbar, "onSwitchToLanguage", this, function(language) {
			this._language = language;
			if (this._tree) {
				this._tree.render(this._language);
				
				// Extension point
				dojo.publish("/app/category/controllers/CategoryController/onSwitchToLanguage_" + this._module, [ language ]);
			}
		});
		
		return this;	// category.js.views.CategoryToolbar
	},
	
	setCategoryTreeView: function(/*category.js.views.CategoryTreeView*/ tree) {
		// summary:
		//		Sets the category tree view
		this._mediator.setCategoryTreeView(tree);
		
		this._tree = tree;
		this._tree.setModule(this._module)
				  .render(this._language);
		
		// Reload the tree after adding new category
		core.js.base.controllers.Subscriber.subscribe(this.TOPIC_GROUP, "/app/category/category/add/onComplete", this, function(data) {
			if (data.result == "APP_RESULT_OK") {
				tree.render(this._language);
			}
		});
		
		// Select category handler
		dojo.connect(tree, "onSelectCategory", this, function(item) {
			var categoryId = item.root ? null : item.category_id[0];
			
			// Extension point
			dojo.publish("/app/category/controllers/CategoryController/onSelectCategory_" + this._module, [ categoryId ]);
		});
		
		// Add category handler
		dojo.connect(tree, "onAddCategory", this, "addCategory");
		
		// Edit handler
		dojo.connect(tree, "onEditCategory", this, "editCategory");
		core.js.base.controllers.Subscriber.subscribe(this.TOPIC_GROUP, "/app/category/category/edit/onCancel", this, function() {
			this._helper.removePane();
		});
		core.js.base.controllers.Subscriber.subscribe(this.TOPIC_GROUP, "/app/category/category/edit/onComplete", this, function(data) {
			this._helper.removePane();
			dojo.publish("/app/global/notification", [{
				message: this._i18n.category.edit[data.result == "APP_RESULT_OK" ? "success" : "error"],
				type: (data.result == "APP_RESULT_OK") ? "message" : "error"
			}]);
			if (data.result == "APP_RESULT_OK") {
				tree.render(this._language);
			}
		});
		
		// Delete handler
		dojo.connect(tree, "onDeleteCategory", this, "deleteCategory");
		core.js.base.controllers.Subscriber.subscribe(this.TOPIC_GROUP, "/app/category/category/delete/onCancel", this, function() {
			this._helper.closeDialog();
		});
		core.js.base.controllers.Subscriber.subscribe(this.TOPIC_GROUP, "/app/category/category/delete/onStart", this, function() {
			this._helper.showStandby();
		});
		core.js.base.controllers.Subscriber.subscribe(this.TOPIC_GROUP, "/app/category/category/delete/onComplete", this, function(data) {
			this._helper.hideStandby();
			this._helper.closeDialog();
			dojo.publish("/app/global/notification", [{
				message: this._i18n.category["delete"][data.result == "APP_RESULT_OK" ? "success" : "error"],
				type: (data.result == "APP_RESULT_OK") ? "message" : "error"
			}]);
			if (data.result == "APP_RESULT_OK") {
				tree.render(this._language);
			}
		});
		
		// Rename handler
		dojo.connect(tree, "onRenameCategory", this, "renameCategory");
		core.js.base.controllers.Subscriber.subscribe(this.TOPIC_GROUP, "/app/category/category/rename/onCancel", this, function() {
			this._helper.closeDialog();
		});
		core.js.base.controllers.Subscriber.subscribe(this.TOPIC_GROUP, "/app/category/category/rename/onComplete", this, function(data) {
			this._helper.closeDialog();
			dojo.publish("/app/global/notification", [{
				message: this._i18n.category.rename[data.result == "APP_RESULT_OK" ? "success" : "error"],
				type: (data.result == "APP_RESULT_OK") ? "message" : "error"
			}]);
			if (data.result == "APP_RESULT_OK") {
				tree.render(this._language);
			}
		});
		
		// Move handler
		dojo.connect(tree, "onMoveCategory", this, "moveCategory");
		
		// Drop external item to the tree
		dojo.connect(tree, "onDropExternalSource", this, function(/*dojo.dnd.Item*/ externalItem, /*dojo.data.Item*/ categoryItem) {
			var category = {
				category_id: categoryItem.category_id[0],
				name: categoryItem.name[0]
			};
			dojo.publish("/app/category/controllers/CategoryController/onDropExternalSource_" + this._module, [{ source: externalItem, category: category }]);
		});
		
		// Translate category handler
		dojo.connect(tree, "onTranslateCategory", this, "translateCategory");
		
		return this;	// category.js.views.CategoryToolbar
	},
	
	addCategory: function(/*dojo.data.Item?*/ item) {
		// summary:
		//		Adds new category
		// item:
		//		The parent category item
		var params = {
			mod: this._module,
			language: this._language,
			parent_id: (item && item.category_id) ? item.category_id[0] : null
		};
		var url = core.js.base.controllers.ActionProvider.get("category_category_add").url + "?" + dojo.objectToQuery(params);
		this._helper.showPane(url);
	},
	
	deleteCategory: function(/*dojo.data.Item*/ item) {
		// summary:
		//		Deletes given category
		var params = {
			category_id: item.category_id[0]
		};
		var url = core.js.base.controllers.ActionProvider.get("category_category_delete").url + "?" + dojo.objectToQuery(params);
		this._helper.showDialog(url, {
			title: this._i18n.category["delete"].title,
			style: "width: 300px",
			refreshOnShow: true
		});
	},
	
	editCategory: function(/*dojo.data.Item|String*/ item) {
		// summary:
		//		Edits given category
		var params = {
			category_id: dojo.isObject(item) ? item.category_id[0] : item
		};
		var url = core.js.base.controllers.ActionProvider.get("category_category_edit").url + "?" + dojo.objectToQuery(params);
		this._helper.showPane(url);
	},
	
	moveCategory: function(/*dojo.data.Item*/ sourceItem, /*dojo.data.Item*/ targetItem) {
		// summary:
		//		Moves given category
		var _this = this;
		var categoryId = sourceItem.category_id[0];
		var parentId   = targetItem.root ? "0" : targetItem.category_id[0];
		this._helper.showStandby();
		dojo.xhrPost({
			url: core.js.base.controllers.ActionProvider.get("category_category_move").url,
			content: {
				category_id: categoryId,
				parent_id: parentId
			},
			handleAs: "json",
			load: function(data) {
				_this._helper.hideStandby();
				dojo.publish("/app/global/notification", [{
					message: _this._i18n.category.move[data.result == "APP_RESULT_OK" ? "success" : "error"],
					type: (data.result == "APP_RESULT_OK") ? "message" : "error"
				}]);
				if (data.result == "APP_RESULT_OK") {
					_this._tree.render();
				}
			}
		});
	},
	
	renameCategory: function(/*dojo.data.Item*/ item) {
		// summary:
		//		Renames given category
		var params = {
			category_id: item.category_id[0]
		};
		var url = core.js.base.controllers.ActionProvider.get("category_category_rename").url + "?" + dojo.objectToQuery(params);
		this._helper.showDialog(url, {
			title: this._i18n.category.rename.title,
			style: "width: 300px",
			refreshOnShow: true
		});
	},
	
	translateCategory: function(/*dojo.data.Item*/ item, /*String*/ language) {
		// summary:
		//		Translates given category to other language
		var params = {
			source_id: item.category_id[0],
			language: language,
			mod: this._module
		};
		var url = core.js.base.controllers.ActionProvider.get("category_category_add").url + "?" + dojo.objectToQuery(params);
		this._helper.showPane(url);
	}
});
