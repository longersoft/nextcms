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
 * @version		2011-10-26
 */

dojo.provide("core.js.views.UserCollectionView");

dojo.require("dojo.dnd.Source");

dojo.require("core.js.base.Encoder");

dojo.declare("core.js.views.UserCollectionView", null, {
	// summary:
	//		This class shows the list of users which can be dragged from the Users Toolbox
	
	// _id: String
	_id: null,
	
	// _clearDiv: DomNode
	_clearDiv: null,
	
	// _inputName: String
	_inputName: null,
	
	// _users: Array
	//		Map user's Id with user data
	_users: {},
	
	constructor: function(/*String*/ id, /*String?*/ inputName) {
		// inputName:
		//		If this param is defined, the view will generate hidden inputs
		//		containing the user's Ids
		this._id		= id;
		this._inputName = inputName;
		this._users		= {};
		
		dojo.addClass(this._id, "coreUserCollectionItemsContainer");
		this._clearDiv = dojo.create("div", {
			style: "clear: both"
		}, this._id);
		
		this._init();
	},
	
	_init: function() {
		// summary:
		//		Allows to drag the user item from the Users Toolbox
		//		and drop to the view container
		var _this = this;
		
		new dojo.dnd.Target(this._id, {
			accept: ["coreUserItemDnd"],
			onDropExternal: function(source, nodes, copy) {
				dojo.forEach(nodes, function(node) {
					var user = core.js.base.Encoder.decode(dojo.attr(node, "data-app-entity-props"));
					_this.addUser(user);
				});
			}
		});
	},
	
	addUser: function(/*Object*/ user) {
		// summary:
		//		Adds user
		// user:
		//		Contains user's Id and username
		var _this = this;
		if (this._users[user.user_id]) {
			return;
		}
		this._users[user.user_id] = user;
		
		// Add a DIV container to show the user
		var div = dojo.create("div", {
			className: "coreUserCollectionItem"
		}, this._clearDiv, "before");
		var span = dojo.create("span", {
			innerHTML: user.user_name
		}, div);
		
		// Remove the div if click on the username
		dojo.connect(span, "onclick", function() {
			dojo.query(div).orphan();
			delete _this._users[user.user_id];
		});
		
		// Add hidden input
		if (this._inputName) {
			dojo.create("input", {
				type: "hidden",
				name: this._inputName,
				value: user.user_id
			}, div);
		}
	}
});
