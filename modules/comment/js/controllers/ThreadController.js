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

dojo.provide("comment.js.controllers.ThreadController");

dojo.require("comment.js.controllers.ThreadMediator");
dojo.require("core.js.base.controllers.Subscriber");
dojo.require("core.js.base.I18N");

dojo.declare("comment.js.controllers.ThreadController", null, {
	// _id: String
	_id: null,
	
	// _i18n: Object
	_i18n: null,
	
	// _helper: core.js.base.views.Helper
	_helper: null,
	
	// _toolbar: comment.js.views.ThreadToolbar
	_toolbar: null,
	
	// _commentListView: comment.js.views.CommentListView
	_commentListView: null,
	
	// _commentContextMenu: comment.js.views.CommentContextMenu
	_commentContextMenu: null,
	
	// _mediator: comment.js.controllers.ThreadMediator
	_mediator: new comment.js.controllers.ThreadMediator(),
	
	// _defaultCriteria: Object
	_defaultCriteria: {
		keyword: null,
		page: 1
	},
	
	// TOPIC_GROUP: [const] String
	TOPIC_GROUP: "/comment/js/controllers/ThreadController",
	
	constructor: function(/*String*/ id) {
		this._id = id;
		
		core.js.base.I18N.requireLocalization("comment/languages");
		this._i18n = core.js.base.I18N.getLocalization("comment/languages");
		
		// Create helper instance
		this._helper = new core.js.base.views.Helper(id);
		this._helper.setLanguageData(this._i18n);
		
		core.js.base.controllers.Subscriber.unsubscribe(this.TOPIC_GROUP);
	},
	
	setThreadToolbar: function(/*comment.js.views.ThreadToolbar*/ toolbar) {
		// summary:
		//		Sets the thread toolbar
		this._toolbar = toolbar;
		
		// Close handler
		dojo.connect(toolbar, "onClose", this, function() {
			dojo.publish("/app/comment/comment/view/onClose");
		});
		
		// Refresh handler
		dojo.connect(toolbar, "onRefresh", this, "searchComments");
		
		// Search handler
		dojo.connect(toolbar, "onSearchComments", this, function(keyword) {
			this.searchComments({
				keyword: keyword,
				page: 1
			});
		});
		
		return this;	// comment.js.controllers.ThreadController
	},
	
	setCommentListView: function(/*comment.js.views.CommentListView*/ commentListView) {
		// summary:
		//		Sets the comments list view
		this._commentListView = commentListView;
		
		// Show the context menu
		dojo.connect(commentListView, "onMouseDown", this, function(commentItemView) {
			if (this._commentContextMenu) {
				this._commentContextMenu.show(commentItemView);
			}
		});
		
		return this;	// comment.js.controllers.ThreadController
	},
	
	setCommentContextMenu: function(/*comment.js.views.CommentContextMenu*/ commentContextMenu) {
		// summary:
		//		Sets the comment's context menu
		this._commentContextMenu = commentContextMenu;
		this._mediator.setCommentContextMenu(commentContextMenu);
		
		// Activate handler
		dojo.connect(commentContextMenu, "onActivateComment", this, "activateComment");
		
		// Edit handler
		dojo.connect(commentContextMenu, "onEditComment", this, "editComment");
		core.js.base.controllers.Subscriber.subscribe(this.TOPIC_GROUP, "/app/comment/comment/edit/onCancel", this, function() {
			this._helper.removePane();
		});
		core.js.base.controllers.Subscriber.subscribe(this.TOPIC_GROUP, "/app/comment/comment/edit/onStart", this, function() {
			this._helper.showStandby();
		});
		core.js.base.controllers.Subscriber.subscribe(this.TOPIC_GROUP, "/app/comment/comment/edit/onComplete", this, function(data) {
			this._helper.hideStandby();
			dojo.publish("/app/global/notification", [{
				message: this._i18n.comment.edit[(data.result == "APP_RESULT_OK") ? "success" : "error"],
				type: (data.result == "APP_RESULT_OK") ? "message" : "error"
			}]);
			
			if (data.result == "APP_RESULT_OK") {
				this.searchComments();
			}
		});
		
		// Delete handler
		dojo.connect(commentContextMenu, "onDeleteComment", this, "deleteComment");
		core.js.base.controllers.Subscriber.subscribe(this.TOPIC_GROUP, "/app/comment/comment/delete/onCancel", this, function() {
			this._helper.closeDialog();
		});
		core.js.base.controllers.Subscriber.subscribe(this.TOPIC_GROUP, "/app/comment/comment/delete/onComplete", this, function(data) {
			this._helper.closeDialog();
			dojo.publish("/app/global/notification", [{
				message: this._i18n.comment["delete"][(data.result == "APP_RESULT_OK") ? "success" : "error"],
				type: (data.result == "APP_RESULT_OK") ? "message" : "error"
			}]);
			
			if (data.result == "APP_RESULT_OK") {
				this.searchComments();
			}
		});
		
		// Report spam handler
		dojo.connect(commentContextMenu, "onReportSpam", this, "reportSpam");
		
		// Reply handler
		dojo.connect(commentContextMenu, "onReplyComment", this, "replyComment");
		core.js.base.controllers.Subscriber.subscribe(this.TOPIC_GROUP, "/app/comment/comment/reply/onCancel", this, function() {
			this._helper.removePane();
		});
		core.js.base.controllers.Subscriber.subscribe(this.TOPIC_GROUP, "/app/comment/comment/reply/onComplete", this, function(data) {
			this._helper.removePane();
			dojo.publish("/app/global/notification", [{
				message: this._i18n.comment.reply[(data.result == "APP_RESULT_OK") ? "success" : "error"],
				type: (data.result == "APP_RESULT_OK") ? "message" : "error"
			}]);
			
			if (data.result == "APP_RESULT_OK") {
				this.searchComments();
			}
		});
		
		return this;	// comment.js.controllers.ThreadController
	},
	
	activateComment: function(/*comment.js.views.CommentItemView*/ commentItemView) {
		// summary:
		//		Activates or deactivates comment
		var commentId = commentItemView.getComment().comment_id;
		var status = commentItemView.getComment().status;
		var _this  = this;
		dojo.xhrPost({
			url: core.js.base.controllers.ActionProvider.get("comment_comment_activate").url,
			content: {
				comment_id: commentId
			},
			handleAs: "json",
			load: function(data) {
				var message = (data.result == "APP_RESULT_OK") 
							  ? (status == "activated" ? "deactivateSuccess" : "activateSuccess") 
							  : (status == "activated" ? "deactivateError" : "activateError");
				dojo.publish("/app/global/notification", [{
					message: _this._i18n.comment.activate[message],
					type: (data.result == "APP_RESULT_OK") ? "message" : "error"
				}]);
				
				if (data.result == "APP_RESULT_OK") {
					var newStatus = (status == "activated") ? "not_activated" : "activated";
					commentItemView.setStatus(newStatus);
					
					dojo.publish("/app/comment/comment/activate/onSuccess", [{ oldStatus: status, newStatus: newStatus }]);
				}
			}
		});
	},
	
	deleteComment: function(/*comment.js.views.CommentItemView*/ commentItemView) {
		// summary:
		//		Deletes a comment
		var params = {
			comment_id: commentItemView.getComment().comment_id
		};
		var url = core.js.base.controllers.ActionProvider.get("comment_comment_delete").url + "?" + dojo.objectToQuery(params);
		this._helper.showDialog(url, {
			title: this._i18n.comment["delete"].title,
			style: "width: 250px",
			refreshOnShow: true
		});
	},
	
	editComment: function(/*content.js.views.CommentItemView*/ commentItemView) {
		// summary:
		//		Edits given comment
		var params = {
			comment_id: commentItemView.getComment().comment_id
		};
		var url = core.js.base.controllers.ActionProvider.get("comment_comment_edit").url + "?" + dojo.objectToQuery(params);
		this._helper.showPane(url, {
			region: "bottom",
			style: "height: 50%"
		});
	},
	
	replyComment: function(/*comment.js.views.CommentItemView*/ commentItemView, /*Boolean*/ withQuote) {
		// summary:
		//		Replies to given comment
		var params = {
			comment_id: commentItemView.getComment().comment_id,
			quote: withQuote
		};
		var url = core.js.base.controllers.ActionProvider.get("comment_comment_reply").url + "?" + dojo.objectToQuery(params);
		this._helper.showPane(url, {
			region: "bottom",
			style: "height: 50%"
		});
	},
	
	reportSpam: function(/*content.js.views.CommentItemView*/ commentItemView) {
		// summary:
		//		Reports given comment as a spam
		var commentId = commentItemView.getComment().comment_id;
		var status	  = commentItemView.getComment().status;
		var _this = this;
		dojo.xhrPost({
			url: core.js.base.controllers.ActionProvider.get("comment_comment_spam").url,
			content: {
				comment_id: commentId
			},
			handleAs: "json",
			load: function(data) {
				dojo.publish("/app/global/notification", [{
					message: _this._i18n.comment.spam[(data.result == "APP_RESULT_OK") ? "success" : "error"],
					type: (data.result == "APP_RESULT_OK") ? "message" : "error"
				}]);
				
				if (data.result == "APP_RESULT_OK") {
					commentItemView.setStatus("spam");
					dojo.publish("/app/comment/comment/spam/onSuccess", [{ oldStatus: status, newStatus: "spam" }]);
				}
			}
		});
	},
	
	initSearchCriteria: function(/*Object*/ criteria) {
		// summary:
		//		Inits the controls with given criteria
		dojo.mixin(this._defaultCriteria, criteria);
		return this;	// comment.js.controllers.ThreadController
	},
	
	searchComments: function(/*Object*/ criteria) {
		// summary:
		//		Searches for comments in the thread
		dojo.mixin(this._defaultCriteria, criteria);
		var q   = core.js.base.Encoder.encode(this._defaultCriteria);
		var url = core.js.base.controllers.ActionProvider.get("comment_comment_view").url;
		
		var _this = this;
		this._helper.showStandby();
		dojo.xhrPost({
			url: url,
			content: {
				q: q,
				format: "html"
			},
			load: function(data) {
				_this._helper.hideStandby();
				_this._commentListView.setContent(data);
			}
		});
	}
});
