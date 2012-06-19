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

dojo.provide("core.js.views.UserItemView");

dojo.require("dojo.dnd.Source");

dojo.require("core.js.base.controllers.ActionProvider");
dojo.require("core.js.base.Encoder");
dojo.require("core.js.Constant");

dojo.declare("core.js.views.UserItemView", null, {
	// _domNode: DomNode
	_domNode: null,
	
	// _userListView: core.js.views.UserListView
	_userListView: null,
	
	// _user: Object
	// 		Represents user data
	_user: null,
	
	constructor: function(/*DomNode*/ domNode, /*core.js.views.UserListView*/ userListView) {
		this._domNode	   = domNode;
		this._userListView = userListView;
		// Create an user object based on the node's property
		this._user		   = core.js.base.Encoder.decode(dojo.attr(domNode, "data-app-entity-props"));
		
		this._init();
	},
	
	getDomNode: function() {
		return this._domNode;		// DomNode
	},
	
	getUser: function() {
		// summary:
		//		Gets user's properties
		return this._user;			// Object
	},
	
	_init: function() {
		// summary:
		//		Initializes node
		var _this = this;
		
		dojo.connect(this._domNode, "oncontextmenu", function(e) {
			e.preventDefault();
		});
		dojo.connect(this._domNode, "onmousedown", this, function(e) {
			if (dojo.mouseButtons.isRight(e)) {
				e.preventDefault();
				this._userListView.onMouseDown(this);
			}
		});
		
		// Allow to drag and drop image to update user's avatar
		if (core.js.base.controllers.ActionProvider.get("core_user_avatar").isAllowed) {
			var avatarNode = dojo.query(".coreUserItemAvatar", this._domNode)[0];
			new dojo.dnd.Target(avatarNode, {
				accept: ["appDndImage"],
				onDropExternal: function(source, nodes, copy) {
					var data = dojo.attr(nodes[0], "data-app-dndimage"), url;
					if (data) {
						data = dojo.fromJson(data);
						url  = data.url;
					} else {
						// Try to find the first img element
						var images = dojo.query("img", nodes[0]);
						if (images.length > 0) {
							url = dojo.attr(images[0], "src");
						}
					}
					
					_this._userListView.onDropAvatar(_this, url);
				}
			});
		}
	},
	
	updateAvatar: function(/*String*/ avatarUrl) {
		// summary:
		//		Updates user's avatar
		var img = dojo.query(".coreUserItemAvatar img", this._domNode)[0];
		dojo.attr(img, {
			src: core.js.Constant.normalizeUrl(avatarUrl)
		});
	}
});
