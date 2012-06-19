/**
 * NextCMS
 * 
 * @author		Nguyen Huu Phuoc <thenextcms@gmail.com>
 * @copyright	Copyright (c) 2011 - 2012, Nguyen Huu Phuoc
 * @license		http://nextcms.org/license.txt	(GNU GPL version 2 or later)
 * @link		http://nextcms.org
 * @category	modules
 * @package		media
 * @subpackage	js
 * @since		1.0
 * @version		2012-03-28
 */

dojo.provide("media.js.views.AlbumItemView");

dojo.require("dojo.dnd.Source");

dojo.require("core.js.base.Encoder");
dojo.require("core.js.Constant");

dojo.declare("media.js.views.AlbumItemView", null, {
	// _domNode: DomNode
	//		The DomNode of album item 
	_domNode: null,
	
	// _album: Object
	//		Contains the album properties
	_album: null,
	
	// _albumListView: media.js.views.AlbumListView
	//		The list view that the album item belong to
	_albumListView: null,
	
	constructor: function(/*DomNode*/ domNode, /*media.js.views.AlbumListView*/ albumListView) {
		this._domNode		= domNode;
		this._albumListView = albumListView;
		
		// See album/add.ajax.phtml to see the HTML template of album item
		var data = core.js.base.Encoder.decode(dojo.attr(domNode, "data-app-entity-props"));
		this._album = data;
		
		this._init();
		this.setViewType(data.view_type);
	},

	getDomNode: function() {
		return this._domNode;	// DomNode
	},
	
	getAlbumTitleNode: function() {
		return dojo.query(".mediaAlbumTitle", this._domNode)[0];	// DomNode
	},
	
	getCoverNode: function() {
		// summary:
		//		Gets the node that shows the album's cover
		return dojo.query(".mediaAlbumCover", this._domNode)[0];	// DomNode
	},
	
	getAlbum: function() {
		// summary:
		//		Gets the album object
		return this._album;	// Object
	},
	
	_init: function() {
		// summary:
		//		Initializes node
		var _this = this;
		
		dojo.connect(this._domNode, "oncontextmenu", function(e) {
			e.preventDefault();
		});
		dojo.connect(this._domNode, "onmousedown", this, function(e) {
			if (dojo.mouseButtons.isRight(e)) {
				e.preventDefault();
				this._albumListView.onMouseDown(this);
			}
		});
		
		dojo.connect(this.getAlbumTitleNode(), "onclick", this, function() {
			this._albumListView.onClickAlbum(this);
		});
		
		// Allow to drag multiple photos to album
		new dojo.dnd.Target(this._domNode, {
			accept: ["mediaPhotoItemDnd"],
			onDropExternal: function(source, nodes, copy) {
				_this._albumListView.onDropPhotos(_this, nodes);
			}
		});
	},
	
	increasePhotoCounter: function(/*Integer*/ increasingNumber) {
		// summary:
		//		Increases (or descreases) the number of photos in the album
		// increasingNumber:
		//		The number of photos that will be added to or removed from the album
		var nodes =  dojo.query(".mediaAlbumPhotoCounter", this._domNode);
		if (nodes.length > 0) {
			var numPhotos = parseInt(nodes[0].innerHTML);
			nodes[0].innerHTML = numPhotos + increasingNumber;
		}
	},
	
	setViewType: function(/*String*/ viewType) {
		// summary:
		//		Sets the view type
		// viewType:
		//		Can be "list" or "grid"
		var currentClass = (viewType == "list") ? "mediaAlbumItemGridView" : "mediaAlbumItemListView";
		var newClass	 = (viewType == "list") ? "mediaAlbumItemListView" : "mediaAlbumItemGridView";
		
		dojo.removeClass(this._domNode, currentClass);
		dojo.addClass(this._domNode, newClass);
	},
	
	updateCover: function(/*Object*/ thumbnails) {
		// summary:
		//		Updates the cover image
		// thumbnails:
		//		Contains the following members:
		//		- photo_id: Id of cover photo
		//		- "square", "thumbnail", "small", "crop", "medium", "large", "original": 
		//		Value of these members are the full URL associating with the size
		var cover = this.getCoverNode();
		dojo.attr(cover, "src", core.js.Constant.normalizeUrl(thumbnails.square));
		
		// Update album data
		this._album.cover = thumbnails.photo_id;
	}
});
