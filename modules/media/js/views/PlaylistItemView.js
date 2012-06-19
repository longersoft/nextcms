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

dojo.provide("media.js.views.PlaylistItemView");

dojo.require("dojo.dnd.Source");

dojo.require("core.js.base.Encoder");
dojo.require("core.js.Constant");

dojo.declare("media.js.views.PlaylistItemView", null, {
	// _domNode: DomNode
	//		The DomNode of playlist item 
	_domNode: null,
	
	// _playlistTitleNode: DomNode
	_playlistTitleNode: null,
	
	// _playlist: Object
	//		Contains the playlist properties
	_playlist: null,
	
	// _playlistListView: media.js.views.PlaylistListView
	//		The list view that the playlist item belong to
	_playlistListView: null,
	
	constructor: function(/*DomNode*/ domNode, /*media.js.views.PlaylistListView*/ playlistListView) {
		this._domNode			= domNode;
		this._playlistTitleNode = dojo.query(".mediaPlaylistTitle", this._domNode)[0]
		this._playlistListView	= playlistListView;
		
		var data = core.js.base.Encoder.decode(dojo.attr(domNode, "data-app-entity-props"));
		this._playlist = data;
		
		this._init();
		this.setViewType(data.view_type);
	},

	getDomNode: function() {
		return this._domNode;	// DomNode
	},
	
	getPosterNode: function() {
		// summary:
		//		Gets the node that shows the playlist's poster
		return dojo.query(".mediaPlaylistPoster", this._domNode)[0];	// DomNode
	},
	
	getPlaylist: function() {
		// summary:
		//		Gets the playlist object
		return this._playlist;	// Object
	},
	
	_init: function() {
		// summary:
		//		Initialize node
		var _this = this;
		
		dojo.connect(this._playlistTitleNode, "onclick", this, function(e) {
			this._playlistListView.onClickPlaylist(this);
		});
		
		dojo.connect(this._domNode, "oncontextmenu", function(e) {
			e.preventDefault();
		});
		dojo.connect(this._domNode, "onmousedown", this, function(e) {
			if (dojo.mouseButtons.isRight(e)) {
				e.preventDefault();
				this._playlistListView.onMouseDown(this);
			}
		});
		
		// Allow to drag multiple videos to playlist
		new dojo.dnd.Target(this._domNode, {
			accept: ["mediaVideoItemDnd"],
			onDropExternal: function(source, nodes, copy) {
				_this._playlistListView.onDropVideos(_this, nodes);
			}
		});
	},
	
	increaseVideoCounter: function(/*Integer*/ increasingNumber) {
		// summary:
		//		Increases (or descreases) the number of videos in the playlist
		// increasingNumber:
		//		The number of videos that will be added to or removed from the playlist
		var nodes =  dojo.query(".mediaPlaylistVideoCounter", this._domNode);
		if (nodes.length > 0) {
			var numVideos = parseInt(nodes[0].innerHTML);
			nodes[0].innerHTML = numVideos + increasingNumber;
		}
	},
	
	setViewType: function(/*String*/ viewType) {
		// summary:
		//		Sets the view type
		// viewType:
		//		Can be "list" or "grid"
		var currentClass = (viewType == "list") ? "mediaPlaylistItemGridView" : "mediaPlaylistItemListView";
		var newClass	 = (viewType == "list") ? "mediaPlaylistItemListView" : "mediaPlaylistItemGridView";
		
		dojo.removeClass(this._domNode, currentClass);
		dojo.addClass(this._domNode, newClass);
	},
	
	updatePoster: function(/*Object*/ thumbnails) {
		// summary:
		//		Updates the poster image
		// thumbnails:
		//		Contains the following members:
		//		- video_id: Id of poster video
		//		- "square", "thumbnail", "small", "crop", "medium", "large", "original": 
		//		Value of these members are the full URL associating with the size
		var poster = this.getPosterNode();
		dojo.attr(poster, "src", core.js.Constant.normalizeUrl(thumbnails.square));
		
		// Update playlist data
		this._playlist.poster = thumbnails.video_id;
	},
	
	updateTitle: function(/*String*/ title, /*String*/ shortTitle) {
		// summary:
		//		Updates playlist's title
		this._playlist.title	   = title;
		this._playlist.short_title = shortTitle;
		this._playlistTitleNode.innerHTML = title;
	}
});
