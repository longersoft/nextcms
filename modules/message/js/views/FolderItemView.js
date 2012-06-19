/**
 * NextCMS
 * 
 * @author		Nguyen Huu Phuoc <thenextcms@gmail.com>
 * @copyright	Copyright (c) 2011 - 2012, Nguyen Huu Phuoc
 * @license		http://nextcms.org/license.txt	(GNU GPL version 2 or later)
 * @link		http://nextcms.org
 * @category	modules
 * @package		message
 * @subpackage	js
 * @since		1.0
 * @version		2012-03-28
 */

dojo.provide("message.js.views.FolderItemView");

dojo.require("core.js.base.Encoder");

dojo.declare("message.js.views.FolderItemView", null, {
	// _domNode: DomNode
	_domNode: null,
	
	// _folderListView: message.js.views.FolderListView
	_folderListView: null,
	
	// _folder: Object
	_folder: null,
	
	constructor: function(/*DomNode*/ domNode, /*message.js.views.FolderListView*/ folderListView) {
		this._domNode		 = domNode;
		this._folderListView = folderListView;
		
		this._folder = core.js.base.Encoder.decode(dojo.attr(domNode, "data-app-entity-props"));
		// Set "id" attribute
		dojo.attr(this._domNode, "id", "messageFolderItemView_" + this._folder.folder_id);
		this._init();
	},
	
	getDomNode: function() {
		return this._domNode;	// DomNode
	},
	
	getFolder: function() {
		// summary:
		//		Gets folder's properties
		return this._folder;	// Object
	},
	
	getFolderNameNode: function() {
		// summary:
		//		Gets the node showing the folder's name
		return dojo.query(".messageFolderName", this._domNode)[0];	// DomNode
	},
	
	_init: function() {
		// summary:
		//		Inits folder item view
		var _this = this;
		
		dojo.connect(this._domNode, "oncontextmenu", function(e) {
			e.preventDefault();
		});
		dojo.connect(this._domNode, "onmousedown", this, function(e) {
			if (dojo.mouseButtons.isRight(e)) {
				e.preventDefault();
				this._folderListView.onMouseDown(this);
			}
		});
		
		dojo.connect(this.getFolderNameNode(), "onclick", this, function(e) {
			this._folderListView.onClickFolder(this);
		});
	}
});
