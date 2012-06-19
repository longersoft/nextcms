/**
 * NextCMS
 * 
 * @author		Nguyen Huu Phuoc <thenextcms@gmail.com>
 * @copyright	Copyright (c) 2011 - 2012, Nguyen Huu Phuoc
 * @license		http://nextcms.org/license.txt	(GNU GPL version 2 or later)
 * @link		http://nextcms.org
 * @category	modules
 * @package		file
 * @subpackage	hooks
 * @since		1.0
 * @version		2011-10-24
 */

dojo.provide("file.hooks.explorer.FileListView");

dojo.require("file.hooks.explorer.FileItemView");

dojo.declare("file.hooks.explorer.FileListView", null, {
	// _id: String
	_id: null,
	
	// _domNode: DomNode
	_domNode: null,
	
	// _fileItemViews: Array
	//		Array of file item views
	_fileItemViews: [],
	
	constructor: function(/*String*/ id) {
		// summary:
		//		Creates new file list view
		// id:
		//		The ID of list view container. The DomNode has to be declared as a dojox.layout.ContentPane
		this._id 	  = id;
		this._domNode = dojo.byId(id);
		
		dojo.connect(dijit.byId(id), "onDownloadEnd", this, function() {
			this._init();
		});
	},
	
	getDomNode: function() {
		return this._domNode;	// DomNode
	},
	
	_init: function() {
		var _this = this;
		this._fileItemViews = [];
		dojo.query(".fileHooksExplorerFileItem", this._id).forEach(function(node, index, arr) {
			var fileItemView = new file.hooks.explorer.FileItemView(node, _this);
			_this._fileItemViews.push(fileItemView);
		});
	},
	
	setViewType: function(/*String*/ viewType) {
		// summary:
		//		Sets the view type
		// viewType:
		//		Can be "grid" or "list"
		dojo.forEach(this._fileItemViews, function(item) {
			item.setViewType(viewType);
		});
	},
	
	////////// CALLBACKS //////////
	
	onOpenDir: function(/*String*/ path) {
		// summary:
		//		Opens the given dir
		// tags:
		//		callback
	}
});
