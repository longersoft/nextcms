/**
 * NextCMS
 * 
 * @author		Nguyen Huu Phuoc <thenextcms@gmail.com>
 * @copyright	Copyright (c) 2011 - 2012, Nguyen Huu Phuoc
 * @license		http://nextcms.org/license.txt	(GNU GPL version 2 or later)
 * @link		http://nextcms.org
 * @category	modules
 * @package		category
 * @subpackage	js
 * @since		1.0
 * @version		2012-03-28
 */

dojo.provide("category.js.views.FolderItemView");

dojo.require("dojo.dnd.Source");

dojo.require("core.js.base.Encoder");
dojo.require("core.js.Constant");

dojo.declare("category.js.views.FolderItemView", null, {
	// _domNode: DomNode
	//		The DomNode of folder item 
	_domNode: null,
	
	// _folder: Object
	//		Contains the folder properties
	_folder: null,
	
	// _folderListView: category.js.views.FolderListView
	//		The list view that the folder item belong to
	_folderListView: null,
	
	constructor: function(/*DomNode*/ domNode, /*category.js.views.FolderListView?*/ folderListView) {
		this._domNode		 = domNode;
		this._folderListView = folderListView;
		
		this._folder = core.js.base.Encoder.decode(dojo.attr(domNode, "data-app-entity-props"));
		this._init();
	},

	getDomNode: function() {
		return this._domNode;	// DomNode
	},
	
	getFolderNameNode: function() {
		return dojo.query(".categoryFolderName", this._domNode)[0];	// DomNode
	},
	
	getFolder: function() {
		// summary:
		//		Gets the folder object
		return this._folder;	// Object
	},
	
	_init: function() {
		// summary:
		//		Initializes node
		var _this = this;
		var entityClass = this._folder.entity_class;
		
		dojo.connect(this._domNode, "oncontextmenu", function(e) {
			e.preventDefault();
		});
		dojo.connect(this._domNode, "onmousedown", this, function(e) {
			if (dojo.mouseButtons.isRight(e)) {
				e.preventDefault();
				this._folderListView.onMouseDown(this);
			}
		});
		
		dojo.connect(this.getFolderNameNode(), "onclick", this, function() {
			this._folderListView.setSelectedFolderItemView(this);
			this._folderListView.onClickFolder(this);
		});
		
		// Allow to drag multiple items to folder
		new dojo.dnd.Target(this._domNode, {
			accept: ["categoryFolderItemDnd"],
			onDropExternal: function(source, nodes, copy) {
				_this._folderListView.onDropExternal(_this, nodes);
			}
		});
	}
});
