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

dojo.provide("media.js.views.VideoListView");

dojo.require("dojo.dnd.Source");

dojo.require("core.js.base.controllers.ActionProvider");
dojo.require("media.js.views.VideoItemView");

dojo.declare("media.js.views.VideoListView", null, {
	// _id: String
	_id: null,
	
	// _domNode: DomNode
	_domNode: null,
	
	// _videoItemMap: Object
	//		Map the video Id with video item
	_videoItemMap: {},
	
	// _viewSize: String
	//		The size of videos in the list
	//		Can be "square", "thumbnail", "small", "crop", "medium" 
	_viewSize: "thumbnail",
	
	constructor: function(/*String*/ id) {
		this._id	  = id;
		this._domNode = dojo.byId(id);
		this._init();
	},
	
	_init: function() {
		var _this = this;
		this._videoItemMap   = {};
		dojo.query(".mediaVideoItem", this._id).forEach(function(node, index, arr) {
			var videoItemView = new media.js.views.VideoItemView(node, _this);
			_this._videoItemMap[videoItemView.getVideo().video_id + ""] = videoItemView;
		});
		
		if (core.js.base.controllers.ActionProvider.get("media_video_copy").isAllowed
			|| core.js.base.controllers.ActionProvider.get("media_video_order").isAllowed) {
			var container = dojo.query(".mediaVideoItemsContainer", this._id)[0];
			if (container) {
				new dojo.dnd.AutoSource(container, {
					accept: [],
					selfAccept: false,
					selfCopy: false
				});
			}
		}
		
		if (core.js.base.controllers.ActionProvider.get("media_video_cover").isAllowed) {
			for (var videoId in this._videoItemMap) {
				(function() {
					var videoItemView = _this._videoItemMap[videoId + ""];
					var posterNode = dojo.query(".mediaVideoItemPoster", videoItemView.getDomNode())[0];
					new dojo.dnd.Target(posterNode, {
						accept: ["appDndImage"],
						onDropExternal: function(source, nodes, copy) {
							var thumbnails = dojo.attr(nodes[0], "data-app-dndthumbnails");
							_this.onUpdatePoster(videoItemView, dojo.fromJson(thumbnails));
						}
					});
				})();
			}
		}
		
		// Extension point
		this.onPopulateVideos();
	},
	
	getNumVideoItemViews: function() {
		// summary:
		//		Returns the number of video items in the list 
		return dojo.query(".mediaVideoItem", this._domNode).length;
	},
	
	getVideoItemView: function(/*String*/ videoId) {
		// summary:
		//		Gets a video item by given video's Id
		// videoId:
		//		Id of video
		return this._videoItemMap[videoId + ""];		// media.js.views.VideoItemView
	},
	
	getVideoItemViews: function() {
		// summary:
		//		Returns all the video item views in the order of DomNode
		var videoItemViews = [];
		var _this = this;
		dojo.query(".mediaVideoItem", this._id).forEach(function(node, index, arr) {
			videoItemViews.push(new media.js.views.VideoItemView(node, _this));
		});
		return videoItemViews;
	},

	increaseVideoCounter: function(/*Integer*/ increasingNumber) {
		// summary:
		//		Increases (or descreases) the number of videos in the list
		// increasingNumber:
		//		The number of videos that will be added to or removed from the list
		var nodes =  dojo.query(".mediaVideoListCounter", this._domNode);
		if (nodes.length > 0) {
			nodes[0].innerHTML = parseInt(nodes[0].innerHTML) + increasingNumber;
		}
	},
	
	removeVideoItemView: function(/*media.js.views.VideoItemView*/ videoItemView) {
		// summary:
		//		Removes a video item from the list
		delete this._videoItemMap[videoItemView.getVideo().video_id + ""];
		dojo.destroy(videoItemView.getDomNode());
	},
	
	setContent: function(/*String*/ html) {
		// summary:
		//		Reloads the entire list by HTML content
		// html:
		//		Entire HTML to show the list of videos
		dijit.byId(this._id).setContent(html);
		
		// Re-init
		this._init();
	},
	
	setViewSize: function(/*String*/ size) {
		// summary:
		//		Shows the videos in given size of thumbnail
		// size:
		//		The size of thumbnail, can be: square, thumbnail, small, crop, medium
		this._viewSize = size;
		for (var videoId in this._videoItemMap) {
			this._videoItemMap[videoId + ""].setViewSize(size);
		}
	},
	
	////////// CALLBACKS //////////
	
	onMouseDown: function(/*media.js.views.VideoItemView*/ videoItemView) {
		// summary:
		//		Called when user right-click a video item
		// videoItemView:
		//		The selected video item
		// tags:
		//		callback
	},
	
	onPopulateVideos: function() {
		// summary:
		//		Called after populating all videos
		// tags:
		//		callback
	},
	
	onUpdatePoster: function(/*media.js.views.VideoItemView*/ videoItemView, /*Object*/ thumbnails) {
		// tags:
		//		callback
	}
});
