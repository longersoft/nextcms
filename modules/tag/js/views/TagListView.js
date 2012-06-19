/**
 * NextCMS
 * 
 * @author		Nguyen Huu Phuoc <thenextcms@gmail.com>
 * @copyright	Copyright (c) 2011 - 2012, Nguyen Huu Phuoc
 * @license		http://nextcms.org/license.txt	(GNU GPL version 2 or later)
 * @link		http://nextcms.org
 * @category	modules
 * @package		tag
 * @subpackage	js
 * @since		1.0
 * @version		2012-03-28
 */

dojo.provide("tag.js.views.TagListView");

dojo.require("tag.js.views.TagItemView");

dojo.declare("tag.js.views.TagListView", null, {
	// _id: String
	_id: null,
	
	// _domNode: DomNode
	_domNode: null,
	
	constructor: function(/*String*/ id) {
		this._id	  = id;
		this._domNode = dojo.byId(id);
		
		this._init();
	},
	
	setContent: function(/*String*/ html) {
		// summary:
		//		Reloads the entire list by HTML content
		// html:
		//		Entire HTML to show the list of tags
		dijit.byId(this._id).setContent(html);
		this._init();
	},
	
	_init: function() {
		// summary:
		//		Loads all tag item views
		var _this = this;
		dojo.query(".tagTagItem", this._domNode).forEach(function(node) {
			var tagItemView = new tag.js.views.TagItemView(node, _this);
		});
	},
	
	////////// CALLBACKS //////////
	
	onMouseDown: function(/*tag.js.views.TagItemView*/ tagItemView) {
		// summary:
		//		Called when user right-click a tag item
		// tagItemView:
		//		The selected tag item
		// tags:
		//		callback
	}
});
