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
 * @version		2012-03-29
 */

dojo.provide("file.js.views.BookmarkItemView");

dojo.require("core.js.base.Encoder");

dojo.declare("file.js.views.BookmarkItemView", null, {
	// _domNode: DomNode
	_domNode: null,
	
	// _bookmark: Object
	_bookmark: null,
	
	// _bookmarkListView: file.js.views.BookmarkListView
	_bookmarkListView: null,
	
	constructor: function(/*DomNode*/ domNode, /*file.js.views.BookmarkListView*/ bookmarkListView) {
		this._domNode		   = domNode;
		this._bookmarkListView = bookmarkListView;
		this._bookmark		   = dojocore.js.base.Encoder.decode(dojo.attr(domNode, "data-app-entity-props")); 
		this._init();
	},
	
	_init: function() {
		// summary:
		//		Initializes node
		var _this = this;
		dojo.connect(this.getBookmarkNameNode(), "onclick", this, function(e) {
			this._bookmarkListView.onClickBookmark(this);
		});
		
		var renameIconNode = dojo.query(".fileBookmarkRenameIcon", this._domNode)[0];
		// Show/hide the renaming icon when moving the mouse over/out the bookmark item
		dojo.connect(this._domNode, "onmouseover", function(e) {
			dojo.style(renameIconNode, "display", "block");
		});
		dojo.connect(this._domNode, "onmouseout", function(e) {
			dojo.style(renameIconNode, "display", "none");
		});
		dojo.connect(renameIconNode, "onclick", this, function(e) {
			this._bookmarkListView.onRenameBookmark(this);
		});
		
		var iconNode = dojo.query(".fileBookmarkUnbookmarkIcon", this._domNode)[0];
		dojo.connect(iconNode, "onclick", this, function(e) {
			this._bookmarkListView.onUnbookmarkDirectory(this);
		});
	},
	
	getDomNode: function() {
		return this._domNode;	// DomNode
	},
	
	getBookmark: function() {
		// summary:
		//		Gets bookmark's properties
		return this._bookmark;	// Object
	},
	
	getBookmarkNameNode: function() {
		return dojo.query(".fileBookmarkName", this._domNode)[0];	// DomNode
	}
});
