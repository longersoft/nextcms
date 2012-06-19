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

dojo.provide("media.js.views.AlbumListView");

dojo.require("dojo.dnd.Source");

dojo.require("core.js.base.controllers.ActionProvider");
dojo.require("media.js.views.AlbumItemView");

dojo.declare("media.js.views.AlbumListView", null, {
	// _id: String
	//		Id of the DomNode
	_id: null,
	
	// _domNode: DomNode
	//		The DomNode of album list
	_domNode: null,
	
	// _albumIdViewHash: Object
	//		Maps the album's Id with associated album item. It will be used to retrive the album item based on album's Id
	_albumIdViewHash: {},
	
	// _selectedAlbumItemView: media.js.views.AlbumItemView
	_selectedAlbumItemView: null,
	
	constructor: function(/*String*/ id) {
		this._id      = id;
		this._domNode = dojo.byId(id);
		
		this._init();
	},
	
	getId: function() {
		return this._id;	// String
	},
	
	getDomNode: function() {
		return this._domNode; 	// DomNode
	},
	
	_init: function() {
		var _this = this;
		this._albumIdViewHash = {};
		dojo.query(".mediaAlbumItem", this._id).forEach(function(node, index, arr) {
			var albumItemView = new media.js.views.AlbumItemView(node, _this);
			
			_this._albumIdViewHash[albumItemView.getAlbum().album_id + ""] = albumItemView;
			
			if (dojo.hasClass(node, "mediaAlbumItemSelected")) {
				_this._selectedAlbumItemView = albumItemView;
			}
			
			if (core.js.base.controllers.ActionProvider.get("media_album_cover").isAllowed) {
				var coverNode = albumItemView.getCoverNode();
				new dojo.dnd.Target(coverNode, {
					accept: ["appDndImage"],
					onDropExternal: function(source, nodes, copy) {
						var thumbnails = dojo.attr(nodes[0], "data-app-dndthumbnails");
						if (thumbnails) {
							_this.onUpdateCover(albumItemView, dojo.fromJson(thumbnails));
						}
					}
				});
			}
		});
	},
	
	setContent: function(/*String*/ html) {
		// summary:
		//		Set the content of list view container
		// description:
		//		This method should be called when adding, removing an album item, etc, and I want to 
		//		reload the entire list
		dijit.byId(this._id).setContent(html);
		
		// Re-init
		this._init();
	},
	
	getAlbumItemView: function(/*String*/ albumId) {
		// summary:
		//		Returns the album item view by given album's Id
		if (!this._albumIdViewHash[albumId + ""]) {
			return null;
		}
		return this._albumIdViewHash[albumId + ""];		// media.js.views.AlbumItemView 
	},
	
	setSelectedAlbumItemView: function(/*media.js.views.AlbumItemView*/ albumItemView) {
		if (albumItemView) {
			dojo.query(".mediaAlbumItemSelected", this._domNode).removeClass("mediaAlbumItemSelected");
			dojo.query(albumItemView.getDomNode()).addClass("mediaAlbumItemSelected");
		}
		
		this._selectedAlbumItemView = albumItemView;
	},
	
	getSelectedAlbumItemView: function() {
		return this._selectedAlbumItemView;		// media.js.views.AlbumItemView
	},
	
	setViewType: function(/*String*/ viewType) {
		// summary:
		//		Show the list of albums in a given type of view
		// viewType:
		//		Can be "list" or "grid"
		for (var albumId in this._albumIdViewHash) {
			this._albumIdViewHash[albumId + ""].setViewType(viewType);
		}
	},
	
	////////// CALLBACKS //////////
	
	onClickAlbum: function(/*media.js.views.AlbumItemView*/ albumItemView) {
		// summary:
		//		Called when user click an album item
		// albumItemView:
		//		The selected album item
		// tags:
		//		callback
	},
	
	onDropPhotos: function(/*media.js.views.AlbumItemView*/ albumItemView, /*DomNode[]*/ photoNodes) {
		// summary:
		//		Called when user drop photo items to the selected album
		// tags:
		//		callback
	},
	
	onMouseDown: function(/*media.js.views.AlbumItemView*/ albumItemView) {
		// summary:
		//		Called when user right-click an album item
		// albumItemView:
		//		The selected album item
		// tags:
		//		callback
	},
	
	onUpdateCover: function(/*media.js.views.AlbumItemView*/ albumItemView, /*Object*/ thumbnails) {
		// summary:
		//		Updates the album's cover
		// tags:
		//		callback
	}
});
