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

dojo.provide("media.js.views.VideoContextMenu");

dojo.require("dijit.Menu");
dojo.require("dijit.MenuItem");
dojo.require("dijit.MenuSeparator");
dojo.require("dojox.string.sprintf");

dojo.require("core.js.base.controllers.ActionProvider");
dojo.require("core.js.base.I18N");

dojo.declare("media.js.views.VideoContextMenu", null, {
	// _contextMenu: dijit.Menu
	_contextMenu: null,
	
	// _i18n: Object
	_i18n: null,
	
	// _posterMenuSeparator: dijit.MenuSeparator
	_posterMenuSeparator: null,
	
	// _setPosterMenuItem: dijit.MenuItem
	_setPosterMenuItem: null,
	
	// _removeMenuItem: dijit.MenuItem
	_removeMenuItem: null,
	
	// _downloadMenuItem: dijit.MenuItem
	_downloadMenuItem: null,
	
	// _activateMenuItem: dijit.MenuItem
	_activateMenuItem: null,
	
	constructor: function() {
		core.js.base.I18N.requireLocalization("media/languages");
		this._i18n = core.js.base.I18N.getLocalization("media/languages");
	},
	
	show: function(/*media.js.views.VideoItemView*/ videoItemView) {
		// summary:
		//		Show menu context for selected video item
		// videoItemView:
		//		The selected video item
		var _this = this;
		
		// Create menu
		this._contextMenu = new dijit.Menu({
			targetNodeIds: [ dojo.attr(videoItemView.getDomNode(), "id") ]
		});
		
		var video = videoItemView.getVideo();
		
		// "Activate" menu item
		this._activateMenuItem = new dijit.MenuItem({
			label: (video.status == "activated") ? this._i18n.global._share.deactivateAction : this._i18n.global._share.activateAction,
			iconClass: "appIcon " + (video.status == "activated" ? "appDeactivateIcon" : "appActivateIcon"),
			disabled: !core.js.base.controllers.ActionProvider.get("media_video_activate").isAllowed,
			onClick: function() {
				_this.onActivateVideo(videoItemView);
			}
		});
		this._contextMenu.addChild(this._activateMenuItem);
		
		// Update information item
		this._contextMenu.addChild(new dijit.MenuItem({
			label: this._i18n.global._share.editAction,
			disabled: !core.js.base.controllers.ActionProvider.get("media_video_update").isAllowed,
			onClick: function() {
				_this.onUpdateVideo(videoItemView);
			}
		}));
		
		// Delete item
		this._contextMenu.addChild(new dijit.MenuItem({
			label: this._i18n.global._share.deleteAction,
			iconClass: "appIcon appDeleteIcon",
			disabled: !core.js.base.controllers.ActionProvider.get("media_video_delete").isAllowed,
			onClick: function() {
				_this.onDeleteVideo(videoItemView);
			}
		}));
		
		// Rename item
		this._contextMenu.addChild(new dijit.MenuItem({
			label: this._i18n.global._share.renameAction,
			iconClass: "appIcon appRenameIcon",
			disabled: !core.js.base.controllers.ActionProvider.get("media_video_rename").isAllowed,
			onClick: function() {
				_this.onRenameVideo(videoItemView);
			}
		}));
		
		this._posterMenuSeparator = new dijit.MenuSeparator();
		this._contextMenu.addChild(this._posterMenuSeparator);
		
		// Set poster menu item
		this._setPosterMenuItem = new dijit.MenuItem({
			label: this._i18n.playlist._share.setPosterAction,
			disabled: !core.js.base.controllers.ActionProvider.get("media_playlist_cover").isAllowed,
			onClick: function() {
				_this.onSetPoster(videoItemView);
			}
		});
		this._contextMenu.addChild(this._setPosterMenuItem);
		
		// Remove video from playlist
		this._removeMenuItem = new dijit.MenuItem({
			label: this._i18n.video._share.removeAction,
			disabled: !core.js.base.controllers.ActionProvider.get("media_video_remove").isAllowed,
			onClick: function() {
				_this.onRemoveFromPlaylist(videoItemView);
			}
		});
		this._contextMenu.addChild(this._removeMenuItem);
		
		this._contextMenu.addChild(new dijit.MenuSeparator());
		
		// Play item
		this._contextMenu.addChild(new dijit.MenuItem({
			label: this._i18n.video._share.playAction,
			onClick: function() {
				videoItemView.playVideo();
			}
		}));
		
		// Download item
		this._downloadMenuItem = new dijit.MenuItem({
			label: this._i18n.global._share.downloadAction,
			iconClass: "appIcon appDownloadIcon",
			disabled: !core.js.base.controllers.ActionProvider.get("media_video_download").isAllowed,
			onClick: function() {
				_this.onDownloadVideo(videoItemView);
			}
		});
		this._contextMenu.addChild(this._downloadMenuItem);
		this._contextMenu.startup();
		
		dojo.connect(this._contextMenu, "_openMyself", this, function() {
			this.onContextMenu(videoItemView);
		});
	},
	
	////////// CONTROL ITEM STATE //////////
	
	allowToDownload: function(/*Boolean*/ isAllowed) {
		// summary:
		//		Allows/disallows to download the video
		isAllowed = isAllowed && core.js.base.controllers.ActionProvider.get("media_video_download").isAllowed;
		this._downloadMenuItem.set("disabled", !isAllowed);
		return this;	// media.js.views.VideoContextMenu
	},
	
	allowToRemove: function(/*Boolean*/ isAllowed) {
		// summary:
		//		Allows/disallows to remove video from playlist
		isAllowed = isAllowed && core.js.base.controllers.ActionProvider.get("media_video_remove").isAllowed;
		this._removeMenuItem.set("disabled", !isAllowed);
		return this;	// media.js.views.VideoContextMenu
	},
	
	allowToSetPoster: function(/*Boolean*/ isAllowed, /*media.js.views.PlaylistItemView*/ playlistItemView) {
		// summary:
		//		Allows/disallows to set a video as poster of playlist
		isAllowed = isAllowed && core.js.base.controllers.ActionProvider.get("media_playlist_cover").isAllowed;
		this._setPosterMenuItem.set("disabled", !isAllowed);
		return this;	// media.js.views.VideoContextMenu
	},
	
	////////// CALLBACKS //////////
	
	onActivateVideo: function(/*media.js.views.VideoItemView*/ videoItemView) {
		// summary:
		//		Activates/deactivates video
		// videoItemView:
		//		The video item
		// tags:
		//		callback
	},
	
	onContextMenu: function(/*media.js.views.VideoItemView*/ videoItemView) {
		// summary:
		//		Called after right-clicking the video item
		// videoItemView:
		//		The video item
		// tags:
		//		callback
	},
	
	onDeleteVideo: function(/*media.js.views.VideoItemView*/ videoItemView) {
		// summary:
		//		Deletes video
		// videoItemView:
		//		The video item
		// tags:
		//		callback
	},
	
	onDownloadVideo: function(/*media.js.views.VideoItemView*/ videoItemView) {
		// summary:
		//		Downloads video
		// videoItemView:
		//		The video item
		// tags:
		//		callback
	},
	
	onRemoveFromPlaylist: function(/*media.js.views.VideoItemView*/ videoItemView) {
		// summary:
		//		Removes video from playlist
		// videoItemView:
		//		The video item
		// tags:
		//		callback
	},
	
	onRenameVideo: function(/*media.js.views.VideoItemView*/ videoItemView) {
		// summary:
		//		Renames video
		// videoItemView:
		//		The video item
		// tags:
		//		callback
	},
	
	onSetPoster: function(/*media.js.views.VideoItemView*/ videoItemView) {
		// summary:
		//		Sets poster to the playlist
		// videoItemView:
		//		The video item
		// tags:
		//		callback
	},
	
	onUpdateVideo: function(/*media.js.views.VideoItemView*/ videoItemView) {
		// summary:
		//		Updates video's information
		// videoItemView:
		//		The video item
		// tags:
		//		callback
	}
});
