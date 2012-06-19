/**
 * NextCMS
 * 
 * @author		Nguyen Huu Phuoc <thenextcms@gmail.com>
 * @copyright	Copyright (c) 2011 - 2012, Nguyen Huu Phuoc
 * @license		http://nextcms.org/license.txt	(GNU GPL version 2 or later)
 * @link		http://nextcms.org
 * @category	modules
 * @package		core
 * @subpackage	js
 * @since		1.0
 * @version		2012-05-16
 */

dojo.provide("core.js.controllers.LanguageController");

dojo.require("core.js.base.controllers.ActionProvider");
dojo.require("core.js.base.controllers.Subscriber");
dojo.require("core.js.base.Encoder");
dojo.require("core.js.base.I18N");
dojo.require("core.js.base.views.Helper");

dojo.require("core.js.controllers.LanguageMediator");

dojo.declare("core.js.controllers.LanguageController", null, {
	// _id: String
	_id: null,
	
	// _i18n: Object
	_i18n: null,
	
	// _helper: core.js.base.views.Helper
	_helper: null,
	
	// _toolbar: core.js.views.LanguageToolbar
	_toolbar: null,
	
	// _grid: core.js.views.LanguageGrid
	_grid: null,
	
	// _mediator: core.js.controllers.LanguageMediator
	_mediator: new core.js.controllers.LanguageMediator(),
	
	// TOPIC_GROUP: [const] String
	TOPIC_GROUP: "/core/js/controllers/LanguageController",
	
	constructor: function(/*String*/ id) {
		this._id = id;
		
		core.js.base.I18N.requireLocalization("core/languages");
		this._i18n = core.js.base.I18N.getLocalization("core/languages");
		
		// Create helper instance
		this._helper = new core.js.base.views.Helper(id);
		this._helper.setLanguageData(this._i18n);
		
		core.js.base.controllers.Subscriber.unsubscribe(this.TOPIC_GROUP);
	},
	
	setLanguageToolbar: function(/*core.js.views.LanguageToolbar*/ toolbar) {
		// summary:
		//		Sets the translator toolbar
		this._toolbar = toolbar;
		this._mediator.setLanguageToolbar(toolbar);
		
		// Load the language file handler
		dojo.connect(toolbar, "onLoadLanguage", this, function(file) {
			if (this._grid) {
				this._grid.showLanguageFile(file);
			}
		});
		
		return this;	// core.js.controllers.LanguageController
	},
	
	setLanguageGrid: function(/*core.js.views.LanguageGrid*/ grid) {
		// summary:
		//		Sets the translator grid
		this._grid = grid;
		this._mediator.setLanguageGrid(grid);
		
		// Add new item handler
		dojo.connect(grid, "onAddItem", this, "addItem");
		core.js.base.controllers.Subscriber.subscribe(this.TOPIC_GROUP, "/app/core/language/add/onCancel", this._helper, "closeDialog");
		core.js.base.controllers.Subscriber.subscribe(this.TOPIC_GROUP, "/app/core/language/add/onComplete", this, function(data) {
			this._helper.closeDialog();
			dojo.publish("/app/global/notification", [{
				message: this._i18n.language.add[(data.result == "APP_RESULT_OK") ? "success" : "error"],
				type: (data.result == "APP_RESULT_OK") ? "message" : "error"
			}]);
			
			if (data.result == "APP_RESULT_OK") {
				this._grid.addItem({
					path: data.path + "." + data.key,
					key: data.key,
					text: data.text
				});
			}
		});
		
		// Delete item handler
		dojo.connect(grid, "onDeleteItem", this, "deleteItem");
		core.js.base.controllers.Subscriber.subscribe(this.TOPIC_GROUP, "/app/core/language/delete/onCancel", this._helper, "closeDialog");
		core.js.base.controllers.Subscriber.subscribe(this.TOPIC_GROUP, "/app/core/language/delete/onComplete", this, function(data) {
			this._helper.closeDialog();
			dojo.publish("/app/global/notification", [{
				message: this._i18n.language["delete"][(data.result == "APP_RESULT_OK") ? "success" : "error"],
				type: (data.result == "APP_RESULT_OK") ? "message" : "error"
			}]);
			
			if (data.result == "APP_RESULT_OK") {
				this._grid.deleteSelectedItem();
			}
		});
		
		// Edit item handler
		dojo.connect(grid, "onEditItem", this, "editItem");
		core.js.base.controllers.Subscriber.subscribe(this.TOPIC_GROUP, "/app/core/language/edit/onCancel", this._helper, "closeDialog");
		core.js.base.controllers.Subscriber.subscribe(this.TOPIC_GROUP, "/app/core/language/edit/onComplete", this, function(data) {
			this._helper.closeDialog();
			dojo.publish("/app/global/notification", [{
				message: this._i18n.language.edit[(data.result == "APP_RESULT_OK") ? "success" : "error"],
				type: (data.result == "APP_RESULT_OK") ? "message" : "error"
			}]);
			
			if (data.result == "APP_RESULT_OK") {
				this._grid.updateSelectedItem(data.text);
			}
		});
		
		return this;	// core.js.controllers.LanguageController
	},
	
	addItem: function(/*dojo.data.Item*/ parentItem) {
		// summary:
		//		Adds new language item
		var params = {
			file: this._grid.getLanguageFile(),
			path: parentItem.path[0]
		};
		var url = core.js.base.controllers.ActionProvider.get("core_language_add").url + "?" + dojo.objectToQuery(params);
		this._helper.showDialog(url, {
			title: this._i18n.language.add.title,
			style: "width: 450px",
			refreshOnShow: true
		});
	},
	
	deleteItem: function(/*dojo.data.Item*/ item) {
		// summary:
		//		Deletes a given language item
		var params = {
			file: this._grid.getLanguageFile(),
			path: item.path[0]
		};
		var url = core.js.base.controllers.ActionProvider.get("core_language_delete").url + "?" + dojo.objectToQuery(params);
		this._helper.showDialog(url, {
			title: this._i18n.language["delete"].title,
			style: "width: 250px",
			refreshOnShow: true
		});
	},
	
	editItem: function(/*dojo.data.Item*/ item) {
		// summary:
		//		Edits a language item
		var params = {
			file: this._grid.getLanguageFile(),
			path: item.path[0],
			text: item.text[0]
		};
		var url = core.js.base.controllers.ActionProvider.get("core_language_edit").url + "?" + dojo.objectToQuery(params);
		this._helper.showDialog(url, {
			title: this._i18n.language.edit.title,
			style: "width: 450px",
			refreshOnShow: true
		});
	}
});
