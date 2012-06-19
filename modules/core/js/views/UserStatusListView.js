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
 * @version		2011-10-18
 */

dojo.provide("core.js.views.UserStatusListView");

dojo.declare("core.js.views.UserStatusListView", null, {
	// _id: String
	_id: null,
	
	// _domNode: DomNode
	_domNode: null,
	
	constructor: function(/*String*/ id) {
		this._id = id;
		this._domNode = dojo.byId(id);
		
		this._init();
	},

	_init: function() {
		var _this = this;
		dojo.query(".coreStatusLabel", this._domNode).forEach(function(node, index, attr) {
			dojo.connect(node, "onclick", function(e) {
				dojo.query(".coreStatusItemSelected", _this._domNode).removeClass("coreStatusItemSelected");
				dojo.addClass(node.parentNode, "coreStatusItemSelected");
				
				var status = dojo.attr(this, "data-app-status");
				if (status == "") {
					status = null;
				}
				_this.onStatusSelected(status);
			});
		});
		
		// Allow to drag and drop articles to the status item to update status
		if (core.js.base.controllers.ActionProvider.get("core_user_activate").isAllowed) {
			dojo.query("li", this._domNode).forEach(function(node) {
				var statusNode = dojo.query("a.coreStatusLabel", node)[0];
				var status = dojo.attr(statusNode, "data-app-status");
				
				if (status != "") {
					new dojo.dnd.Target(node, {
						accept: ["coreUserItemDnd"],
						onDropExternal: function(source, nodes, copy) {
							_this.onUpdateStatus(status, nodes);
						}
					});
				}
			});
		}
	},
	
	increaseUserCounter: function(/*String*/ status, /*Integer*/ increasingNumber) {
		this._updateUserCounter(status, increasingNumber);
		
		// Update the counter of "View all" node
		this._updateUserCounter("", increasingNumber);
	},
	
	_updateUserCounter: function(/*String*/ status, /*Integer*/ increasingNumber) {
		var statusItemNodes = dojo.query('.coreStatusLabel[data-app-status="' + status + '"]', this._domNode);
		if (statusItemNodes.length > 0) {
			var counterNode = dojo.query(".coreStatusUserCounter", statusItemNodes[0].parentNode)[0];
			var numUsers	= parseInt(counterNode.innerHTML);
			counterNode.innerHTML = numUsers + increasingNumber;
		}
	},
	
	////////// CALLBACKS //////////
	
	onStatusSelected: function(/*String?*/ status) {
		// tags:
		//		callback
	},
	
	onUpdateStatus: function(/*String*/ status, /*DomNode[]*/ userNodes) {
		// tags:
		//		callback
	}
});
