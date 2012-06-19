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

dojo.provide("media.js.views.PhotoListView");

dojo.require("dojo.dnd.Source");
dojo.require("dojox.image.Lightbox");

dojo.require("core.js.base.controllers.ActionProvider");
dojo.require("media.js.views.PhotoItemView");

dojo.declare("media.js.views.PhotoListView", null, {
	// _id: String
	_id: null,
	
	// _domNode: DomNode
	_domNode: null,
	
	// _photoItemMap: Object
	//		Map the photo Id with photo item
	_photoItemMap: {},
	
	// _viewSize: String
	//		The size of photos in the list
	//		Can be "square", "thumbnail", "small", "crop", "medium" 
	_viewSize: "thumbnail",
	
	constructor: function(/*String*/ id) {
		this._id	  = id;
		this._domNode = dojo.byId(id);
		this._init();
	},
	
	_init: function() {
		var _this = this;
		this._photoItemMap = {};
		dojo.query(".mediaPhotoItem", this._id).forEach(function(node, index, arr) {
			var photoItemView = new media.js.views.PhotoItemView(node, _this);
			
			_this._photoItemMap[photoItemView.getPhoto().photo_id + ""] = photoItemView;
		});
		
		if (core.js.base.controllers.ActionProvider.get("media_photo_copy").isAllowed
			|| core.js.base.controllers.ActionProvider.get("media_photo_order").isAllowed) {
			var container = dojo.query(".mediaPhotoItemsContainer", this._id)[0];
			if (container) {
				new dojo.dnd.AutoSource(container, {
					accept: [],
					selfAccept: false,
					selfCopy: false
				});
			}
		}
		
		// Extension point
		this.onPopulatePhotos();
	},
	
	getNumPhotoItemViews: function() {
		// summary:
		//		Returns the number of photo items in the list 
		return dojo.query(".mediaPhotoItem", this._domNode).length;
	},
	
	getPhotoItemView: function(/*String*/ photoId) {
		// summary:
		//		Get a photo item by given photo's Id
		// photoId:
		//		Id of photo
		return this._photoItemMap[photoId + ""];		// media.js.views.PhotoItemView
	},
	
	getPhotoItemViews: function() {
		// summary:
		//		Returns all the photo item views in the order of DomNode
		var photoItemViews = [];
		var _this = this;
		dojo.query(".mediaPhotoItem", this._id).forEach(function(node, index, arr) {
			photoItemViews.push(new media.js.views.PhotoItemView(node, _this));
		});
		return photoItemViews;
	},

	increasePhotoCounter: function(/*Integer*/ increasingNumber) {
		// summary:
		//		Increase (or descrease) the number of photos in the list
		// increasingNumber:
		//		The number of photos that will be added to or removed from the list
		var nodes =  dojo.query(".mediaPhotoListCounter", this._domNode);
		if (nodes.length > 0) {
			nodes[0].innerHTML = parseInt(nodes[0].innerHTML) + increasingNumber;
		}
	},
	
	removePhotoItemView: function(/*media.js.views.PhotoItemView*/ photoItemView) {
		// summary:
		//		Remove a photo item from the list. It should be called when deleting photos 
		//		or removing photos from the album.
		delete this._photoItemMap[photoItemView.getPhoto().photo_id + ""];
		dojo.destroy(photoItemView.getDomNode());
	},
	
	setContent: function(/*String*/ html) {
		// summary:
		//		Reload the entire list by HTML content
		// html:
		//		Entire HTML to show the list of photos
		dijit.byId(this._id).setContent(html);
		
		// Re-init
		this._init();
	},
	
	setViewSize: function(/*String*/ size) {
		// summary:
		//		Show the photos in given size of thumbnail
		// size:
		//		The size of thumbnail, can be: square, thumbnail, small, crop, medium
		this._viewSize = size;
		for (var photoId in this._photoItemMap) {
			this._photoItemMap[photoId + ""].setViewSize(size);
		}
	},
	
	showSlide: function(/*media.js.views.PhotoItemView*/ photoItemView) {
		// summary:
		//		Show a slide containing all the photos in the list
		// photoItemView:
		//		Show the slide starting at the photo item view.
		//		If this parameter is not passed, it will show the slide starting the last viewed item as usual.
		
		// DOJO LESSON: The dojox.image.Lightbox does not allow to pass the starting image, so I extend it
		dojo.declare("media.js.views.Lightbox", [dojox.image.Lightbox], {
			show: function(groupData) {
				(!groupData) ? this._attachedDialog.show(this) : this._attachedDialog.show(groupData);
			}
		});
		
		var lightbox = null,
			photo	 = null,
			// Generate an unique Id for group
			groupId	 	   = "media.js.views.PhotoListView_Lightbox_" + new Date().getTime(),
			photoItemViews = this.getPhotoItemViews();
		
		for (var i = 0; i < photoItemViews.length; i++) {
			photo = photoItemViews[i].getPhoto();
			if (i == 0) {
				// If you want to show the thumbnails in the size of current viewing, use photo[this._viewSize]
				lightbox = new media.js.views.Lightbox({
					title: photo.title,
					href: photo.large,
					group: groupId
				});
				lightbox.startup();
			} else {
				lightbox._attachedDialog.addImage({
					title: photo.title,
					href: photo.large
				}, groupId);
			}
		}
		
		!photoItemView ? lightbox.show() 
					   : lightbox.show({
						   title: photoItemView.getPhoto().title,
						   href: photoItemView.getPhoto().large,
						   group: groupId
					   });
	},
	
	////////// CALLBACKS //////////
	
	onMouseDown: function(/*media.js.views.PhotoItemView*/ photoItemView) {
		// summary:
		//		Called when user right-click a photo item
		// photoItemView:
		//		The selected photo item
		// tags:
		//		callback
	},
	
	onPopulatePhotos: function() {
		// summary:
		//		Called after populating all photos
		// tags:
		//		callback
	}
});
