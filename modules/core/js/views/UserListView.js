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
 * @version		2012-05-25
 */

dojo.provide("core.js.views.UserListView");

dojo.require("dojo.dnd.Source");

dojo.require("core.js.base.controllers.ActionProvider");
dojo.require("core.js.views.UserItemView");

dojo.declare("core.js.views.UserListView", null, {
	// _id: String
	_id: null,
	
	// _userItemMap: Object
	//		Map the user Id with user item
	_userItemMap: {},
	
	constructor: function(/*String*/ id) {
		this._id = id;
		this._init();
	},
	
	_init: function() {
		// summary:
		//		Finds all children nodes, create a user item for each node and init it
		var _this = this;
		
		this._userItemMap = {};
		dojo.query(".coreUserItem", this._id).forEach(function(node, index, arr) {
			var userItemView = new core.js.views.UserItemView(node, _this);
			_this._userItemMap[userItemView.getUser().user_id + ""] = userItemView;
		});
		
		// FIXME: Do not allow to move the user node in users container
		// The same way is use dojo.dnd.AutoSource
		// Set autoSync=true ensure that the DnD operations will be done as usual if the data changes
		if (core.js.base.controllers.ActionProvider.get("core_user_move").isAllowed) {
			var container = dojo.query(".coreUserItemsContainer", this._id)[0];
			if (container) {
				new dojo.dnd.Source(container, {
					accept: [],
					selfAccept: false,
					selfCopy: false,
					autoSync: true
				});
			}
		}
	},
	
	getUserItemView: function(/*String*/ userId) {
		return this._userItemMap[userId + ""];	// core.js.views.UserItemView
	},
	
	increaseUserCounter: function(/*Integer*/ increasingNumber) {
		// summary:
		//		Increases (or descreases) the number of users in the list
		// increasingNumber:
		//		The number of users that will be added to or removed from the list
		var nodes = dojo.query(".coreUserListCounter", this._domNode);
		if (nodes.length > 0) {
			nodes[0].innerHTML = parseInt(nodes[0].innerHTML) + increasingNumber;
		}
	},
	
	removeUserItemView: function(/*String*/ userId) {
		// summary:
		//		Removes user item from the list view
		// userId:
		//		User's Id
		var userItemView = this._userItemMap[userId + ""];
		if (userItemView) {
			delete this._userItemMap[userId + ""];
			dojo.destroy(userItemView.getDomNode());
			
			this.increaseUserCounter(-1);
		}
	},
	
	setContent: function(/*String*/ html) {
		// summary:
		//		Reloads the entire list by HTML content
		// html:
		//		Entire HTML for showing the list of users
		dijit.byId(this._id).setContent(html);
		
		// Re-init
		this._init();
	},
	
	////////// CALLBACKS //////////
	
	onDropAvatar: function(/*core.js.views.UserItemView*/ userItemView, /*String*/ avatarUrl) {
		// summary:
		//		Called when user drop an image to user's avatar area
		// userItemView:
		//		The selected user item
		// tags:
		//		callback
	},
	
	onMouseDown: function(/*core.js.views.UserItemView*/ userItemView) {
		// summary:
		//		Called when user right-click an user item
		// userItemView:
		//		The selected user item
		// tags:
		//		callback
	}
});
