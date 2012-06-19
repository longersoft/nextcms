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

dojo.provide("core.js.controllers.PageController");

dojo.require("dojo.io.iframe");
dojo.require("dojox.layout.ContentPane");
dojo.require("dojox.string.sprintf");

dojo.require("core.js.base.controllers.ActionProvider");
dojo.require("core.js.base.controllers.Subscriber");
dojo.require("core.js.base.Encoder");
dojo.require("core.js.base.I18N");
dojo.require("core.js.base.views.Helper");
dojo.require("core.js.controllers.PageMediator");

dojo.declare("core.js.controllers.PageController", null, {
	// _id: String
	_id: null,
	
	// _tabContainerId: String
	_tabContainerId: null,
	
	// _i18n: Object
	_i18n: null,
	
	// _helper: core.js.base.views.Helper
	_helper: null,	
	
	// _pageToolbar: core.js.views.PageToolbar
	_pageToolbar: null,
	
	// _pageGrid: core.js.views.PageGrid
	_pageGrid: null,
	
	// _defaultCriteria: Object
	_defaultCriteria: {
		template: null,
		language: null
	},
	
	// _mediator: core.js.controllers.PageMediator
	_mediator: new core.js.controllers.PageMediator(),
	
	// _layoutPane: dojox.layout.ContentPane
	//		The pane shows the page's layout editor
	_layoutPane: null,
	
	// TOPIC_GROUP: [const] String
	TOPIC_GROUP: "/core/js/controllers/PageController",
	
	constructor: function(/*String*/ id, /*String*/ tabContainerId) {
		this._id = id;
		this._tabContainerId = tabContainerId;
		
		core.js.base.I18N.requireLocalization("core/languages");
		this._i18n = core.js.base.I18N.getLocalization("core/languages");
		
		// Create helper instance
		this._helper = new core.js.base.views.Helper(id);
		this._helper.setLanguageData(this._i18n);
		
		core.js.base.controllers.Subscriber.unsubscribe(this.TOPIC_GROUP);
	},
	
	setPageToolbar: function(/*core.js.views.PageToolbar*/ toolbar) {
		// summary:
		//		Sets the page toolbar
		this._pageToolbar = toolbar;
		this._mediator.setPageToolbar(toolbar);
		
		// Add new page handler
		dojo.connect(toolbar, "onAddPage", this, "addPage");
		core.js.base.controllers.Subscriber.subscribe(this.TOPIC_GROUP, "/app/core/page/add/onCancel", this, function() {
			this._helper.removePane();
		});
		core.js.base.controllers.Subscriber.subscribe(this.TOPIC_GROUP, "/app/core/page/add/onComplete", this, function(data) {
			this._helper.removePane();
			dojo.publish("/app/global/notification", [{
				message: this._i18n.page.add[(data.result == "APP_RESULT_OK") ? "success" : "error"],
				type: (data.result == "APP_RESULT_OK") ? "message" : "error"
			}]);
			
			if (data.result == "APP_RESULT_OK") {
				this.searchPages();
			}
		});
		
		// Refresh handler
		dojo.connect(toolbar, "onRefresh", this, "searchPages");
		
		// Save order handler
		dojo.connect(toolbar, "onSaveOrder", this, "saveOrder");
		
		// Select template handler
		dojo.connect(toolbar, "onSelectTemplate", this, function(template) {
			this.searchPages({
				template: template
			});
		});
		
		// Switch language handler
		dojo.connect(toolbar, "onSwitchToLanguage", this, function(language) {
			this.searchPages({
				language: language
			});
		});
		
		return this;	// core.js.controllers.PageController
	},
	
	setPageGrid: function(/*core.js.views.PageGrid*/ grid) {
		// summary:
		//		Sets the page grid
		this._pageGrid = grid;
		this._mediator.setPageGrid(grid);
		
		// Edit page handler
		dojo.connect(grid, "onEditPage", this, "editPage");
		core.js.base.controllers.Subscriber.subscribe(this.TOPIC_GROUP, "/app/core/page/edit/onCancel", this, function() {
			this._helper.removePane();
		});
		core.js.base.controllers.Subscriber.subscribe(this.TOPIC_GROUP, "/app/core/page/edit/onStart", this, function() {
			this._helper.showStandby();
		});
		core.js.base.controllers.Subscriber.subscribe(this.TOPIC_GROUP, "/app/core/page/edit/onComplete", this, function(data) {
			this._helper.hideStandby();
			this._helper.removePane();
			dojo.publish("/app/global/notification", [{
				message: this._i18n.page.edit[(data.result == "APP_RESULT_OK") ? "success" : "error"],
				type: (data.result == "APP_RESULT_OK") ? "message" : "error"
			}]);
			
			if (data.result == "APP_RESULT_OK") {
				this.searchPages();
			}
		});
		
		// Delete page handler
		dojo.connect(grid, "onDeletePage", this, "deletePage");
		core.js.base.controllers.Subscriber.subscribe(this.TOPIC_GROUP, "/app/core/page/delete/onCancel", this, function() {
			this._helper.closeDialog();
		});
		core.js.base.controllers.Subscriber.subscribe(this.TOPIC_GROUP, "/app/core/page/delete/onComplete", this, function(data) {
			this._helper.closeDialog();
			dojo.publish("/app/global/notification", [{
				message: this._i18n.page["delete"][(data.result == "APP_RESULT_OK") ? "success" : "error"],
				type: (data.result == "APP_RESULT_OK") ? "message" : "error"
			}]);
			
			if (data.result == "APP_RESULT_OK") {
				this.searchPages();
			}
		});
		
		// Layout page handler
		dojo.connect(grid, "onLayoutPage", this, "layoutPage");
		core.js.base.controllers.Subscriber.subscribe(this.TOPIC_GROUP, "/app/core/page/layout/onComplete", this, function(data) {
			dojo.publish("/app/global/notification", [{
				message: this._i18n.page.layout[(data.result == "APP_RESULT_OK") ? "success" : "error"],
				type: (data.result == "APP_RESULT_OK") ? "message" : "error"
			}]);
		});
		core.js.base.controllers.Subscriber.subscribe(this.TOPIC_GROUP, "/app/core/page/layout/onCancel", this, function(pageId) {
			var tabContainer = dijit.byId(this._tabContainerId);
			if (this._layoutPane) {
				tabContainer.closeChild(this._layoutPane);
				this._layoutPane = null;
			}
		});
		
		// Paste layout handler
		dojo.connect(grid, "onPasteLayout", this, "pasteLayout");
		
		// Import layout handler
		dojo.connect(grid, "onImportLayout", this, "importLayout");
		core.js.base.controllers.Subscriber.subscribe(this.TOPIC_GROUP, "/app/core/page/import/onCancel", this, function() {
			this._helper.closeDialog();
		});
		core.js.base.controllers.Subscriber.subscribe(this.TOPIC_GROUP, "/app/core/page/import/onSuccess", this, function(data) {
			this._helper.closeDialog();
			dojo.publish("/app/global/notification", [{ message: this._i18n.page.import.success }]);
			this.searchPages();
		});
		
		// Export layout handler
		dojo.connect(grid, "onExportLayout", this, "exportLayout");
		
		// Translate page handler
		dojo.connect(grid, "onTranslatePage", this, "translatePage");
		
		// Set cache lifetime handler
		dojo.connect(grid, "onSetCacheLifetime", this, "setCacheLifetime");
		
		// Remove cache handler
		dojo.connect(grid, "onRemoveCache", this, "removeCache");
		
		return this;	// core.js.controllers.PageController
	},
	
	addPage: function() {
		// summary:
		//		Adds new page
		var params = {
			template: this._defaultCriteria.template,
			language: this._defaultCriteria.language
		};
		var url = core.js.base.controllers.ActionProvider.get("core_page_add").url + "?" + dojo.objectToQuery(params);
		this._helper.showPane(url, {
			style: "width: 50%"
		});
	},
	
	deletePage: function(/*dojo.data.Item*/ item) {
		// summary:
		//		Deletes given page
		var params = {
			page_id: item.page_id[0]
		};
		var url = core.js.base.controllers.ActionProvider.get("core_page_delete").url + "?" + dojo.objectToQuery(params);
		this._helper.showDialog(url, {
			title: this._i18n.page["delete"].title,
			style: "width: 250px",
			refreshOnShow: true
		});
	},
	
	editPage: function(/*dojo.data.Item|String*/ item) {
		// summary:
		//		Edits given page
		var params = {
			page_id: dojo.isObject(item) ? item.page_id[0] : item
		};
		var url = core.js.base.controllers.ActionProvider.get("core_page_edit").url + "?" + dojo.objectToQuery(params);
		this._helper.showPane(url, {
			style: "width: 50%"
		});
	},
	
	exportLayout: function(/*dojo.data.Item*/ item) {
		// summary:
		//		Exports layout of given page
		dojo.io.iframe.send({
			url: core.js.base.controllers.ActionProvider.get("core_page_export").url,
			method: "GET",
			content: {
				page_id: item.page_id[0]
			}
		});
	},
	
	importLayout: function(/*dojo.data.Item*/ item) {
		// summary:
		//		Imports layout of given page
		var params = {
			page_id: item.page_id[0]
		};
		var url = core.js.base.controllers.ActionProvider.get("core_page_import").url + "?" + dojo.objectToQuery(params);
		this._helper.showDialog(url, {
			title: this._i18n.page.import.title,
			style: "width: 250px",
			refreshOnShow: true
		});
	},
	
	initSearchCriteria: function(/*Object*/ criteria) {
		// summary:
		//		Inits with given criteria
		dojo.mixin(this._defaultCriteria, criteria);
		this._pageToolbar.initSearchCriteria(this._defaultCriteria);
		return this;	// core.js.controllers.PageController
	},
	
	layoutPage: function(/*dojo.data.Item*/ item) {
		// summary:
		//		Layouts given page item
		var params = {
			page_id: item.page_id[0]
		};
		var url = core.js.base.controllers.ActionProvider.get("core_page_layout").url + "?" + dojo.objectToQuery(params);
		
		// Get the tab container
		var tabContainer = dijit.byId(this._tabContainerId);
		
		if (this._layoutPane && this._layoutPane.get("appPageId") == params.page_id) {
			tabContainer.selectChild(this._layoutPane);
		} else {
			// Close the layout pane
			dojo.publish("/app/core/page/layout/onCancel", [ params.page_id ]);
			
			// Create new one
			this._layoutPane = new dojox.layout.ContentPane({
				loadingMessage: "<div class='appCenter'><div><span class='dijitContentPaneLoading'>" + this._i18n.global._share.loadingAction + "</span></div></div>",
				appPageId: params.page_id
			});
			this._layoutPane.set("href", url);
			this._layoutPane.set("title", dojox.string.sprintf(this._i18n.page.layout.title, item.name[0]));
			dojo.connect(this._layoutPane, "onDownloadEnd", this, function() {
				dojo.publish("/app/global/onLoadComplete", [ this._layoutPane.get("href") ]);
			});
			
			// Add new tab for updating the page's layout and activate the tab
			tabContainer.addChild(this._layoutPane);
			tabContainer.selectChild(this._layoutPane);
		}
	},
	
	pasteLayout: function(/*dojo.data.Item*/ item, /*String*/ layoutData) {
		// summary:
		//		Pastes layout data
		// layoutData:
		//		The layout data which will be set to the selected page
		var _this = this;
		this._helper.showStandby();
		var pageId = item.page_id[0];
		dojo.xhrPost({
			url: core.js.base.controllers.ActionProvider.get("core_page_layout").url,
			content: {
				page_id: pageId,
				layout: layoutData,
				format: "json"
			},
			handleAs: "json",
			load: function(data) {
				_this._helper.hideStandby();
				dojo.publish("/app/global/notification", [{
					message: _this._i18n.page.layout[(data.result == "APP_RESULT_OK") ? "success" : "error"],
					type: (data.result == "APP_RESULT_OK") ? "message" : "error"
				}]);
				
				dojo.publish("/app/core/page/layout/onPasteSuccess", [{
					page_id: pageId,
					layout: dojo.fromJson(layoutData)
				}]);
			}
		});
	},
	
	removeCache: function(/*dojo.data.Item*/ item) {
		// summary:
		//		Removes cache of selected page
		var _this = this;
		dojo.xhrPost({
			url: core.js.base.controllers.ActionProvider.get("core_cache_remove").url,
			content: {
				page_id: item.page_id[0]
			},
			handleAs: "json",
			load: function(data) {
				dojo.publish("/app/global/notification", [{
					message: _this._i18n.cache.remove[(data.result == "APP_RESULT_OK") ? "success" : "error"],
					type: (data.result == "APP_RESULT_OK") ? "message" : "error"
				}]);
			}
		});
	},
	
	saveOrder: function() {
		// summary:
		//		Saves the order of pages
		var grid = this._pageGrid.getGrid();
		// DOJO LESSON: How to get the number of rows of a data grid
		var rowCount = grid.get("rowCount");
		var pages = [], item;
		for (var i = 0; i < rowCount; i++) {
			item = grid.getItem(i);
			pages.push(item.page_id[0] + "");
		}
		
		var _this = this;
		this._helper.showStandby();
		dojo.xhrPost({
			url: core.js.base.controllers.ActionProvider.get("core_page_order").url,
			content: {
				pages: pages.join(",")
			},
			handleAs: "json",
			load: function(data) {
				_this._helper.hideStandby();
				dojo.publish("/app/global/notification", [{
					message: _this._i18n.page.order[(data.result == "APP_RESULT_OK") ? "success" : "error"],
					type: (data.result == "APP_RESULT_OK") ? "message" : "error"
				}]);
				
				if (data.result == "APP_RESULT_OK") {
					_this.searchPages();
				}
			}
		});
	},
	
	searchPages: function(/*Object*/ criteria) {
		// summary:
		//		Searches for pages
		dojo.mixin(this._defaultCriteria, criteria);
		
		var q   = core.js.base.Encoder.encode(this._defaultCriteria);
		var url = core.js.base.controllers.ActionProvider.get("core_page_list").url;
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
				_this._pageGrid.showPages(data.pages);
			}
		});
		
		return this;	// core.js.controllers.PageController
	},
	
	setCacheLifetime: function(/*dojo.data.Item*/ item, /*Integer*/ numSeconds) {
		// summary:
		//		Sets page cache lifetime
		var _this = this;
		dojo.xhrPost({
			url: core.js.base.controllers.ActionProvider.get("core_cache_page").url,
			content: {
				page_id: item.page_id[0],
				lifetime: numSeconds
			},
			handleAs: "json",
			load: function(data) {
				dojo.publish("/app/global/notification", [{
					message: _this._i18n.cache.page[(data.result == "APP_RESULT_OK") ? "success" : "error"],
					type: (data.result == "APP_RESULT_OK") ? "message" : "error"
				}]);
			}
		});
	},
	
	translatePage: function(/*dojo.data.Item*/ item, /*String*/ language) {
		// summary:
		//		Translates given menu to other language
		var params = {
			source_id: item.page_id[0],
			language: language
		};
		var url = core.js.base.controllers.ActionProvider.get("core_page_add").url + "?" + dojo.objectToQuery(params);
		this._helper.showPane(url, {
			style: "width: 50%"
		});
	}
});
