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
 * @version		2012-06-18
 */

dojo.provide("menu.js.controllers.MenuController");

dojo.require("core.js.base.controllers.ActionProvider");
dojo.require("core.js.base.controllers.Subscriber");
dojo.require("core.js.base.Encoder");
dojo.require("core.js.base.I18N");
dojo.require("core.js.base.views.Helper");

dojo.declare("menu.js.controllers.MenuController", null, {
	// _id: String
	_id: null,
	
	// _i18n: Object
	_i18n: null,
	
	// _helper: core.js.base.views.Helper
	_helper: null,
	
	// _menuToolbar: menu.js.views.MenuToolbar
	_menuToolbar: null,
	
	// _menuGrid: menu.js.views.MenuGrid
	_menuGrid: null,
	
	// _paginatorContainer: String
	_paginatorContainer: null,
	
	// _defaultCriteria: Object
	_defaultCriteria: {
		keyword: null,
		page: 1,
		per_page: 20,
		language: null
	},
	
	// TOPIC_GROUP: [const] String
	TOPIC_GROUP: "/menu/js/controllers/MenuController",
	
	constructor: function(/*String*/ id) {
		this._id = id;
		
		core.js.base.I18N.requireLocalization("menu/languages");
		this._i18n = core.js.base.I18N.getLocalization("menu/languages");
		
		// Create helper instance
		this._helper = new core.js.base.views.Helper(id);
		this._helper.setLanguageData(this._i18n);
		
		core.js.base.controllers.Subscriber.unsubscribe(this.TOPIC_GROUP);
	},
	
	setMenuToolbar: function(/*menu.js.views.MenuToolbar*/ menuToolbar) {
		// summary:
		//		Sets the menu toolbar
		this._menuToolbar = menuToolbar;
		
		// Add menu handler
		dojo.connect(menuToolbar, "onAddMenu", this, "addMenu");
		core.js.base.controllers.Subscriber.subscribe(this.TOPIC_GROUP, "/app/menu/menu/add/onCancel", this, function() {
			this._helper.removePane();
		});
		core.js.base.controllers.Subscriber.subscribe(this.TOPIC_GROUP, "/app/menu/menu/add/onStart", this, function() {
			this._helper.showStandby();
		});
		core.js.base.controllers.Subscriber.subscribe(this.TOPIC_GROUP, "/app/menu/menu/add/onComplete", this, function(data) {
			this._helper.hideStandby();
			this._helper.removePane();
			dojo.publish("/app/global/notification", [{
				message: this._i18n.menu.add[(data.result == "APP_RESULT_OK") ? "success" : "error"],
				type: (data.result == "APP_RESULT_OK") ? "message" : "error"
			}]);
			
			if (data.result == "APP_RESULT_OK") {
				this.searchMenus();
			}
		});

		// Refresh handler
		dojo.connect(menuToolbar, "onRefresh", this, "searchMenus");
		
		// Search handler
		dojo.connect(menuToolbar, "onSearchMenus", this, function(keyword) {
			this.searchMenus({
				keyword: keyword,
				page: 1
			});
		});
		dojo.connect(menuToolbar, "onUpdatePageSize", this, function(perPage) {
			this.searchMenus({
				page: 1,
				per_page: perPage
			});
		});
		
		// Switch to other language handler
		dojo.connect(menuToolbar, "onSwitchToLanguage", this, function(language) {
			this.searchMenus({
				language: language,
				page: 1
			});
		});
		
		return this;	// menu.js.controllers.MenuController
	},
	
	setMenuGrid: function(/*menu.js.views.MenuGrid*/ menuGrid) {
		// summary:
		//		Sets the menu grid
		this._menuGrid = menuGrid;
		
		// Edit menu handler
		dojo.connect(menuGrid, "onEditMenu", this, "editMenu");
		core.js.base.controllers.Subscriber.subscribe(this.TOPIC_GROUP, "/app/menu/menu/edit/onCancel", this, function() {
			this._helper.removePane();
		});
		core.js.base.controllers.Subscriber.subscribe(this.TOPIC_GROUP, "/app/menu/menu/edit/onStart", this, function() {
			this._helper.showStandby();
		});
		core.js.base.controllers.Subscriber.subscribe(this.TOPIC_GROUP, "/app/menu/menu/edit/onComplete", this, function(data) {
			this._helper.hideStandby();
			this._helper.removePane();
			dojo.publish("/app/global/notification", [{
				message: this._i18n.menu.edit[(data.result == "APP_RESULT_OK") ? "success" : "error"],
				type: (data.result == "APP_RESULT_OK") ? "message" : "error"
			}]);
			
			if (data.result == "APP_RESULT_OK") {
				this.searchMenus();
			}
		});
		
		// Delete menu handler
		dojo.connect(menuGrid, "onDeleteMenu", this, "deleteMenu");
		core.js.base.controllers.Subscriber.subscribe(this.TOPIC_GROUP, "/app/menu/menu/delete/onCancel", this, function() {
			this._helper.closeDialog();
		});
		core.js.base.controllers.Subscriber.subscribe(this.TOPIC_GROUP, "/app/menu/menu/delete/onComplete", this, function(data) {
			this._helper.closeDialog();
			dojo.publish("/app/global/notification", [{
				message: this._i18n.menu["delete"][(data.result == "APP_RESULT_OK") ? "success" : "error"],
				type: (data.result == "APP_RESULT_OK") ? "message" : "error"
			}]);
			
			if (data.result == "APP_RESULT_OK") {
				this.searchMenus();
			}
		});
		
		// Translate menu handler
		dojo.connect(menuGrid, "onTranslateMenu", this, "translateMenu");
		
		return this;	// menu.js.controllers.MenuController
	},
	
	setMenuPaginator: function(/*String*/ paginatorContainer) {
		// summary:
		//		Sets the container of paginator
		this._paginatorContainer = paginatorContainer;
		
		core.js.base.controllers.Subscriber.subscribe(this.TOPIC_GROUP, "/app/menu/menu/list/onGotoPage", this, function(page) {
			this.searchMenus({
				page: page
			});
		});
		
		return this;	// menu.js.controllers.MenuController
	},	
	
	addMenu: function() {
		// summary:
		//		Adds new menu
		var url = core.js.base.controllers.ActionProvider.get("menu_menu_add").url;
		this._helper.showPane(url, {
			style: "width: 50%"
		});
	},
	
	deleteMenu: function(/*dojo.data.Item*/ item) {
		// summary:
		//		Deletes given menu
		var params = {
			menu_id: item.menu_id[0]
		};
		var url = core.js.base.controllers.ActionProvider.get("menu_menu_delete").url + "?" + dojo.objectToQuery(params);
		this._helper.showDialog(url, {
			title: this._i18n.menu["delete"].title,
			style: "width: 250px",
			refreshOnShow: true
		});
	},
	
	editMenu: function(/*dojo.data.Item|String*/ item) {
		// summary:
		//		Edits given menu
		var params = {
			menu_id: dojo.isObject(item) ? item.menu_id[0] : item
		};
		var url = core.js.base.controllers.ActionProvider.get("menu_menu_edit").url + "?" + dojo.objectToQuery(params);
		this._helper.showPane(url, {
			style: "width: 50%"
		});
	},
	
	initSearchCriteria: function(/*Object*/ criteria) {
		// summary:
		//		Inits the controls with given criteria
		dojo.mixin(this._defaultCriteria, criteria);
		this._menuToolbar.initSearchCriteria(criteria);
		
		return this;	// menu.js.controllers.MenuController
	},
	
	searchMenus: function(/*Object*/ criteria) {
		// summary:
		//		Searches for menus
		dojo.mixin(this._defaultCriteria, criteria);
		var q   = core.js.base.Encoder.encode(this._defaultCriteria);
		var url = core.js.base.controllers.ActionProvider.get("menu_menu_list").url;
		dojo.hash("u=" + url + "/?q=" + q);
		
		var _this = this;
		this._helper.showStandby();
		dojo.xhrPost({
			url: url,
			content: {
				q: q,
				format: "json"
			},
			handleAs: "json",
			load: function(data) {
				_this._helper.hideStandby();
				_this._menuGrid.showMenus(data.data);
				
				// Update the paginator
				dijit.byId(_this._paginatorContainer).setContent(data.paginator);
			}
		});
	},
	
	translateMenu: function(/*dojo.data.Item*/ item, /*String*/ language) {
		// summary:
		//		Translates given menu to other language
		var params = {
			source_id: item.menu_id[0],
			language: language
		};
		var url = core.js.base.controllers.ActionProvider.get("menu_menu_add").url + "?" + dojo.objectToQuery(params);
		this._helper.showPane(url, {
			style: "width: 50%"
		});
	}
});
