/**
 * NextCMS
 * 
 * @author		Nguyen Huu Phuoc <thenextcms@gmail.com>
 * @copyright	Copyright (c) 2011 - 2012, Nguyen Huu Phuoc
 * @license		http://nextcms.org/license.txt	(GNU GPL version 2 or later)
 * @link		http://nextcms.org
 * @category	modules
 * @package		comment
 * @subpackage	js
 * @since		1.0
 * @version		2012-06-18
 */

dojo.provide("comment.js.controllers.CommentController");

dojo.require("core.js.base.controllers.ActionProvider");
dojo.require("core.js.base.controllers.Subscriber");
dojo.require("core.js.base.Encoder");
dojo.require("core.js.base.I18N");
dojo.require("core.js.base.views.Helper");

dojo.declare("comment.js.controllers.CommentController", null, {
	// _id: String
	_id: null,
	
	// _i18n: Object
	_i18n: null,
	
	// _helper: core.js.base.views.Helper
	_helper: null,
	
	// _toolbar: comment.js.views.CommentToolbar
	_toolbar: null,
	
	// _grid: comment.js.views.CommentGrid
	_grid: null,
	
	// _paginatorContainer: String
	_paginatorContainer: null,
	
	// _defaultCriteria: Object
	_defaultCriteria: {
		status: "not_activated",
		keyword: null,
		page: 1,
		per_page: 20,
		language: null,
		counter: false
	},
	
	// TOPIC_GROUP: [const] String
	TOPIC_GROUP: "/comment/js/controllers/CommentController",
	
	constructor: function(/*String*/ id) {
		this._id = id;
		
		core.js.base.I18N.requireLocalization("comment/languages");
		this._i18n = core.js.base.I18N.getLocalization("comment/languages");
		
		// Create helper instance
		this._helper = new core.js.base.views.Helper(id);
		this._helper.setLanguageData(this._i18n);
		
		core.js.base.controllers.Subscriber.unsubscribe(this.TOPIC_GROUP);
	},
	
	setCommentToolbar: function(/*comment.js.views.CommentToolbar*/ commentToolbar) {
		// summary:
		//		Sets the comment toolbar
		this._toolbar = commentToolbar;
		
		// Refresh handler
		dojo.connect(commentToolbar, "onRefresh", this, "searchComments");
		
		// Search handler
		dojo.connect(commentToolbar, "onSearchComments", this, function(criteria) {
			this.searchComments(criteria);
		});
		dojo.connect(commentToolbar, "onUpdatePageSize", this, function(perPage) {
			this.searchComments({
				page: 1,
				per_page: perPage
			});
		});
		
		// Switch to other language handler
		dojo.connect(commentToolbar, "onSwitchToLanguage", this, function(language) {
			this.searchComments({
				page: 1,
				language: language,
				counter: true		// I need to update the comment counters
			});
		});
		
		return this;	// comment.js.controllers.CommentController
	},
	
	setCommentGrid: function(/*comment.js.views.CommentGrid*/ commentGrid) {
		// summary:
		//		Sets the comments grid
		this._grid = commentGrid;
		
		// View thread handler
		dojo.connect(commentGrid, "onViewThread", this, "viewThread");
		core.js.base.controllers.Subscriber.subscribe(this.TOPIC_GROUP, "/app/comment/comment/view/onClose", this, function() {
			this._helper.removePane();
		});
		
		return this;	// comment.js.controllers.CommentController
	},
	
	setCommentPaginator: function(/*String*/ paginatorContainer) {
		// summary:
		//		Sets the container of paginator
		this._paginatorContainer = paginatorContainer;
		
		core.js.base.controllers.Subscriber.subscribe(this.TOPIC_GROUP, "/app/comment/comment/list/onGotoPage", this, function(page) {
			this.searchComments({
				page: page
			});
		});
		
		return this;	// comment.js.controllers.CommentController
	},
	
	viewThread: function(/*dojo.data.Item*/ item) {
		// summary:
		//		Views comment thread
		var q = {
			comment_id: item.comment_id[0],
			entity_id: item.entity_id[0],
			entity_class: item.entity_class[0],
			entity_module: item.entity_module[0]
		};
		q = core.js.base.Encoder.encode(q);
		var url = core.js.base.controllers.ActionProvider.get("comment_comment_view").url + "?q=" + q;
		this._helper.showPane(url, {
			style: "width: 50%"
		});
	},
	
	initSearchCriteria: function(/*Object*/ criteria) {
		// summary:
		//		Inits the controls with given criteria
		dojo.mixin(this._defaultCriteria, criteria);
		this._toolbar.initSearchCriteria(criteria);
		
		return this;	// comment.js.controllers.CommentController
	},
	
	searchComments: function(/*Object*/ criteria) {
		// summary:
		//		Searches for latest comments
		dojo.mixin(this._defaultCriteria, criteria);
		var q   = core.js.base.Encoder.encode(this._defaultCriteria);
		var url = core.js.base.controllers.ActionProvider.get("comment_comment_list").url;
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
				_this._grid.showComments(data.data);
				
				// Update the paginator
				dijit.byId(_this._paginatorContainer).setContent(data.paginator);
				
				// Update the counters
				if (data.counters) {
					dojo.publish("/app/comment/comment/list/onUpdateCounters", [ data.counters ]);
				}
			}
		});
	},
	
	////////// STATUS FILTER //////////
	
	// _statusListView: comment.js.views.CommentStatusListView
	_statusListView: null,
	
	setStatusListView: function(/*comment.js.views.CommentStatusListView*/ statusListView) {
		// summary:
		//		Sets the status list view
		this._statusListView = statusListView;
		
		// Filter comments by status
		dojo.connect(statusListView, "onStatusSelected", this, function(status) {
			this.searchComments({
				status: status
			});
		});
		
		// Update number of comments after updating comment's status
		core.js.base.controllers.Subscriber.subscribe(this.TOPIC_GROUP, "/app/comment/comment/activate/onSuccess", this, function(data) {
			statusListView.increaseCommentCounter(data.oldStatus, -1);
			statusListView.increaseCommentCounter(data.newStatus, 1);
		});
		
		core.js.base.controllers.Subscriber.subscribe(this.TOPIC_GROUP, "/app/comment/comment/spam/onSuccess", this, function(data) {
			statusListView.increaseCommentCounter(data.oldStatus, -1);
			statusListView.increaseCommentCounter(data.newStatus, 1);
		});
		
		// after switching to other language
		core.js.base.controllers.Subscriber.subscribe(this.TOPIC_GROUP, "/app/comment/comment/list/onUpdateCounters", this, function(counters) {
			for (var status in counters) {
				statusListView.setCommentCounter("total" == status ? null : status, counters[status]);
			}
		});
		
		return this;	// comment.js.controllers.CommentController
	}
});
