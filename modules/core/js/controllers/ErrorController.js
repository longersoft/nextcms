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

dojo.provide("core.js.controllers.ErrorController");

dojo.require("core.js.base.controllers.ActionProvider");
dojo.require("core.js.base.controllers.Subscriber");
dojo.require("core.js.base.Encoder");
dojo.require("core.js.base.I18N");
dojo.require("core.js.base.views.Helper");

dojo.declare("core.js.controllers.ErrorController", null, {
	// _id: String
	_id: null,
	
	// _i18n: Object
	_i18n: null,
	
	// _helper: core.js.base.views.Helper
	_helper: null,
	
	// _errorToolbar: core.js.views.ErrorToolbar
	_errorToolbar: null,
	
	// _errorGrid: core.js.views.ErrorGrid
	_errorGrid: null,
	
	// _paginatorContainer: String
	_paginatorContainer: null,
	
	// _defaultCriteria: Object
	_defaultCriteria: {
		page: 1,
		module: null,
		from_date: null,
		to_date: null
	},
	
	// TOPIC_GROUP: [const] String
	TOPIC_GROUP: "/core/js/controllers/ErrorController",
	
	constructor: function(/*String*/ id) {
		this._id = id;
		
		core.js.base.I18N.requireLocalization("core/languages");
		this._i18n = core.js.base.I18N.getLocalization("core/languages");
		
		// Create helper instance
		this._helper = new core.js.base.views.Helper(id);
		this._helper.setLanguageData(this._i18n);
		
		core.js.base.controllers.Subscriber.unsubscribe(this.TOPIC_GROUP);
	},
	
	setErrorToolbar: function(/*core.js.views.ErrorToolbar*/ toolbar) {
		// summary:
		//		Sets the error toolbar
		this._errorToolbar = toolbar;
		
		// Refresh handler
		dojo.connect(toolbar, "onRefresh", this, "searchErrors");
		
		// Module filter handler
		dojo.connect(toolbar, "onSelectModule", this, function(module) {
			this.searchErrors({
				module: module
			});
		});
		
		// Search handler
		dojo.connect(toolbar, "onSearchErrors", this, function(criteria) {
			this.searchErrors(criteria);
		});
		
		return this;	// core.js.controllers.ErrorController
	},
	
	setErrorGrid: function(/*core.js.views.ErrorGrid*/ grid) {
		// summary:
		//		Sets the error grid
		this._errorGrid = grid;
		
		// Delete error handler
		dojo.connect(grid, "onDeleteError", this, "deleteError");
		core.js.base.controllers.Subscriber.subscribe(this.TOPIC_GROUP, "/app/core/error/delete/onCancel", this, function() {
			this._helper.closeDialog();
		});
		core.js.base.controllers.Subscriber.subscribe(this.TOPIC_GROUP, "/app/core/error/delete/onComplete", this, function(data) {
			this._helper.closeDialog();
			dojo.publish("/app/global/notification", [{
				message: this._i18n.error["delete"][(data.result == "APP_RESULT_OK") ? "success" : "error"],
				type: (data.result == "APP_RESULT_OK") ? "message" : "error"
			}]);
			
			if (data.result == "APP_RESULT_OK") {
				this.searchErrors();
			}
		});
		
		// View error handler
		dojo.connect(grid, "onViewError", this, "viewError");
		core.js.base.controllers.Subscriber.subscribe(this.TOPIC_GROUP, "/app/core/error/view/onCancel", this, function() {
			this._helper.removePane();
		});
		
		return this;	// core.js.controllers.ErrorController
	},
	
	setErrorPaginator: function(/*String*/ paginatorContainer) {
		// summary:
		//		Sets the container of paginator
		this._paginatorContainer = paginatorContainer;
		
		core.js.base.controllers.Subscriber.subscribe(this.TOPIC_GROUP, "/app/core/error/list/onGotoPage", this, function(page) {
			this.searchErrors({
				page: page
			});
		});
		
		return this;	// core.js.controllers.ErrorController
	},
	
	deleteError: function(/*dojo.data.Item*/ item) {
		// summary:
		//		Deletes the given error
		var params = {
			error_id: item.error_id[0]
		};
		var url = core.js.base.controllers.ActionProvider.get("core_error_delete").url + "?" + dojo.objectToQuery(params);
		this._helper.showDialog(url, {
			title: this._i18n.error["delete"].title,
			style: "width: 250px",
			refreshOnShow: true
		});
	},
	
	initSearchCriteria: function(/*Object*/ criteria) {
		// summary:
		//		Inits with given criteria
		dojo.mixin(this._defaultCriteria, criteria);
		this._errorToolbar.initSearchCriteria(this._defaultCriteria);
		return this;	// core.js.controllers.ErrorController
	},
	
	searchErrors: function(/*Object*/ criteria) {
		// summary:
		//		Searches for errors
		dojo.mixin(this._defaultCriteria, criteria);
		
		var q   = core.js.base.Encoder.encode(this._defaultCriteria);
		var url = core.js.base.controllers.ActionProvider.get("core_error_list").url;
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
				_this._errorGrid.showErrors(data.errors);
				
				// Update the paginator
				dijit.byId(_this._paginatorContainer).setContent(data.paginator);
			}
		});
		
		return this;	// core.js.controllers.ErrorController
	},
	
	viewError: function(/*dojo.data.Item*/ item) {
		// summary:
		//		Views given error item
		var params = {
			error_id: item.error_id[0]
		};
		var url = core.js.base.controllers.ActionProvider.get("core_error_view").url + "?" + dojo.objectToQuery(params);
		this._helper.showPane(url, {
			style: "width: 50%"
		});
	}
});
