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
 * @version		2012-02-28
 */

dojo.provide("message.js.views.MessageContextMenu");

dojo.require("dijit.Menu");
dojo.require("dijit.MenuItem");
dojo.require("dijit.MenuSeparator");
dojo.require("dijit.PopupMenuItem");

dojo.require("core.js.base.Config");
dojo.require("core.js.base.controllers.ActionProvider");
dojo.require("core.js.base.I18N");

dojo.declare("message.js.views.MessageContextMenu", null, {
	// _contextMenu: dijit.Menu
	_contextMenu: null,
	
	// _i18n: Object
	_i18n: null,
	
	// _replyMenuItem: dijit.MenuItem
	_replyMenuItem: null,
	
	// _replyAllMenuItem: dijit.MenuItem
	_replyAllMenuItem: null,
	
	// _markReadMenuItem: dijit.MenuItem
	_markReadMenuItem: null,
	
	// _starMenuItem: dijit.MenuItem
	_starMenuItem: null,
	
	// _deleteMenuItem: dijit.MenuItem
	_deleteMenuItem: null,
	
	// _moveToFolderMenuItems: Object
	_moveToFolderMenuItems: {},
	
	constructor: function() {
		core.js.base.I18N.requireLocalization("message/languages");
		this._i18n = core.js.base.I18N.getLocalization("message/languages");
	},
	
	show: function(/*message.js.views.MessageItemView*/ messageItemView) {
		// summary:
		//		Shows the context menu for each message item in the thread
		var _this = this;
		
		// Get message data
		var message = messageItemView.getMessage();
		
		// Create the menu
		this._contextMenu = new dijit.Menu({
			targetNodeIds: [ dojo.attr(messageItemView.getDomNode(), "id") ]
		});
		
		// "Reply" menu item
		this._replyMenuItem = new dijit.MenuItem({
			label: this._i18n.global._share.replyAction,
			iconClass: "appIcon messageReplyIcon",
			disabled: !core.js.base.controllers.ActionProvider.get("message_message_send").isAllowed,
			onClick: function() {
				_this.onReplyMessage(messageItemView, false);
			}
		});
		this._contextMenu.addChild(this._replyMenuItem);
		
		// "Reply all" menu item
		this._replyMenuItem = new dijit.MenuItem({
			label: this._i18n.global._share.replyAllAction,
			iconClass: "appIcon messageReplyAllIcon",
			disabled: !core.js.base.controllers.ActionProvider.get("message_message_send").isAllowed,
			onClick: function() {
				_this.onReplyMessage(messageItemView, true);
			}
		});
		this._contextMenu.addChild(this._replyMenuItem);
		
		this._contextMenu.addChild(new dijit.MenuSeparator());
		
		// "Mark as read" menu item
		this._markReadMenuItem = new dijit.MenuItem({
			label: (message.unread + "" == "1") ? this._i18n.global._share.markAsReadAction : this._i18n.global._share.markAsUnreadAction,
			iconClass: "appIcon " + ((message.unread + "" == "1") ? "messageReadIcon" : "messageUnreadIcon"),
			disabled: !core.js.base.controllers.ActionProvider.get("message_message_mark").isAllowed,
			onClick: function() {
				_this.onMarkRead(messageItemView);
			}
		});
		this._contextMenu.addChild(this._markReadMenuItem);
		
		// "Star" menu item
		this._starMenuItem = new dijit.MenuItem({
			label: (message.starred + "" == "1") ? this._i18n.global._share.unstarAction : this._i18n.global._share.starAction,
			iconClass: "appIcon " + ((message.starred + "" == "1") ? "appUnstarIcon" : "appStarIcon"),
			disabled: !core.js.base.controllers.ActionProvider.get("message_message_star").isAllowed,
			onClick: function() {
				_this.onStar(messageItemView);
			}
		});
		this._contextMenu.addChild(this._starMenuItem);
		
		// "Delete" menu item
		this._deleteMenuItem = new dijit.MenuItem({
			label: (message.deleted + "" == "1") ? this._i18n.global._share.deleteForeverAction : this._i18n.global._share.deleteAction,
			iconClass: "appIcon appDeleteIcon",
			disabled: !core.js.base.controllers.ActionProvider.get("message_message_delete").isAllowed,
			onClick: function() {
				_this.onDeleteMessage(messageItemView);
			}
		});
		this._contextMenu.addChild(this._deleteMenuItem);
		
		// Get the list of folders
		var folders = core.js.base.Config.get("core", "message_folders");
		if (folders == null) {
			// Get the list of folders from the server
			dojo.xhrPost({
				url: core.js.base.controllers.ActionProvider.get("message_folder_list").url,
				content: {
					format: "json"
				},
				handleAs: "json",
				load: function(data) {
					folders = {};
					dojo.forEach(data, function(folder) {
						folders[folder.folder_id + ""] = folder;
					});
					
					// Cache the list of folders
					core.js.base.Config.set("core", "message_folders", folders);
					_this._createMoveToFolderMenu(messageItemView);
					
					_this._contextMenu.startup();
					_this.onContextMenu(messageItemView);
				}
			});
		} else {
			this._createMoveToFolderMenu(messageItemView);
			
			this._contextMenu.startup();
			
			// Extension point
			this.onContextMenu(messageItemView);
		}
	},
	
	_createMoveToFolderMenu: function(/*message.js.views.MessageItemView*/ messageItemView) {
		// summary:
		//		Creates a popup menu which allows to move message to other folders
		var folders = core.js.base.Config.get("core", "message_folders");
		var moveToPopupMenu = new dijit.Menu();
		var _this = this;
		
		// Add a menu item that allows to move message to "Inbox"
		var moveToInboxMenuItem = new dijit.MenuItem({
			label: this._i18n.folder._share.inboxFolder,
			onClick: function(e) {
				_this.onMoveToFolder(messageItemView, {
					folder_id: "inbox"
				});
			}
		});
		moveToPopupMenu.addChild(moveToInboxMenuItem);
		this._moveToFolderMenuItems["inbox"] = moveToInboxMenuItem;
		
		for (var folderId in folders) {
			var menuItem = new dijit.MenuItem({
				__folderId: folderId,
				label: folders[folderId].name,
				onClick: function(e) {
					_this.onMoveToFolder(messageItemView, folders[this.__folderId]);
				}
			});
			moveToPopupMenu.addChild(menuItem);
			
			this._moveToFolderMenuItems[folderId] = menuItem;
		}
		
		this._contextMenu.addChild(new dijit.PopupMenuItem({
			label: this._i18n.message._share.moveToFolderAction,
			popup: moveToPopupMenu
		}));
	},
	
	////////// CONTROL STATE OF MENU ITEMS //////////
	
	allowToMove: function(/*String*/ folderId) {
		// summary:
		//		Allows/disallows to move the message to folder
		// folderId:
		//		Id of folder
		if (this._moveToFolderMenuItems[folderId]) {
			this._moveToFolderMenuItems[folderId].set("disabled", !core.js.base.controllers.ActionProvider.get("message_message_move").isAllowed);
		}
		return this;	// message.js.views.MessageContextMenu
	},
	
	allowToMoveExceptOne: function(/*String*/ folderId) {
		// summary:
		//		Allows/disallows to move the message to all folders, except the given folder
		// folderId:
		//		Id of folder
		var isAllowed = core.js.base.controllers.ActionProvider.get("message_message_move").isAllowed;
		for (var id in this._moveToFolderMenuItems) {
			this._moveToFolderMenuItems[id].set("disabled", (id == folderId) || !isAllowed);
		}
		return this;	// message.js.views.MessageContextMenu
	},
	
	allowToStar: function(/*Boolean*/ isAllowed) {
		// summary:
		//		Allows/disallows to star the message
		isAllowed = isAllowed && core.js.base.controllers.ActionProvider.get("message_message_star").isAllowed;
		this._starMenuItem.set("disabled", !isAllowed);
		return this;	// message.js.views.MessageContextMenu
	},
	
	////////// CALLBACKS //////////
	
	onContextMenu: function(/*message.js.views.MessageItemView*/ messageItemView) {
		// summary:
		//		Called when right-click on given message item
		// tags:
		//		callback
	},
	
	onDeleteMessage: function(/*message.js.views.MessageItemView*/ messageItemView) {
		// summary:
		//		Deletes given message item
		// tags:
		//		callback
	},
	
	onMarkRead: function(/*message.js.views.MessageItemView*/ messageItemView) {
		// summary:
		//		Marks given message as read one
		// tags:
		//		callback
	},
	
	onMoveToFolder: function(/*message.js.views.MessageItemView*/ messageItemView, /*Object*/ folder) {
		// summary:
		//		Moves given message to other folder
		// tags:
		//		callback
	},
	
	onReplyMessage: function(/*message.js.views.MessageItemView*/ messageItemView, /*Boolean*/ replyAll) {
		// summary:
		//		Replies to given message
		// replyAll:
		//		Indicates replying a message to all recipients or not
		// tags:
		//		callback
	},
	
	onStar: function(/*message.js.views.MessageItemView*/ messageItemView) {
		// summary:
		//		Stars/Unstars the given message item
		// tags:
		//		callback
	}
});
