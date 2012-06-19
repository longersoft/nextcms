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
 * @version		2012-06-05
 */

dojo.provide("media.js.controllers.VideoMediator");

dojo.require("core.js.base.controllers.Subscriber");

dojo.declare("media.js.controllers.VideoMediator", null, {
	// summary:
	//		This class is used to control the state of controls in the controller
	
	// _videoToolbar: media.js.views.VideoToolbar,
	_videoToolbar: null,
	
	// _videoContextMenu: media.js.views.VideoContextMenu
	_videoContextMenu: null,
	
	// _videoListView: media.js.views.VideoListView
	_videoListView: null,
	
	// _playlistListView: media.js.views.PlaylistListView
	_playlistListView: null,
	
	// _playlistToolbar: media.js.views.PlaylistToolbar
	_playlistToolbar: null,
	
	// TOPIC_GROUP: [const] String
	TOPIC_GROUP: "/media/js/controllers/VideoMediator",
	
	constructor: function() {
		core.js.base.controllers.Subscriber.unsubscribe(this.TOPIC_GROUP);
	},
	
	////////// SET CONTROLS //////////
	
	setPlaylistToolbar: function(/*media.js.views.PlaylistToolbar*/ playlistToolbar) {
		// summary:
		//		Sets playlist toolbar
		this._playlistToolbar = playlistToolbar;
	},
	
	setVideoToolbar: function(/*media.js.views.VideoToolbar*/ videoToolbar) {
		// summary:
		//		Sets the video toolbar
		this._videoToolbar = videoToolbar;
	},
	
	setVideoContextMenu: function(/*media.js.views.VideoContextMenu*/ videoContextMenu) {
		// summary:
		//		Sets the video's context menu
		this._videoContextMenu = videoContextMenu;
		dojo.connect(videoContextMenu, "onContextMenu", this, "onContextMenu");
	},
	
	setVideoListView: function(/*media.js.views.VideoListView*/ videoListView) {
		// summary:
		//		Sets the videos list view
		this._videoListView = videoListView;
		dojo.connect(videoListView, "onPopulateVideos", this, "onPopulateVideos");
	},
	
	setPlaylistListView: function(/*media.js.views.PlaylistListView*/ playlistListView) {
		// summary:
		//		Sets the playlists list view
		this._playlistListView = playlistListView;
		core.js.base.controllers.Subscriber.subscribe(this.TOPIC_GROUP, "/app/media/playlist/list/onViewAll", this, "onViewAllPlaylist");
	},
	
	////////// UPDATE STATE OF CONTROLS //////////
	
	initVideoSearchCriteria: function(/*Object*/ criteria) {
		// summary:
		//		Inits the controls with given criteria. It should be called at the first time the page is loaded
		if (criteria.per_page) {
			this._videoToolbar.setPerPageValue(criteria.per_page);
		}
		this._playlistToolbar.setLanguage(criteria.language);
		this._videoToolbar.allowToSaveOrder(criteria.playlist_id)
						  .setKeyword(criteria.title)
						  .setViewSize(criteria.view_size);
	},
	
	onViewAllPlaylist: function(/*DomNode*/ viewAllNode) {
		this._videoToolbar.allowToSaveOrder(false);
	},
	
	onContextMenu: function(/*media.js.views.VideoItemView*/ videoItemView) {
		// Enable the set cover item if the selected video is not poster of the selected playlist
		var playlistItemView = this._playlistListView.getSelectedPlaylistItemView();
		this._videoContextMenu.allowToSetPoster(playlistItemView && playlistItemView.getPlaylist().poster != videoItemView.getVideo().video_id)
							  // Disable the remove item if the selected video is used as poster of the selected playlist
							  .allowToRemove(playlistItemView && playlistItemView.getPlaylist().poster != videoItemView.getVideo().video_id)
							  .allowToDownload(videoItemView.getVideo().url);
	},
	
	onPopulateVideos: function() {
		// Disable some controls in the video's toolbar if there is no videos
		var numVideos = this._videoListView.getNumVideoItemViews();
		this._videoToolbar.allowToSearch(numVideos > 0)
						  .allowToViewSize(numVideos > 0)
						  .allowToChangePageSize(numVideos > 0)
						  // FIXME: Disable the save order button if there is no playlist selected
						  .allowToSaveOrder(numVideos > 0 && this._playlistListView.getSelectedPlaylistItemView());
	}
});
