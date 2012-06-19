/**
 * NextCMS
 * 
 * @author		Nguyen Huu Phuoc <thenextcms@gmail.com>
 * @copyright	Copyright (c) 2011 - 2012, Nguyen Huu Phuoc
 * @license		http://nextcms.org/license.txt	(GNU GPL version 2 or later)
 * @link		http://nextcms.org
 * @category	modules
 * @package		message
 * @subpackage	js
 * @since		1.0
 * @version		2012-06-18
 */

dojo.provide("message.js.controllers.ThreadController");

dojo.require("dojox.string.sprintf");

dojo.require("core.js.base.controllers.Subscriber");
dojo.require("core.js.base.I18N");
dojo.require("message.js.controllers.ThreadMediator");

dojo.declare("message.js.controllers.ThreadController", null, {
	// _id: String
	_id: null,
	
	// _i18n: Object
	_i18n: null,
	
	// _helper: core.js.base.views.Helper
	_helper: null,
	
	// _toolbar: message.js.views.ThreadToolbar
	_toolbar: null,
	
	// _messageListView: message.js.views.MessageListView
	_messageListView: null,
	
	// _messageContextMenu: message.js.views.MessageContextMenu
	_messageContextMenu: null,
	
	// _mediator: message.js.controllers.ThreadMediator
	_mediator: new message.js.controllers.ThreadMediator(),
	
	// _defaultCriteria: Object
	_defaultCriteria: {
		keyword: null,
		page: 1
	},
	
	// TOPIC_GROUP: [const] String
	TOPIC_GROUP: "/message/js/controllers/ThreadController",
	
	constructor: function(/*String*/ id) {
		this._id = id;
		
		core.js.base.I18N.requireLocalization("message/languages");
		this._i18n = core.js.base.I18N.getLocalization("message/languages");
		
		// Create helper instance
		this._helper = new core.js.base.views.Helper(id);
		this._helper.setLanguageData(this._i18n);
		
		core.js.base.controllers.Subscriber.unsubscribe(this.TOPIC_GROUP);
	},
	
	setMessageThreadToolbar: function(/*message.js.views.ThreadToolbar*/ toolbar) {
		// summary:
		//		Sets the message thread toolbar
		this._toolbar = toolbar;
		
		// Close handler
		dojo.connect(toolbar, "onClose", this, function() {
			dojo.publish("/app/message/message/view/onClose");
		});
		
		// Refresh handler
		dojo.connect(toolbar, "onRefresh", this, "searchMessages");
		
		// Search handler
		dojo.connect(toolbar, "onSearchComments", this, function(keyword) {
			this.searchMessages({
				keyword: keyword,
				page: 1
			});
		});
		
		return this;	// message.js.controllers.ThreadController
	},
	
	setMessageListView: function(/*message.js.views.MessageListView*/ messageListView) {
		// summary:
		//		Sets the messages list view
		this._messageListView = messageListView;
		
		// Show the context menu
		dojo.connect(messageListView, "onMouseDown", this, function(messageItemView) {
			if (this._messageContextMenu) {
				this._messageContextMenu.show(messageItemView);
			}
		});
		
		return this;	// message.js.controllers.ThreadController
	},
	
	setMessageContextMenu: function(/*message.js.views.MessageContextMenu*/ messageContextMenu) {
		// summary:
		//		Sets the message's context menu
		this._messageContextMenu = messageContextMenu;
		this._mediator.setMessageContextMenu(messageContextMenu);
		
		// Reply message handler
		dojo.connect(messageContextMenu, "onReplyMessage", this, "replyMessage");
		core.js.base.controllers.Subscriber.subscribe(this.TOPIC_GROUP, "/app/message/message/send/onCancel", this, function(id) {
			if (id == this._id) {
				this._helper.removePane();
			}
		});
		
		// Mark as read/unread handler
		dojo.connect(messageContextMenu, "onMarkRead", this, "markRead");
		
		// Star handler
		dojo.connect(messageContextMenu, "onStar", this, "star");
		
		// Delete message handler
		dojo.connect(messageContextMenu, "onDeleteMessage", this, "deleteMessage");
		core.js.base.controllers.Subscriber.subscribe(this.TOPIC_GROUP, "/app/message/message/delete/onCancel", this, function() {
			this._helper.closeDialog();
		});
		core.js.base.controllers.Subscriber.subscribe(this.TOPIC_GROUP, "/app/message/message/delete/onComplete", this, function(data) {
			this._helper.closeDialog();
			dojo.publish("/app/global/notification", [{
				message: this._i18n.message["delete"][(data.result == "APP_RESULT_OK") ? "success" : "error"],
				type: (data.result == "APP_RESULT_OK") ? "message" : "error"
			}]);
			
			if (data.result == "APP_RESULT_OK") {
				var message = this._messageListView.getMessageItemView(data.message_id).getMessage();
				message.deleted = data.deleted;
				dojo.publish("/app/message/message/delete/onSuccess", [ message ]);
				this.searchMessages();
			}
		});
		
		// Move to folder handler
		dojo.connect(messageContextMenu, "onMoveToFolder", this, "moveToFolder");
		
		return this;	// message.js.controllers.ThreadController
	},
	
	initSearchCriteria: function(/*Object*/ criteria) {
		// summary:
		//		Inits the controls with given criteria
		dojo.mixin(this._defaultCriteria, criteria);
		return this;	// message.js.controllers.ThreadController
	},
	
	deleteMessage: function(/*message.js.views.MessageItemView*/ messageItemView) {
		// summary:
		//		Deletes given message item
		var params = {
			message_id: messageItemView.getMessage().message_id,
			deleted: messageItemView.getMessage().deleted
		};
		var url = core.js.base.controllers.ActionProvider.get("message_message_delete").url + "?" + dojo.objectToQuery(params);
		this._helper.showDialog(url, {
			title: this._i18n.message["delete"].title,
			style: "width: 250px",
			refreshOnShow: true
		});
	},
	
	markRead: function(/*message.js.views.MessageItemView*/ messageItemView) {
		// summary:
		//		Marks given message as read one
		var message = messageItemView.getMessage();
		var status  = message.unread + "";
		var _this   = this;
		dojo.xhrPost({
			url: core.js.base.controllers.ActionProvider.get("message_message_mark").url,
			content: {
				folder_id: message.folder_id,
				message_id: message.message_id
			},
			handleAs: "json",
			load: function(data) {
				var notification = (data.result == "APP_RESULT_OK") 
									? (status == "1" ? "markReadSuccess" : "markUnreadSuccess") 
									: (status == "1" ? "markReadError" : "markUnreadError");
				dojo.publish("/app/global/notification", [{
					message: _this._i18n.message.mark[notification],
					type: (data.result == "APP_RESULT_OK") ? "message" : "error"
				}]);
				
				if (data.result == "APP_RESULT_OK") {
					// Update read property
					var newStatus = (status == "1") ? "0" : "1";
					var message   = messageItemView.getMessage();
					message.unread = newStatus;
					dojo.publish("/app/message/message/mark/onSuccess", [ message ]);
				}
			}
		});
	},
	
	moveToFolder: function(/*message.js.views.MessageItemView*/ messageItemView, /*Object*/ folder) {
		// summary:
		//		Moves given message to other folder
		var _this = this;
		this._helper.showStandby();
		dojo.xhrPost({
			url: core.js.base.controllers.ActionProvider.get("message_message_move").url,
			content: {
				message_id: messageItemView.getMessage().message_id,
				folder_id: folder.folder_id
			},
			handleAs: "json",
			load: function(data) {
				_this._helper.hideStandby();
				dojo.publish("/app/global/notification", [{
					message: dojox.string.sprintf(_this._i18n.message.move[(data.result == "APP_RESULT_OK") ? "success" : "error"], folder.name),
					type: (data.result == "APP_RESULT_OK") ? "message" : "error"
				}]);
				
				if (data.result == "APP_RESULT_OK") {
					var message = messageItemView.getMessage();
					dojo.publish("/app/message/message/move/onSuccess", [{ message: message, from_folder_id: message.folder_id, to_folder_id: data.folder_id }]);
					message.folder_id = data.folder_id;
					message.deleted   = "0";
				}
			}
		});
	},
	
	replyMessage: function(/*message.js.views.MessageItemView*/ messageItemView, /*Boolean*/ replyAll) {
		// summary:
		//		Replies to given message
		var params = {
			container_id: this._id,
			message_id: messageItemView.getMessage().message_id,
			reply_all: replyAll
		};
		var url = core.js.base.controllers.ActionProvider.get("message_message_send").url + "?" + dojo.objectToQuery(params);
		this._helper.showPane(url, {
			region: "bottom",
			style: "height: 60%"
		});
	},
	
	star: function(/*message.js.views.MessageItemView*/ messageItemView) {
		// summary:
		//		Stars given message
		var message = messageItemView.getMessage();
		var status  = message.starred + "";
		var _this   = this;
		dojo.xhrPost({
			url: core.js.base.controllers.ActionProvider.get("message_message_star").url,
			content: {
				message_id: message.message_id
			},
			handleAs: "json",
			load: function(data) {
				var message = (data.result == "APP_RESULT_OK") 
								? (status == "1" ? "unstarSuccess" : "starSuccess") 
								: (status == "1" ? "unstarError" : "starError");
				dojo.publish("/app/global/notification", [{
					message: _this._i18n.message.star[message],
					type: (data.result == "APP_RESULT_OK") ? "message" : "error"
				}]);
				
				if (data.result == "APP_RESULT_OK") {
					// Update starred property
					var newStatus = (status == "1") ? "0" : "1";
					messageItemView.getMessage().starred = newStatus;
					dojo.publish("/app/message/message/star/onSuccess", [{ message_id: messageItemView.getMessage().message_id, starred: newStatus }]);
				}
			}
		});
	},
	
	searchMessages: function(/*Object*/ criteria) {
		// summary:
		//		Searches for messages in the thread
		dojo.mixin(this._defaultCriteria, criteria);
		var q   = core.js.base.Encoder.encode(this._defaultCriteria);
		var url = core.js.base.controllers.ActionProvider.get("message_message_view").url;
		
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
				_this._messageListView.setContent(data);
			}
		});
	}
});
