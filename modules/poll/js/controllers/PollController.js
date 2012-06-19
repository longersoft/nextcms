/**
 * NextCMS
 * 
 * @author		Nguyen Huu Phuoc <thenextcms@gmail.com>
 * @copyright	Copyright (c) 2011 - 2012, Nguyen Huu Phuoc
 * @license		http://nextcms.org/license.txt	(GNU GPL version 2 or later)
 * @link		http://nextcms.org
 * @category	modules
 * @package		poll
 * @subpackage	js
 * @since		1.0
 * @version		2012-06-18
 */

dojo.provide("poll.js.controllers.PollController");

dojo.require("core.js.base.controllers.ActionProvider");
dojo.require("core.js.base.controllers.Subscriber");
dojo.require("core.js.base.Encoder");
dojo.require("core.js.base.I18N");
dojo.require("core.js.base.views.Helper");

dojo.declare("poll.js.controllers.PollController", null, {
	// _id: String
	_id: null,
	
	// _i18n: Object
	_i18n: null,
	
	// _helper: core.js.base.views.Helper
	_helper: null,
	
	// _pollToolbar: poll.js.views.PollToolbar
	_pollToolbar: null,
	
	// _pollGrid: poll.js.views.PollGrid
	_pollGrid: null,
	
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
	TOPIC_GROUP: "/poll/js/controllers/PollController",
	
	constructor: function(/*String*/ id) {
		this._id = id;
		
		core.js.base.I18N.requireLocalization("poll/languages");
		this._i18n = core.js.base.I18N.getLocalization("poll/languages");
		
		// Create helper instance
		this._helper = new core.js.base.views.Helper(id);
		this._helper.setLanguageData(this._i18n);
		
		core.js.base.controllers.Subscriber.unsubscribe(this.TOPIC_GROUP);
	},
	
	setPollToolbar: function(/*poll.js.views.PollToolbar*/ pollToolbar) {
		// summary:
		//		Sets the poll toolbar
		this._pollToolbar = pollToolbar;
		
		// Add poll handler
		dojo.connect(pollToolbar, "onAddPoll", this, "addPoll");
		core.js.base.controllers.Subscriber.subscribe(this.TOPIC_GROUP, "/app/poll/poll/add/onCancel", this, function() {
			this._helper.removePane();
		});
		core.js.base.controllers.Subscriber.subscribe(this.TOPIC_GROUP, "/app/poll/poll/add/onStart", this, function() {
			this._helper.showStandby();
		});
		core.js.base.controllers.Subscriber.subscribe(this.TOPIC_GROUP, "/app/poll/poll/add/onComplete", this, function(data) {
			this._helper.hideStandby();
			this._helper.removePane();
			dojo.publish("/app/global/notification", [{
				message: this._i18n.poll.add[(data.result == "APP_RESULT_OK") ? "success" : "error"],
				type: (data.result == "APP_RESULT_OK") ? "message" : "error"
			}]);
			
			if (data.result == "APP_RESULT_OK") {
				this.searchPolls();
			}
		});
		
		// Refresh handler
		dojo.connect(pollToolbar, "onRefresh", this, "searchPolls");
		
		// Search handler
		dojo.connect(pollToolbar, "onSearchPolls", this, function(keyword) {
			this.searchPolls({
				keyword: keyword,
				page: 1
			});
		});
		
		// Switch to other language handler
		dojo.connect(pollToolbar, "onSwitchToLanguage", this, function(language) {
			this.searchPolls({
				language: language,
				page: 1
			});
		});
		
		// Update page size handler
		dojo.connect(pollToolbar, "onUpdatePageSize", this, function(perPage) {
			this.searchPolls({
				page: 1,
				per_page: perPage
			});
		});
		
		return this;	// poll.js.controllers.PollController
	},
	
	setPollGrid: function(/*poll.js.views.PollGrid*/ pollGrid) {
		// summary:
		//		Sets the poll grid
		this._pollGrid = pollGrid;
		
		// Edit poll handler
		dojo.connect(pollGrid, "onEditPoll", this, "editPoll");
		core.js.base.controllers.Subscriber.subscribe(this.TOPIC_GROUP, "/app/poll/poll/edit/onCancel", this, function() {
			this._helper.removePane();
		});
		core.js.base.controllers.Subscriber.subscribe(this.TOPIC_GROUP, "/app/poll/poll/edit/onStart", this, function() {
			this._helper.showStandby();
		});
		core.js.base.controllers.Subscriber.subscribe(this.TOPIC_GROUP, "/app/poll/poll/edit/onComplete", this, function(data) {
			this._helper.hideStandby();
			this._helper.removePane();
			dojo.publish("/app/global/notification", [{
				message: this._i18n.poll.edit[(data.result == "APP_RESULT_OK") ? "success" : "error"],
				type: (data.result == "APP_RESULT_OK") ? "message" : "error"
			}]);
			
			if (data.result == "APP_RESULT_OK") {
				this.searchPolls();
			}
		});
		
		// Delete poll handler
		dojo.connect(pollGrid, "onDeletePoll", this, "deletePoll");
		core.js.base.controllers.Subscriber.subscribe(this.TOPIC_GROUP, "/app/poll/poll/delete/onCancel", this, function() {
			this._helper.closeDialog();
		});
		core.js.base.controllers.Subscriber.subscribe(this.TOPIC_GROUP, "/app/poll/poll/delete/onComplete", this, function(data) {
			this._helper.closeDialog();
			dojo.publish("/app/global/notification", [{
				message: this._i18n.poll["delete"][(data.result == "APP_RESULT_OK") ? "success" : "error"],
				type: (data.result == "APP_RESULT_OK") ? "message" : "error"
			}]);
			
			if (data.result == "APP_RESULT_OK") {
				this.searchPolls();
			}
		});
		
		// Translate poll handler
		dojo.connect(pollGrid, "onTranslatePoll", this, "translatePoll");
		
		return this;	// poll.js.controllers.PollController
	},
	
	setPollPaginator: function(/*String*/ paginatorContainer) {
		// summary:
		//		Sets the container of paginator
		this._paginatorContainer = paginatorContainer;
		
		return this;	// poll.js.controllers.PollController
	},
	
	addPoll: function() {
		// summary:
		//		Adds new poll
		var url = core.js.base.controllers.ActionProvider.get("poll_poll_add").url;
		this._helper.showPane(url, {
			style: "width: 50%"
		});
	},
	
	deletePoll: function(/*dojo.data.Item*/ item) {
		// summary:
		//		Deletes given poll
		var params = {
			poll_id: item.poll_id[0]
		};
		var url = core.js.base.controllers.ActionProvider.get("poll_poll_delete").url + "?" + dojo.objectToQuery(params);
		this._helper.showDialog(url, {
			title: this._i18n.poll["delete"].title,
			style: "width: 250px",
			refreshOnShow: true
		});
	},
	
	editPoll: function(/*dojo.data.Item|String*/ item) {
		// summary:
		//		Edits given poll
		var params = {
			poll_id: dojo.isObject(item) ? item.poll_id[0] : item
		};
		var url = core.js.base.controllers.ActionProvider.get("poll_poll_edit").url + "?" + dojo.objectToQuery(params);
		this._helper.showPane(url, {
			style: "width: 50%"
		});
	},
	
	initSearchCriteria: function(/*Object*/ criteria) {
		// summary:
		//		Inits the controls with given criteria
		dojo.mixin(this._defaultCriteria, criteria);
		this._pollToolbar.initSearchCriteria(criteria);
		
		return this;	// poll.js.controllers.PollController
	},
	
	searchPolls: function(/*Object*/ criteria) {
		// summary:
		//		Searches for polls
		dojo.mixin(this._defaultCriteria, criteria);
		var q   = core.js.base.Encoder.encode(this._defaultCriteria);
		var url = core.js.base.controllers.ActionProvider.get("poll_poll_list").url;
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
				_this._pollGrid.showPolls(data.data);
				
				// Update the paginator
				dijit.byId(_this._paginatorContainer).setContent(data.paginator);
			}
		});
	},
	
	translatePoll: function(/*dojo.data.Item*/ item, /*String*/ language) {
		// summary:
		//		Translates given poll to other language
		var params = {
			source_id: item.poll_id[0],
			language: language
		};
		var url = core.js.base.controllers.ActionProvider.get("poll_poll_add").url + "?" + dojo.objectToQuery(params);
		this._helper.showPane(url, {
			style: "width: 50%"
		});
	}
});
