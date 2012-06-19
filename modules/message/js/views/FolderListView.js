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
 * @version		2012-03-28
 */

dojo.provide("message.js.views.FolderListView");

dojo.require("message.js.views.FolderItemView");

dojo.declare("message.js.views.FolderListView", null, {
	// _id: String
	_id: null,
	
	// _domNode: DomNode
	_domNode: null,
	
	// _folderItemMap: Object
	//		Map foler's Id with folder item view
	_folderItemMap: {},
	
	// _selectedFolderId: String
	_selectedFolderId: "inbox",
	
	constructor: function(/*String*/ id) {
		this._id	  = id;
		this._domNode = dojo.byId(id);
		this._init();
	},
	
	_init: function() {
		this._folderItemMap = {};
		
		var _this = this;
		dojo.query(".messageFolderItem", this._id).forEach(function(node, index, arr) {
			var folderItemView = new message.js.views.FolderItemView(node, _this);
			_this._folderItemMap[folderItemView.getFolder().folder_id + ""] = folderItemView;
		});
		
		this.setSelectedFolder(this._selectedFolderId);
	},
	
	getDomNode: function() {
		return this._domNode;	// DomNode
	},
	
	setContent: function(/*String*/ html) {
		// summary:
		//		Reloads the entire list by HTML content
		dijit.byId(this._id).setContent(html);
		
		// Re-init
		this._init();
	},
	
	setSelectedFolder: function(/*String*/ folderId) {
		// summary:
		//		Sets the selected folder
		if (!this._folderItemMap[folderId + ""]) {
			folderId = "inbox";
		}
		this._selectedFolderId = folderId;
		var folderItemView	   = this._folderItemMap[this._selectedFolderId + ""];
		
		dojo.query(".messageFolderItem", this._domNode).removeClass("messageFolderSelected");
		dojo.addClass(folderItemView.getDomNode(), "messageFolderSelected");
	},
	
	increaseUnreadMessages: function(/*String*/ folderId, /*Integer*/ increasingNumber) {
		// summary:
		//		Increases or decreases the number of unread message.
		// description:
		//		It should be called after marking a message as read/unread
		// Get the node showing "inbox" folder
		if (!this._folderItemMap[folderId + ""]) {
			return;
		}
		var folderItemView = this._folderItemMap[folderId + ""];
		var nodes		   = dojo.query(".messageFolderCounter", folderItemView.getDomNode());
		if (nodes.length > 0) {
			var numUnreadMessages = parseInt(nodes[0].innerHTML);
			numUnreadMessages += increasingNumber;
			if (numUnreadMessages >= 0) {
				nodes[0].innerHTML = numUnreadMessages;
			}
		}
	},
	
	////////// CALLBACKS //////////
	
	onClickFolder: function(/*message.js.views.FolderItemView*/ folderItemView) {
		// summary:
		//		Called when user click a folder item
		// folderItemView:
		//		The selected folder item
		// tags:
		//		callback
	},
	
	onMouseDown: function(/*message.js.views.FolderItemView*/ folderItemView) {
		// summary:
		//		Called when user right-click a folder item
		// folderItemView:
		//		The selected folder item
		// tags:
		//		callback
	}
});
