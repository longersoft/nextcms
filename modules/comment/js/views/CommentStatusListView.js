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
 * @version		2011-10-18
 */

dojo.provide("comment.js.views.CommentStatusListView");

dojo.declare("comment.js.views.CommentStatusListView", null, {
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
		
		dojo.query(".commentCommentStatusLabel", this._domNode).forEach(function(node, index, attr) {
			dojo.connect(node, "onclick", function(e) {
				dojo.query(".commentCommentStatusSelected", _this._domNode).removeClass("commentCommentStatusSelected");
				dojo.addClass(node.parentNode, "commentCommentStatusSelected");
				
				var status = dojo.attr(this, "data-app-status");
				if (status == "") {
					status = null;
				}
				_this.onStatusSelected(status);
			});
		});
	},
	
	increaseCommentCounter: function(/*String*/ status, /*Integer*/ increasingNumber) {
		this._updateCommentCounter(status, increasingNumber);
		
		// Update the counter of "View all" node
		this._updateCommentCounter("", increasingNumber);
	},
	
	_updateCommentCounter: function(/*String*/ status, /*Integer*/ increasingNumber) {
		var statusItemNodes = dojo.query('.commentCommentStatusLabel[data-app-status="' + status + '"]', this._domNode);
		if (statusItemNodes.length > 0) {
			var counterNode = dojo.query(".commentCommentStatusCounter", statusItemNodes[0].parentNode)[0];
			var numComments	= parseInt(counterNode.innerHTML);
			counterNode.innerHTML = numComments + increasingNumber;
		}
	},
	
	setCommentCounter: function(/*String?*/ status, /*Integer*/ numComments) {
		// summary:
		//		Updates the number of comments which have the same status
		if (status == null) {
			status = "";
		}
		var statusItemNodes = dojo.query('.commentCommentStatusLabel[data-app-status="' + status + '"]', this._domNode);
		if (statusItemNodes.length > 0) {
			var counterNode = dojo.query(".commentCommentStatusCounter", statusItemNodes[0].parentNode)[0];
			counterNode.innerHTML = numComments;
		}
	},
	
	////////// CALLBACKS //////////
	
	onStatusSelected: function(/*String?*/ status) {
		// summary:
		//		Called when clicking on a status item
		// tags:
		//		callback
	}
});
