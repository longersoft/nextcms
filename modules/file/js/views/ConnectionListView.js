/**
 * NextCMS
 * 
 * @author		Nguyen Huu Phuoc <thenextcms@gmail.com>
 * @copyright	Copyright (c) 2011 - 2012, Nguyen Huu Phuoc
 * @license		http://nextcms.org/license.txt	(GNU GPL version 2 or later)
 * @link		http://nextcms.org
 * @category	modules
 * @package		file
 * @subpackage	js
 * @since		1.0
 * @version		2012-03-28
 */

dojo.provide("file.js.views.ConnectionListView");

dojo.require("file.js.views.ConnectionItemView");

dojo.declare("file.js.views.ConnectionListView", null, {
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
		dojo.query(".fileConnectionItem", this._id).forEach(function(node, index, arr) {
			var connectionItemView = new file.js.views.ConnectionItemView(node, _this);
		});
	},
	
	setContent: function(/*String*/ html) {
		// summary:
		//		Populates the list view
		dijit.byId(this._id).setContent(html);
		this._init();
	},
	
	setSelectedConnection: function(/*file.js.views.ConnectionItemView|null*/ connectionItemView) {
		dojo.query(".fileConnectionItemSelected", this._domNode).removeClass("fileConnectionItemSelected");
		
		if (connectionItemView) {
			dojo.addClass(connectionItemView.getDomNode(), "fileConnectionItemSelected");
		}
	},
	
	////////// CALLBACKS //////////
	
	onMouseDown: function(/*file.js.views.ConnectionItemView*/ connectionItemView) {
		// summary:
		//		Called when user right-click a connection item
		// connectionItemView:
		//		The selected connection item
		// tags:
		//		callback
	}
});
