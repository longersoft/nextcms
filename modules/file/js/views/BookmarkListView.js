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

dojo.provide("file.js.views.BookmarkListView");

dojo.require("dojox.grid.enhanced.plugins.GridSource");

dojo.require("file.js.views.BookmarkItemView");

dojo.declare("file.js.views.BookmarkListView", null, {
	// _id: String
	_id: null,
	
	// _domNode: DomNode
	_domNode: null,
	
	// _bookmarkedPaths: Array
	_bookmarkedPaths: [],
	
	constructor: function(/*String*/ id) {
		this._id 	  = id;
		this._domNode = dojo.byId(id);
		
		this._init();
		this._allowToDragFromGrid();
	},
	
	_init: function() {
		var _this = this;
		this._bookmarkedPaths = [];
		dojo.query(".fileBookmarkItem", this._id).forEach(function(node, index, arr) {
			var bookmarkItemView = new file.js.views.BookmarkItemView(node, _this);
			_this._bookmarkedPaths.push(bookmarkItemView.getBookmark().path);
		});
	},
	
	_allowToDragFromGrid: function() {
		// Allow to drag directory from the grid to the bookmark list
		var bookmarkTarget = new dojox.grid.enhanced.plugins.GridSource(dojo.byId(this._id), {
			isSource: false,
			insertNodesForGrid: false
		});
		dojo.connect(bookmarkTarget, "onDropGridRows", this, function(grid, rowIndexes) {
			// Determine the source items from the grid view
			var sourceItems = [];
			var _this = this;
			
			dojo.forEach(rowIndexes, function(rowIndex, index) {
				var item = grid.getItem(rowIndex);
				
				if (item && item.directory && dojo.indexOf(_this._bookmarkedPaths, item.path) == -1) {
					sourceItems.push(item);
				}
			});
			
			dojo.forEach(sourceItems, function(item, index) {
				_this.onBookmarkDirectory(item);
			});
		});
	},
	
	setContent: function(/*String*/ html) {
		// summary:
		//		Populates the list view
		dijit.byId(this._id).setContent(html);
		this._init();
	},
	
	getBookmarkedPaths: function() {
		// summary:
		//		Gets the list of bookmarked paths
		return this._bookmarkedPaths;	// Array
	},
	
	////////// CALLBACKS //////////
	
	onBookmarkDirectory: function(/*dojo.data.Item*/ item) {
		// summary:
		//		This method is called when dragging the directory from the file grid and drop it to the list
		// tags:
		//		callback
	},
	
	onClickBookmark: function(/*file.js.views.BookmarkItemView*/ bookmarkItemView) {
		// summary:
		//		Called when user click a bookmark item
		// tags:
		//		callback
	},
	
	onRenameBookmark: function(/*file.js.views.BookmarkItemView*/ bookmarkItemView) {
		// summary:
		//		Called when user rename a bookmark item
		// tags:
		//		callback
	},
	
	onUnbookmarkDirectory: function(/*file.js.views.BookmarkItemView*/ bookmarkItemView) {
		// summary:
		//		Called when user remove a bookmark item
		// tags:
		//		callback
	}
});
