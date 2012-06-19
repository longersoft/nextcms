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

dojo.provide("core.js.controllers.AccessLogController");

dojo.require("core.js.base.controllers.ActionProvider");
dojo.require("core.js.base.controllers.Subscriber");
dojo.require("core.js.base.Encoder");
dojo.require("core.js.base.I18N");
dojo.require("core.js.base.views.Helper");
dojo.require("core.js.controllers.AccessLogMediator");

dojo.declare("core.js.controllers.AccessLogController", null, {
	// _id: String
	_id: null,
	
	// _i18n: Object
	_i18n: null,
	
	// _helper: core.js.base.views.Helper
	_helper: null,
	
	// _accessLogToolbar: core.js.views.AccessLogToolbar
	_accessLogToolbar: null,
	
	// _accessLogGrid: core.js.views.AccessLogGrid
	_accessLogGrid: null,
	
	// _paginatorContainer: String
	_paginatorContainer: null,
	
	// _defaultCriteria: Object
	_defaultCriteria: {
		page: 1,
		module: null,
		from_date: null,
		to_date: null,
		ip: null
	},
	
	// _mediator: core.js.controllers.AccessLogMediator
	_mediator: new core.js.controllers.AccessLogMediator(),
	
	// TOPIC_GROUP: [const] String
	TOPIC_GROUP: "/core/js/controllers/AccessLogController",
	
	constructor: function(/*String*/ id) {
		this._id = id;
		
		core.js.base.I18N.requireLocalization("core/languages");
		this._i18n = core.js.base.I18N.getLocalization("core/languages");
		
		// Create helper instance
		this._helper = new core.js.base.views.Helper(id);
		this._helper.setLanguageData(this._i18n);
		
		core.js.base.controllers.Subscriber.unsubscribe(this.TOPIC_GROUP);
	},
	
	setAccessLogToolbar: function(/*core.js.views.AccessLogToolbar*/ toolbar) {
		// summary:
		//		Sets the access log toolbar
		this._accessLogToolbar = toolbar;
		this._mediator.setAccessLogToolbar(toolbar);
		
		// Refresh handler
		dojo.connect(toolbar, "onRefresh", this, "searchAccessLogs");
		
		// Module filter handler
		dojo.connect(toolbar, "onSelectModule", this, function(module) {
			this.searchAccessLogs({
				module: module
			});
		});
		
		// Search handler
		dojo.connect(toolbar, "onSearchAccessLogs", this, function(criteria) {
			this.searchAccessLogs(criteria);
		});
		
		return this;	// core.js.controllers.AccessLogController
	},
	
	setAccessLogGrid: function(/*core.js.views.AccessLogGrid*/ grid) {
		// summary:
		//		Sets the access logs grid
		this._accessLogGrid = grid;
		this._mediator.setAccessLogGrid(grid);
		
		// Delete access log handler
		dojo.connect(grid, "onDeleteAccessLog", this, "deleteAccessLog");
		core.js.base.controllers.Subscriber.subscribe(this.TOPIC_GROUP, "/app/core/accesslog/delete/onCancel", this, function() {
			this._helper.closeDialog();
		});
		core.js.base.controllers.Subscriber.subscribe(this.TOPIC_GROUP, "/app/core/accesslog/delete/onComplete", this, function(data) {
			this._helper.closeDialog();
			dojo.publish("/app/global/notification", [{
				message: this._i18n.accesslog["delete"][(data.result == "APP_RESULT_OK") ? "success" : "error"],
				type: (data.result == "APP_RESULT_OK") ? "message" : "error"
			}]);
			
			if (data.result == "APP_RESULT_OK") {
				this.searchAccessLogs();
			}
		});
		
		// View access log handler
		dojo.connect(grid, "onViewAccessLog", this, "viewAccessLog");
		core.js.base.controllers.Subscriber.subscribe(this.TOPIC_GROUP, "/app/core/accesslog/view/onCancel", this, function() {
			this._helper.removePane();
		});
		
		// Filter by IP handler
		dojo.connect(grid, "onFilterByIp", this, function(ipAddress) {
			this.searchAccessLogs({
				page: 1,
				ip: ipAddress
			});
		});
		
		return this;	// core.js.controllers.AccessLogController
	},
	
	setAccessLogPaginator: function(/*String*/ paginatorContainer) {
		// summary:
		//		Sets the container of paginator
		this._paginatorContainer = paginatorContainer;
		
		core.js.base.controllers.Subscriber.subscribe(this.TOPIC_GROUP, "/app/core/accesslog/list/onGotoPage", this, function(page) {
			this.searchAccessLogs({
				page: page
			});
		});
		
		return this;	// menu.js.controllers.MenuController
	},	
	
	deleteAccessLog: function(/*dojo.data.Item*/ item) {
		// summary:
		//		Deletes the given access log
		var params = {
			log_id: item.log_id[0]
		};
		var url = core.js.base.controllers.ActionProvider.get("core_accesslog_delete").url + "?" + dojo.objectToQuery(params);
		this._helper.showDialog(url, {
			title: this._i18n.accesslog["delete"].title,
			style: "width: 250px",
			refreshOnShow: true
		});
	},
	
	initSearchCriteria: function(/*Object*/ criteria) {
		// summary:
		//		Inits with given criteria
		dojo.mixin(this._defaultCriteria, criteria);
		this._accessLogToolbar.initSearchCriteria(this._defaultCriteria);
		return this;	// core.js.controllers.AccessLogController
	},
	
	searchAccessLogs: function(/*Object*/ criteria) {
		// summary:
		//		Searches for access logs
		dojo.mixin(this._defaultCriteria, criteria);
		
		var q   = core.js.base.Encoder.encode(this._defaultCriteria);
		var url = core.js.base.controllers.ActionProvider.get("core_accesslog_list").url;
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
				_this._accessLogGrid.showAccessLogs(data.logs);
				
				// Update the paginator
				dijit.byId(_this._paginatorContainer).setContent(data.paginator);
			}
		});
		
		return this;	// core.js.controllers.AccessLogController
	},
	
	viewAccessLog: function(/*dojo.data.Item*/ item) {
		// summary:
		//		Views given access log item
		var params = {
			log_id: item.log_id[0]
		};
		var url = core.js.base.controllers.ActionProvider.get("core_accesslog_view").url + "?" + dojo.objectToQuery(params);
		this._helper.showPane(url, {
			style: "width: 50%"
		});
	}
});
