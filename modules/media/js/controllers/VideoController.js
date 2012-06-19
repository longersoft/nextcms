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
 * @version		2012-06-18
 */

dojo.provide("media.js.controllers.VideoController");

dojo.require("dojo.io.iframe");

dojo.require("core.js.base.controllers.ActionProvider");
dojo.require("core.js.base.controllers.Subscriber");
dojo.require("core.js.base.I18N");
dojo.require("core.js.base.Encoder");
dojo.require("core.js.base.views.Helper");
dojo.require("media.js.controllers.VideoMediator");

dojo.declare("media.js.controllers.VideoController", null, {
	// _id: String
	_id: null,
	
	// _i18n: Object
	_i18n: null,
	
	// _helper: core.js.base.views.Helper
	_helper: null,
	
	// _mediator: media.js.controllers.VideoMediator
	_mediator: new media.js.controllers.VideoMediator(),
	
	// TOPIC_GROUP: [const] String
	TOPIC_GROUP: "/media/js/controllers/VideoController",
	
	constructor: function(/*String*/ id) {
		this._id = id;
		
		core.js.base.I18N.requireLocalization("media/languages");
		this._i18n = core.js.base.I18N.getLocalization("media/languages");
		
		// Create helper instance
		this._helper = new core.js.base.views.Helper(id);
		this._helper.setLanguageData(this._i18n);
		
		core.js.base.controllers.Subscriber.unsubscribe(this.TOPIC_GROUP);
	},
	
	////////// MANAGE PLAYLISTS //////////
	
	// _playlistToolbar: media.js.views.PlaylistToolbar
	_playlistToolbar: null,
	
	// _playlistListView: media.js.views.PlaylistListView
	_playlistListView: null,
	
	// _playlistContextMenu: media.js.views.PlaylistContextMenu
	_playlistContextMenu: null,
	
	// _playlistSearchCriteria: Object
	_playlistSearchCriteria: {
		status: null,
		title: null,
		page: 1,
		active_playlist_id: null,
		view_type: "list",
		language: null
	},
	
	setPlaylistToolbar: function(/*media.js.views.PlaylistToolbar*/ playlistToolbar) {
		// summary:
		//		Sets playlist toolbar
		this._playlistToolbar = playlistToolbar;
		this._mediator.setPlaylistToolbar(playlistToolbar);
		
		// Add playlist handler
		dojo.connect(playlistToolbar, "onAddPlaylist", this, "addPlaylist");
		core.js.base.controllers.Subscriber.subscribe(this.TOPIC_GROUP, "/app/media/playlist/add/onCancel", this, function() {
			this._helper.closeDialog();
		});
		core.js.base.controllers.Subscriber.subscribe(this.TOPIC_GROUP, "/app/media/playlist/add/onComplete", this, function(data) {
			this._helper.closeDialog();
			dojo.publish("/app/global/notification", [{
				message: this._i18n.playlist.add[(data.result == "APP_RESULT_OK") ? "success" : "error"],
				type: (data.result == "APP_RESULT_OK") ? "message" : "error"
			}]);
			this.searchPlaylists();
		});
		
		// Refresh handler
		dojo.connect(playlistToolbar, "onRefresh", this, "searchPlaylists");
		
		// Search handler
		dojo.connect(playlistToolbar, "onSearchPlaylists", this, function(title) {
			this.searchPlaylists({
				title: title,
				page: 1
			});
		});
		
		// Switch to other language handler
		dojo.connect(playlistToolbar, "onSwitchToLanguage", this, function(language) {
			this.searchPlaylists({
				active_playlist_id: null,
				title: null,
				page: 1,
				language: language
			});
			this.searchVideos({
				title: null,
				playlist_id: null,
				page: 1,
				language: language
			});
		});
		
		// Change view type handler
		dojo.connect(playlistToolbar, "onChangeViewType", this, function(viewType) {
			this._playlistSearchCriteria.view_type = viewType;
			if (this._playlistListView) {
				this._playlistListView.setViewType(viewType);
			}
		});
		
		return this;	// media.js.controllers.VideoController
	},
	
	setPlaylistListView: function(/*media.js.views.PlaylistListView*/ playlistListView) {
		// summary:
		//		Sets playlist list view
		this._playlistListView = playlistListView;
		this._mediator.setPlaylistListView(playlistListView);
		
		// Shows the context menu
		dojo.connect(playlistListView, "onMouseDown", this, function(playlistItemView) {
			if (this._playlistContextMenu) {
				this._playlistContextMenu.show(playlistItemView);
			}
		});
		
		// Load the list of videos in the selected playlist when selecting a playlist
		dojo.connect(playlistListView, "onClickPlaylist", this, function(playlistItemView) {
			this._playlistListView.setSelectedPlaylistItemView(playlistItemView);
			
			var playlistId = playlistItemView.getPlaylist().playlist_id;
			this._playlistSearchCriteria.active_playlist_id = playlistId;
			this.searchVideos({
				page: 1,
				playlist_id: playlistId
			});
		});
		
		dojo.connect(playlistListView, "onDropVideos", this, "dropVideos");
		
		// Update playlist's poster handler
		dojo.connect(playlistListView, "onUpdatePoster", this, "updatePlaylistPoster");
		
		// Rename playlist handler
		core.js.base.controllers.Subscriber.subscribe(this.TOPIC_GROUP, "/app/media/playlist/rename/onCancel", this, function() {
			this._helper.closeDialog();
		});
		core.js.base.controllers.Subscriber.subscribe(this.TOPIC_GROUP, "/app/media/playlist/rename/onComplete", this, function(data) {
			this._helper.closeDialog();
			dojo.publish("/app/global/notification", [{
				message: this._i18n.playlist.rename[(data.result == "APP_RESULT_OK") ? "success" : "error"],
				type: (data.result == "APP_RESULT_OK") ? "message" : "error"
			}]);
			
			if (data.result == "APP_RESULT_OK") {
				var playlistItemView = this._playlistListView.getPlaylistItemView(data.playlist_id);
				if (playlistItemView) {
					playlistItemView.updateTitle(data.title, data.short_title);
				}
			}
		});
		
		// View all handler
		core.js.base.controllers.Subscriber.subscribe(this.TOPIC_GROUP, "/app/media/playlist/list/onViewAll", this, function(viewAllNode) {
			this._playlistListView.setSelectedPlaylistItemView(null);
			
			dojo.query(".mediaPlaylistItemSelected", this._playlistListView.getDomNode()).removeClass("mediaPlaylistItemSelected");
			dojo.query(viewAllNode).addClass("mediaPlaylistItemSelected");
			this._playlistSearchCriteria.active_playlist_id = null;
			this.searchVideos({
				page: 1,
				playlist_id: null
			});
		});
		
		return this;	// media.js.controllers.VideoController
	},
	
	setPlaylistContextMenu: function(/*media.js.views.PlaylistContextMenu*/ playlistContextMenu) {
		// summary:
		//		Sets playlist context menu
		this._playlistContextMenu = playlistContextMenu;
		
		// Activate handler
		dojo.connect(playlistContextMenu, "onActivatePlaylist", this, "activatePlaylist");
		
		// Delete playlist handler
		dojo.connect(playlistContextMenu, "onDeletePlaylist", this, "deletePlaylist");
		core.js.base.controllers.Subscriber.subscribe(this.TOPIC_GROUP, "/app/media/playlist/delete/onCancel", this, function() {
			this._helper.closeDialog();
		});
		core.js.base.controllers.Subscriber.subscribe(this.TOPIC_GROUP, "/app/media/playlist/delete/onComplete", this, function(data) {
			this._helper.closeDialog();
			dojo.publish("/app/global/notification", [{
				message: this._i18n.playlist["delete"][(data.result == "APP_RESULT_OK") ? "success" : "error"],
				type: (data.result == "APP_RESULT_OK") ? "message" : "error"
			}]);
			this.searchPlaylists();
		});
		
		// Rename playlist handler
		dojo.connect(playlistContextMenu, "onRenamePlaylist", this, "renamePlaylist");
		
		// Add video handler
		dojo.connect(playlistContextMenu, "onAddVideo", this, function(playlistItemView) {
			var playlistId = playlistItemView.getPlaylist().playlist_id;
			this.addVideo(playlistId);
		});
		
		return this;	// media.js.controllers.VideoController
	},
	
	activatePlaylist: function(/*media.js.views.PlaylistItemView*/ playlistItemView) {
		// summary:
		//		Activates/deactivates given playlist
		var status = playlistItemView.getPlaylist().status;
		var _this  = this;
		dojo.xhrPost({
			url: core.js.base.controllers.ActionProvider.get("media_playlist_activate").url,
			content: {
				playlist_id: playlistItemView.getPlaylist().playlist_id
			},
			handleAs: "json",
			load: function(data) {
				var message = (data.result == "APP_RESULT_OK") 
							  ? (status == "activated" ? "deactivateSuccess" : "activateSuccess") 
							  : (status == "activated" ? "deactivateError" : "activateError");
				dojo.publish("/app/global/notification", [{
					message: _this._i18n.playlist.activate[message],
					type: (data.result == "APP_RESULT_OK") ? "message" : "error"
				}]);
				
				if (data.result == "APP_RESULT_OK") {
					playlistItemView.getPlaylist().status = (status == "activated") ? "not_activated" : "activated";
				}
			}
		});
	},
	
	addPlaylist: function() {
		// summary:
		//		Adds new playlist
		var params = {
			language: this._playlistSearchCriteria.language
		};
		var url = core.js.base.controllers.ActionProvider.get("media_playlist_add").url + "?" + dojo.objectToQuery(params);
		this._helper.showDialog(url, {
			title: this._i18n.playlist.add.title,
			style: "width: 400px",
			refreshOnShow: true
		});
	},
	
	deletePlaylist: function(/*media.js.views.PlaylistItemView*/ playlistItemView) {
		// summary:
		//		Deletes given playlist
		var params = {
			playlist_id: playlistItemView.getPlaylist().playlist_id
		};
		var url = core.js.base.controllers.ActionProvider.get("media_playlist_delete").url + "?" + dojo.objectToQuery(params);
		this._helper.showDialog(url, {
			title: this._i18n.playlist["delete"].title,
			style: "width: 250px",
			refreshOnShow: true
		});
	},
	
	renamePlaylist: function(/*media.js.views.PlaylistItemView*/ playlistItemView) {
		// summary:
		//		Renames given playlist
		var params = {
			playlist_id: playlistItemView.getPlaylist().playlist_id
		};
		var url = core.js.base.controllers.ActionProvider.get("media_playlist_rename").url + "?" + dojo.objectToQuery(params);
		this._helper.showDialog(url, {
			title: this._i18n.playlist.rename.title,
			style: "width: 400px",
			refreshOnShow: true
		});
	},
	
	searchPlaylists: function(/*Object*/ criteria) {
		// summary:
		//		Searches for playlists
		this._helper.closeDialog();
		
		var _this = this;
		dojo.mixin(this._playlistSearchCriteria, criteria);
		var q = core.js.base.Encoder.encode(this._playlistSearchCriteria);
		
		dojo.xhrPost({
			url: core.js.base.controllers.ActionProvider.get("media_playlist_list").url,
			content: {
				q: q
			},
			load: function(data) {
				_this._playlistListView.setContent(data);
			}
		});
	},
	
	updatePlaylistPoster: function(/*media.js.views.PlaylistItemView*/ playlistItemView, /*Object*/ thumbnails) {
		// summary:
		//		Updates the playlist's poster. Called after dropping an image from 
		// 		the Image Editor toolbox
		this._helper.showStandby();
		var _this = this;
		dojo.xhrPost({
			url: core.js.base.controllers.ActionProvider.get("media_playlist_cover").url,
			content: {
				playlist_id: playlistItemView.getPlaylist().playlist_id,
				thumbnails: dojo.toJson(thumbnails)
			},
			handleAs: "json",
			load: function(data) {
				_this._helper.hideStandby();
				if (data.result == "APP_RESULT_OK") {
					dojo.publish("/app/global/notification", [{ message: _this._i18n.playlist.cover.success }]);
					playlistItemView.updatePoster(data.thumbnails);
				}
			}
		});
	},
	
	////////// MANAGE VIDEOS //////////
	
	// _videoToolbar: media.js.views.VideoToolbar
	_videoToolbar: null,

	// _videoListView: media.js.views.VideoListView
	_videoListView: null,

	// _videoContextMenu: media.js.views.VideoContextMenu
	_videoContextMenu: null,
	
	// _videoSearchCriteria: Object
	_videoSearchCriteria: {
		playlist_id: null,
		status: null,
		page: 1,
		view_size: "thumbnail",
		per_page: 20,
		language: null
	},

	setVideoToolbar: function(/*media.js.views.VideoToolbar*/ videoToolbar) {
		// summary:
		//		Sets video toolbar
		this._videoToolbar = videoToolbar;
		this._mediator.setVideoToolbar(videoToolbar);
		
		// Add new video handler
		dojo.connect(videoToolbar, "onAddVideo", this, "addVideo");
		core.js.base.controllers.Subscriber.subscribe(this.TOPIC_GROUP, "/app/media/video/add/onCancel", this, function() {
			this._helper.removePane();
		});
		core.js.base.controllers.Subscriber.subscribe(this.TOPIC_GROUP, "/app/media/video/add/onComplete", this, function(data) {
			this._helper.removePane();
			dojo.publish("/app/global/notification", [{
				message: this._i18n.video.add[(data.result == "APP_RESULT_OK") ? "success" : "error"],
				type: (data.result == "APP_RESULT_OK") ? "message" : "error"
			}]);
			
			if (data.result == "APP_RESULT_OK") {
				var playlistItemView = this._playlistListView.getPlaylistItemView(data.playlist_id);
				if (playlistItemView) {
					this._playlistListView.setSelectedPlaylistItemView(playlistItemView);
					
					// Update the number of videos in the playlist
					playlistItemView.increaseVideoCounter(1);
				}
				
				// Show the videos in the playlist
				this.searchVideos({
					page: 1,
					playlist_id: data.playlist_id
				});
			}
		});
		
		// Refresh handler
		dojo.connect(videoToolbar, "onRefresh", this, "searchVideos");
		
		// Save order of videos in playlist
		dojo.connect(videoToolbar, "onSaveOrder", this, "saveVideoOrder");
		
		// Search by title handler
		dojo.connect(videoToolbar, "onSearchVideos", this, function(title) {
			this.searchVideos({
				title: title,
				page: 1
			});
		});
		
		// Update page size handler
		dojo.connect(videoToolbar, "onUpdatePageSize", this, function(perPage) {
			if (this._videoSearchCriteria.per_page != perPage) {
				this.searchVideos({
					page: 1,
					per_page: perPage
				});
			}
		});
		
		// View in various size handler
		dojo.connect(videoToolbar, "onViewSize", this, function(size) {
			this._videoSearchCriteria.view_size = size;
			if (this._videoListView) {
				this._videoListView.setViewSize(size);
			}
		});
		
		return this;	// media.js.controllers.VideoController
	},

	setVideoListView: function(/*media.js.views.VideoListView*/ videoListView) {
		// summary:
		//		Sets video list view
		this._videoListView = videoListView;
		this._mediator.setVideoListView(videoListView);
		
		// Show the context menu
		dojo.connect(videoListView, "onMouseDown", this, function(videoItemView) {
			if (this._videoContextMenu) {
				this._videoContextMenu.show(videoItemView);
			}
		});
		
		// Paging handler
		core.js.base.controllers.Subscriber.subscribe(this.TOPIC_GROUP, "/app/media/video/list/onGotoPage", this, function(page) {
			this.searchVideos({
				page: page
			});
		});
		
		// Update poster handler
		dojo.connect(videoListView, "onUpdatePoster", this, "updatePoster");
		
		return this;	// media.js.controllers.VideoController
	},

	setVideoContextMenu: function(/*media.js.views.VideoContextMenu*/ videoContextMenu) {
		// summary:
		//		Sets video context menu
		this._videoContextMenu = videoContextMenu;
		this._mediator.setVideoContextMenu(videoContextMenu);
		
		// Activate handler
		dojo.connect(videoContextMenu, "onActivateVideo", this, "activateVideo");
		
		// Edit video handler
		dojo.connect(videoContextMenu, "onUpdateVideo", this, "updateVideo");
		core.js.base.controllers.Subscriber.subscribe(this.TOPIC_GROUP, "/app/media/video/update/onCancel", this, function() {
			this._helper.removePane();
		});
		core.js.base.controllers.Subscriber.subscribe(this.TOPIC_GROUP, "/app/media/video/update/onStart", this, function() {
			this._helper.showStandby();
		});
		core.js.base.controllers.Subscriber.subscribe(this.TOPIC_GROUP, "/app/media/video/update/onComplete", this, function(data) {
			this._helper.hideStandby();
			this._helper.removePane();
			dojo.publish("/app/global/notification", [{
				message: this._i18n.video.update[(data.result == "APP_RESULT_OK") ? "success" : "error"],
				type: (data.result == "APP_RESULT_OK") ? "message" : "error"
			}]);
			
			if (data.result == "APP_RESULT_OK") {
				var videoItemView = this._videoListView.getVideoItemView(data.video_id);
				if (videoItemView) {
					videoItemView.getVideo().url		= data.url;
					videoItemView.getVideo().embed_code = data.embed_code;
					videoItemView.updateTitle(data.title, data.short_title);
					if (data.thumbnails) {
						videoItemView.updatePosterThumbnails(data.thumbnails);
					}
				}
			}
		});
		
		// Delete video handler
		dojo.connect(videoContextMenu, "onDeleteVideo", this, "deleteVideo");
		core.js.base.controllers.Subscriber.subscribe(this.TOPIC_GROUP, "/app/media/video/delete/onComplete", this, function(data) {
			dojo.publish("/app/global/notification", [{
				message: this._i18n.video["delete"][(data.result == "APP_RESULT_OK") ? "success" : "error"],
				type: (data.result == "APP_RESULT_OK") ? "message" : "error"
			}]);
			this.searchPlaylists();
			this.searchVideos();
		});
		core.js.base.controllers.Subscriber.subscribe(this.TOPIC_GROUP, "/app/media/video/delete/onCancel", this, function() {
			this._helper.closeDialog();
		});
		
		// Rename video handler
		dojo.connect(videoContextMenu, "onRenameVideo", this, "renameVideo");
		core.js.base.controllers.Subscriber.subscribe(this.TOPIC_GROUP, "/app/media/video/rename/onCancel", this, function() {
			this._helper.closeDialog();
		});
		core.js.base.controllers.Subscriber.subscribe(this.TOPIC_GROUP, "/app/media/video/rename/onComplete", this, function(data) {
			this._helper.closeDialog();
			dojo.publish("/app/global/notification", [{
				message: this._i18n.video.rename[(data.result == "APP_RESULT_OK") ? "success" : "error"],
				type: (data.result == "APP_RESULT_OK") ? "message" : "error"
			}]);
			
			if (data.result == "APP_RESULT_OK") {
				var videoItemView = this._videoListView.getVideoItemView(data.video_id);
				if (videoItemView) {
					videoItemView.updateTitle(data.title, data.short_title);
				}
			}
		});
		
		// Set poster handler
		dojo.connect(videoContextMenu, "onSetPoster", this, "setPlaylistPoster");
		
		// Remove from playlist handler
		dojo.connect(videoContextMenu, "onRemoveFromPlaylist", this, "removeVideo");
		
		// Download handler
		dojo.connect(videoContextMenu, "onDownloadVideo", this, "downloadVideo");
		
		return this;	// media.js.controllers.VideoController
	},
	
	activateVideo: function(/*media.js.views.VideoItemView*/ videoItemView) {
		// summary:
		//		Activates/deactivates given video
		var status = videoItemView.getVideo().status;
		var _this  = this;
		dojo.xhrPost({
			url: core.js.base.controllers.ActionProvider.get("media_video_activate").url,
			content: {
				video_id: videoItemView.getVideo().video_id
			},
			handleAs: "json",
			load: function(data) {
				var message = (data.result == "APP_RESULT_OK") 
							  ? (status == "activated" ? "deactivateSuccess" : "activateSuccess") 
							  : (status == "activated" ? "deactivateError" : "activateError");
				dojo.publish("/app/global/notification", [{
					message: _this._i18n.video.activate[message],
					type: (data.result == "APP_RESULT_OK") ? "message" : "error"
				}]);
				
				if (data.result == "APP_RESULT_OK") {
					videoItemView.getVideo().status = (status == "activated") ? "not_activated" : "activated";
				}
			}
		});
	},
	
	addVideo: function(/*String*/ playlistId) {
		// summary:
		//		Adds new video
		var params = {
			language: this._videoSearchCriteria.language
		};
		if (playlistId) {
			params.playlist_id = playlistId;
		} else if (this._videoSearchCriteria.playlist_id) {
			params.playlist_id = this._videoSearchCriteria.playlist_id;
		}
		var url = core.js.base.controllers.ActionProvider.get("media_video_add").url + "?" + dojo.objectToQuery(params);
		this._helper.showPane(url);
	},
	
	deleteVideo: function(/*media.js.views.VideoItemView*/ videoItemView) {
		// summary:
		//		Deletes video
		var params = {
			video_id: videoItemView.getVideo().video_id
		};
		var url = core.js.base.controllers.ActionProvider.get("media_video_delete").url + "?" + dojo.objectToQuery(params);
		this._helper.showDialog(url, {
			title: this._i18n.video["delete"].title,
			style: "width: 250px",
			refreshOnShow: true
		});
	},
	
	downloadVideo: function(/*media.js.views.VideoItemView*/ videoItemView) {
		// summary:
		//		Downloads video
		var videoId = videoItemView.getVideo().video_id;
		dojo.io.iframe.send({
			url: core.js.base.controllers.ActionProvider.get("media_video_download").url,
			method: "GET",
			content: {
				video_id: videoId
			}
		});
	},
	
	initSearchCriteria: function(/*Object*/ criteria) {
		// summary:
		//		Inits the controls with given criteria
		dojo.mixin(this._videoSearchCriteria, criteria);
		this._playlistSearchCriteria.language			= criteria.language;
		this._playlistSearchCriteria.active_playlist_id = criteria.playlist_id;
		this._mediator.initVideoSearchCriteria(criteria);
	},
	
	removeVideo: function(/*media.js.views.VideoItemView*/ videoItemView) {
		// summary:
		//		Removes a video from the selected playlist
		if (!this._videoSearchCriteria.playlist_id) {
			return;
		}
		this._helper.showStandby();
		var _this = this, playlistId = this._videoSearchCriteria.playlist_id;
		dojo.xhrPost({
			url: core.js.base.controllers.ActionProvider.get("media_video_remove").url,
			content: {
				playlist_id: playlistId,
				video_id: videoItemView.getVideo().video_id
			},
			handleAs: "json",
			load: function(data) {
				_this._helper.hideStandby();
				dojo.publish("/app/global/notification", [{
					message: _this._i18n.video.remove[(data.result == "APP_RESULT_OK") ? "success" : "error"],
					type: (data.result == "APP_RESULT_OK") ? "message" : "error"
				}]);
				
				if (data.result == "APP_RESULT_OK") {
					_this._videoListView.removeVideoItemView(videoItemView);
					_this._videoListView.increaseVideoCounter(-1);
					var playlistItemView = _this._playlistListView.getPlaylistItemView(playlistId);
					if (playlistItemView) {
						playlistItemView.increaseVideoCounter(-1);
					}
				}
			}
		});
	},
	
	renameVideo: function(/*media.js.views.VideoItemView*/ videoItemView) {
		// summary:
		//		Renames given video
		var params = {
			video_id: videoItemView.getVideo().video_id
		};
		var url = core.js.base.controllers.ActionProvider.get("media_video_rename").url + "?" + dojo.objectToQuery(params);
		this._helper.showDialog(url, {
			title: this._i18n.video.rename.title,
			style: "width: 400px",
			refreshOnShow: true
		});
	},
	
	saveVideoOrder: function() {
		// summary:
		//		Saves the order of videos in the selected playlist
		var playlistItemView  = this._playlistListView.getSelectedPlaylistItemView();
		if (!playlistItemView) {
			return;
		}
		var videoItemViews = this._videoListView.getVideoItemViews(),
			data = [],
			startIndex = this._videoSearchCriteria.per_page * (this._videoSearchCriteria.page - 1) + 1;  
		for (var i = 0; i < videoItemViews.length; i++) {
			data.push({
				video_id: videoItemViews[i].getVideo().video_id,
				playlist_id: playlistItemView.getPlaylist().playlist_id,
				index: startIndex + i
			});
		}
		
		this._helper.showStandby();
		var _this = this;
		dojo.xhrPost({
			url: core.js.base.controllers.ActionProvider.get("media_video_order").url,
			content: {
				data: dojo.toJson(data)
			},
			handleAs: "json",
			load: function(data) {
				_this._helper.hideStandby();
				dojo.publish("/app/global/notification", [{
					message: _this._i18n.video.order[(data.result == "APP_RESULT_OK") ? "success" : "error"],
					type: (data.result == "APP_RESULT_OK") ? "message" : "error"
				}]);
			}
		});
	},
	
	searchVideos: function(/*Object*/ criteria) {
		// summary:
		//		Searches for videos
		var _this = this;
		this._helper.closeDialog();
		
		dojo.mixin(this._videoSearchCriteria, criteria);
		var q   = core.js.base.Encoder.encode(this._videoSearchCriteria);
		var url = core.js.base.controllers.ActionProvider.get("media_video_list").url;
		dojo.hash("u=" + url + "/?q=" + q);
		
		this._helper.showStandby();
		dojo.xhrPost({
			url: url,
			content: {
				q: q,
				format: "html"
			},
			load: function(data) {
				_this._videoListView.setContent(data);
				_this._helper.hideStandby();
			}
		});
	},
	
	setPlaylistPoster: function(/*media.js.views.VideoItemView*/ videoItemView) {
		// summary:
		//		Sets the playlist poster
		if (!this._videoSearchCriteria.playlist_id) {
			return;
		}
		this._helper.showStandby();
		var _this = this, playlistId = this._videoSearchCriteria.playlist_id;
		dojo.xhrPost({
			url: core.js.base.controllers.ActionProvider.get("media_playlist_cover").url,
			content: {
				playlist_id: playlistId,
				video_id: videoItemView.getVideo().video_id
			},
			handleAs: "json",
			load: function(data) {
				_this._helper.hideStandby();
				dojo.publish("/app/global/notification", [{
					message: _this._i18n.playlist.cover[(data.result == "APP_RESULT_OK") ? "success" : "error"],
					type: (data.result == "APP_RESULT_OK") ? "message" : "error"
				}]);
				
				if (data.result == "APP_RESULT_OK") {
					var playlistItemView = _this._playlistListView.getPlaylistItemView(playlistId);
					if (playlistItemView) {
						playlistItemView.updatePoster(data.thumbnails);
					}
				}
			}
		});
	},
	
	updatePoster: function(/*media.js.views.VideoItemView*/ videoItemView, /*Object*/ thumbnails) {
		// summary:
		//		Updates video's poster
		var videoId = videoItemView.getVideo().video_id;
		
		var _this = this;
		this._helper.showStandby();
		dojo.xhrPost({
			url: core.js.base.controllers.ActionProvider.get("media_video_cover").url,
			content: {
				video_id: videoId,
				thumbnails: dojo.toJson(thumbnails)
			},
			handleAs: "json",
			load: function(data) {
				_this._helper.hideStandby();
				dojo.publish("/app/global/notification", [{
					message: _this._i18n.video.cover[(data.result == "APP_RESULT_OK") ? "success" : "error"],
					type: (data.result == "APP_RESULT_OK") ? "message" : "error"
				}]);
				
				if (data.result == "APP_RESULT_OK") {
					videoItemView.updatePosterThumbnails(thumbnails);
				}
			}
		});
	},
	
	updateVideo: function(/*media.js.views.VideoItemView*/ videoItemView) {
		// summary:
		//		Updates given video
		var params = {
			video_id: videoItemView.getVideo().video_id
		};
		var url = core.js.base.controllers.ActionProvider.get("media_video_update").url + "?" + dojo.objectToQuery(params);
		this._helper.showPane(url);
	},
	
	dropVideos: function(/*media.js.views.PlaylistItemView*/ playlistItemView, /*DomNode[]*/ videoNodes) {
		// summary:
		//		This callback is called when dragging the video items and dropping them on the playlist
		var newPlaylistId = playlistItemView.getPlaylist().playlist_id;
		var _this = this;
		this._helper.showStandby();
		while (videoNodes.length > 0) {
			var videoNode = videoNodes[0];
			var video = core.js.base.Encoder.decode(dojo.attr(videoNode, "data-app-entity-props"));
			
			dojo.xhrPost({
				url: core.js.base.controllers.ActionProvider.get("media_video_copy").url,
				content: {
					playlist_id: newPlaylistId,
					video_id: video.video_id
				},
				handleAs: "json",
				load: function(data) {
				}
			});
			
			videoNodes.splice(0, 1);
		}
		
		if (videoNodes.length == 0) {
			this._helper.hideStandby();
			// Reload the list of playlists to update the video counters
			this.searchPlaylists();
		}
	}
});
