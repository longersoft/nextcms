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
 * @version		2012-03-28
 */

dojo.provide("comment.js.views.CommentListView");

dojo.require("comment.js.views.CommentItemView");

dojo.declare("comment.js.views.CommentListView", null, {
	// _id: String
	_id: null,
	
	// _domNode: DomNode
	_domNode: null,
	
	constructor: function(/*String*/ id) {
		this._id	  = id;
		this._domNode = dojo.byId(id);
		this._init();
	},
	
	_init: function() {
		var _this = this;
		dojo.query(".commentCommentItem", this._id).forEach(function(node, index, arr) {
			var commentItemView = new comment.js.views.CommentItemView(node, _this);
		});
	},
	
	setContent: function(/*String*/ html) {
		// summary:
		//		Reloads the entire list by HTML content
		// html:
		//		Entire HTML to show the list of comments
		dijit.byId(this._id).setContent(html);
		
		// Re-init
		this._init();
	},
	
	////////// CALLBACKS //////////
	
	onMouseDown: function(/*comment.js.views.CommentItemView*/ commentItemView) {
		// summary:
		//		Called when user right-click a comment item
		// commentItemView:
		//		The selected comment item
		// tags:
		//		callback
	}
});
