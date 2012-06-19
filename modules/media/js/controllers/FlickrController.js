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
 * @version		2012-05-16
 */

dojo.provide("media.js.controllers.FlickrController");

dojo.require("dojo.NodeList-traverse");

dojo.require("core.js.base.controllers.ActionProvider");
dojo.require("core.js.base.controllers.Subscriber");
dojo.require("core.js.base.Encoder");
dojo.require("core.js.base.I18N");
dojo.require("media.js.controllers.FlickrMediator");

dojo.declare("media.js.controllers.FlickrController", null, {
	// _id: String
	_id: null,
	
	// _i18n: Object
	_i18n: null,
	
	// _helper: core.js.base.views.Helper
	_helper: null,
	
	// _setToolbar: media.js.views.FlickrSetToolbar
	_setToolbar: null,
	
	// _setsContainer: String
	_setsContainer: null,
	
	// _photoToolbar: media.js.views.FlickrPhotoToolbar
	_photoToolbar: null,
	
	// _photosContainer: String
	_photosContainer: null,
	
	// _importedContainer: String
	_importedContainer: null,
	
	// _toolbar: media.js.views.FlickrToolbar
	_toolbar: null,
	
	// _photos: Array
	//		Array of imported photos
	_photos: {},
	
	// _photoIds: Array
	//		Array of imported photo Ids
	_photoIds: [],
	
	// _currentSetId: String
	//		Current selected set Id
	_currentSetId: null,
	
	// _mediator: media.js.controllers.FlickrMediator
	_mediator: new media.js.controllers.FlickrMediator(),
	
	// TOPIC_GROUP: [const] String
	TOPIC_GROUP: "/media/js/controllers/FlickrController",
	
	constructor: function(/*String*/ id) {
		this._id = id;
		
		core.js.base.I18N.requireLocalization("media/languages");
		this._i18n = core.js.base.I18N.getLocalization("media/languages");
		
		// Create helper instance
		this._helper = new core.js.base.views.Helper(id);
		this._helper.setLanguageData(this._i18n);
		
		core.js.base.controllers.Subscriber.unsubscribe(this.TOPIC_GROUP);
	},
	
	setSetToolbar: function(/*media.js.views.FlickrSetToolbar*/ setToolbar) {
		// summary:
		//		Sets the set toolbar
		this._setToolbar = setToolbar;
		
		// Reload sets handler
		dojo.connect(setToolbar, "onRefresh", this, "searchSets");
		
		return this;	// media.js.controllers.FlickrController
	},
	
	setSetsContainer: function(/*String*/ container) {
		// summary:
		//		Sets the container listing Flickr sets
		this._setsContainer = container;
		
		core.js.base.controllers.Subscriber.subscribe(this.TOPIC_GROUP, "/app/js/controllers/FlickrController/onSelectSet", this, function(setId) {
			dojo.query(".mediaFlickrSetItem").removeClass("mediaFlickrSetSelectedItem");
			dojo.query(".mediaFlickrSetItem[data-app-set='" + setId + "']").addClass("mediaFlickrSetSelectedItem");
		});
		
		return this;	// media.js.controllers.FlickrController
	},
	
	setPhotoToolbar: function(/*media.js.views.FlickrPhotoToolbar*/ photoToolbar) {
		// summary:
		//		Sets the photo toolbar
		this._photoToolbar = photoToolbar;
		this._mediator.setPhotoToolbar(photoToolbar);
		
		// Reload photos handler
		dojo.connect(photoToolbar, "onRefresh", this, function() {
			if (this._currentSetId) {
				this.searchPhotos(this._currentSetId);
			}
		});
		
		return this;	// media.js.controllers.FlickrController
	},
	
	setPhotosContainer: function(/*String*/ container) {
		// summary:
		//		Sets the container listing Flickr photos
		this._photosContainer = container;
		return this;	// media.js.controllers.FlickrController
	},
	
	setToolbar: function(/*media.js.views.FlickrToolbar*/ toolbar) {
		// summary:
		//		Sets the main toolbar
		this._toolbar = toolbar;
		this._mediator.setToolbar(toolbar);
		
		// Import handler
		dojo.connect(toolbar, "onImport", this, "import");
		
		return this;	// media.js.controllers.FlickrController
	},
	
	setImportedContainer: function(/*String*/ container) {
		// summary:
		//		Sets the container containing the imported photos
		this._importedContainer = container;
		return this;	// media.js.controllers.FlickrController
	},
	
	import: function() {
		// summary:
		//		Imports photos
		var _this = this;
		var params = {
			format: "json",
			photos: dojo.toJson(this._photos)
		};
		this._helper.showStandby();
		dojo.xhrPost({
			url: core.js.base.controllers.ActionProvider.get("media_flickr_import").url,
			content: params,
			handleAs: "json",
			load: function(data) {
				_this._helper.hideStandby();
				dojo.publish("/app/global/notification", [{
					message: _this._i18n.flickr.import[(data.result == "APP_RESULT_OK") ? "success" : "error"],
					type: (data.result == "APP_RESULT_OK") ? "message" : "error"
				}]);
				
				if (data.result == "APP_RESULT_OK") {
					dojo.publish("/app/media/flickr/import/onSuccess", [ data ]);
					
					_this._photos = {};
					
					// Add the id of imported photos to the array
					for (var id in _this._photos) {
						_this._photoIds.push(id);
					}
					// Remove all the minus (-) icons in the imported container
					dojo.query(".mediaFlickrAddRemoveIcon", _this._importedContainer).orphan();
				}
			}
		});
	},
	
	searchPhotos: function(/*String*/ setId) {
		// summary:
		//		Searches for photos of given set
		var _this = this;
		dojo.connect(dijit.byId(this._photosContainer), "onDownloadEnd", this, function() {
			dojo.query(".mediaFlickrPhotoItem", this._photosContainer).forEach(function(node) {
				var photo = core.js.base.Encoder.decode(dojo.attr(node, "data-app-entity-props"));
				if (dojo.indexOf(_this._photoIds, photo.id) == -1 && !_this._photos[photo.id]) {
					dojo.query(".mediaFlickrAddRemoveIcon", node).orphan();
					
					// Show a plus icon for adding the photo to list of imported photos
					var icon = dojo.create("span", {}, node);
					dojo.addClass(icon, ["appIcon", "appAddIcon", "mediaFlickrAddRemoveIcon"]);
					dojo.connect(icon, "onclick", _this, function() {
						_this._addPhoto(node, photo);
					});
				} else {
					dojo.query(node).orphan();
				}
			});
		});
		
		var params = {
			set_id: setId
		};
		dijit.byId(this._photosContainer).set("href", core.js.base.controllers.ActionProvider.get("media_flickr_photo").url + "?" + dojo.objectToQuery(params));
	},
	
	searchSets: function() {
		// summary:
		//		Searches for sets
		var _this = this;
		dojo.connect(dijit.byId(this._setsContainer), "onDownloadEnd", this, function() {
			dojo.query(".mediaFlickrSetItem", this._setsContainer).forEach(function(node) {
				var setId = dojo.attr(node, "data-app-set");
				var img = dojo.query("img", node)[0];
				dojo.connect(img, "onclick", _this, function() {
					_this._currentSetId = setId;
					
					dojo.publish("/app/js/controllers/FlickrController/onSelectSet", [ setId ]);
					
					// Load the photos of selected set
					_this.searchPhotos(setId);
				});
			});
		});
		
		dijit.byId(this._setsContainer).set("href", core.js.base.controllers.ActionProvider.get("media_flickr_set").url);
	},
	
	_addPhoto: function(/*DomNode*/ photoNode, /*Object*/ photo) {
		// summary:
		//		Adds photo to list of imported photos
		if (this._photos[photo.id]) {
			// The photo is already added
			return;
		}
		this._mediator.addPhoto(photo);
		
		this._photos[photo.id] = photo;
		
		// Clone node
		var node = dojo.clone(photoNode);
		dojo.place(node, this._importedContainer);

		dojo.query(photoNode).orphan();
		
		// Turn the plus icon (+) to the minus (-) icon
		var icon = dojo.query(".mediaFlickrAddRemoveIcon", node)[0];
		dojo.removeClass(icon, "appAddIcon");
		dojo.addClass(icon, "appDeleteIcon");
		dojo.connect(icon, "onclick", this, function() {
			this._removePhoto(node, photo);
		});
	},
	
	_removePhoto: function(/*DomNode*/ photoNode, /*Object*/ photo) {
		// summary:
		//		Removes photo from the list of imported photos
		if (!this._photos[photo.id]) {
			return;
		}
		delete this._photos[photo.id];
		this._mediator.removePhoto(photo);
		
		var node = dojo.clone(photoNode);
		dojo.place(node, this._photosContainer);
		
		dojo.query(photoNode).orphan();
		
		// Turn the minus icon (-) to the plus (+) icon
		var icon = dojo.query(".mediaFlickrAddRemoveIcon", node)[0];
		dojo.removeClass(icon, "appDeleteIcon");
		dojo.addClass(icon, "appAddIcon");
		dojo.connect(icon, "onclick", this, function() {
			this._addPhoto(node, photo);
		});
	}
});
