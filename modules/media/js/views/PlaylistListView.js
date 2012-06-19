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

dojo.provide("media.js.views.PlaylistListView");

dojo.require("dojo.dnd.Source");

dojo.require("core.js.base.controllers.ActionProvider");
dojo.require("media.js.views.PlaylistItemView");

dojo.declare("media.js.views.PlaylistListView", null, {
	// _id: String
	//		Id of the DomNode
	_id: null,
	
	// _domNode: DomNode
	_domNode: null,
	
	// _playlistIdViewHash: Object
	//		Maps the playlist's Id with associated playlist item
	_playlistIdViewHash: {},
	
	// _selectedPlaylistItemView: media.js.views.PlaylistItemView
	_selectedPlaylistItemView: null,
	
	constructor: function(/*String*/ id) {
		this._id      = id;
		this._domNode = dojo.byId(id);
		
		this._init();
	},
	
	_init: function() {
		var _this = this;
		this._playlistIdViewHash = {};
		dojo.query(".mediaPlaylistItem", this._id).forEach(function(node, index, arr) {
			var playlistItemView = new media.js.views.PlaylistItemView(node, _this);
			_this._playlistIdViewHash[playlistItemView.getPlaylist().playlist_id + ""] = playlistItemView;
			
			if (dojo.hasClass(node, "mediaPlaylistItemSelected")) {
				_this._selectedPlaylistItemView = playlistItemView;
			}
			
			if (core.js.base.controllers.ActionProvider.get("media_playlist_cover").isAllowed) {
				var coverNode = playlistItemView.getPosterNode();
				new dojo.dnd.Target(coverNode, {
					accept: ["appDndImage"],
					onDropExternal: function(source, nodes, copy) {
						var thumbnails = dojo.attr(nodes[0], "data-app-dndthumbnails");
						if (thumbnails) {
							_this.onUpdatePoster(playlistItemView, dojo.fromJson(thumbnails));
						}
					}
				});
			}
		});
	},
	
	getDomNode: function() {
		return this._domNode;	// Object
	},
	
	getPlaylistItemView: function(/*String*/ playlistId) {
		// summary:
		//		Gets a playlist item view by given playlist's Id
		if (!this._playlistIdViewHash[playlistId + ""]) {
			return null;
		}
		return this._playlistIdViewHash[playlistId + ""];		// media.js.views.PlaylistItemView
	},
	
	getSelectedPlaylistItemView: function() {
		// summary:
		//		Gets the selected playlist item
		return this._selectedPlaylistItemView;		// media.js.views.PlaylistItemView
	},
	
	setContent: function(/*String*/ html) {
		// summary:
		//		Sets the content of list view container
		dijit.byId(this._id).setContent(html);
		
		// Re-init
		this._init();
	},
	
	setSelectedPlaylistItemView: function(/*media.js.views.PlaylistItemView*/ playlistItemView) {
		// summary:
		//		Sets the selected playlist
		if (playlistItemView) {
			dojo.query(".mediaPlaylistItemSelected", this._domNode).removeClass("mediaPlaylistItemSelected");
			dojo.query(playlistItemView.getDomNode()).addClass("mediaPlaylistItemSelected");
		}
		
		this._selectedPlaylistItemView = playlistItemView;
	},
	
	setViewType: function(/*String*/ viewType) {
		// summary:
		//		Shows the list of playlists in a given type of view
		// viewType:
		//		Can be "list" or "grid"
		for (var playlistId in this._playlistIdViewHash) {
			this._playlistIdViewHash[playlistId + ""].setViewType(viewType);
		}
	},
	
	////////// CALLBACKS //////////
	
	onClickPlaylist: function(/*media.js.views.PlaylistItemView*/ playlistItemView) {
		// summary:
		//		Called when user click a playlist item
		// playlistItemView:
		//		The selected playlist item
		// tags:
		//		callback
	},
	
	onDropVideos: function(/*media.js.views.PlaylistItemView*/ playlistItemView, /*DomNode[]*/ videoNodes) {
		// summary:
		//		Called when user drop video items to the selected playlist
		// tags:
		//		callback
	},
	
	onMouseDown: function(/*media.js.views.PlaylistItemView*/ playlistItemView) {
		// summary:
		//		Called when user right-click a playlist item
		// playlistItemView:
		//		The selected playlist item
		// tags:
		//		callback
	},
	
	onUpdatePoster: function(/*media.js.views.PlaylistItemView*/ playlistItemView, /*Object*/ thumbnails) {
		// summary:
		//		Updates the playlist's poster
		// tags:
		//		callback
	}
});
