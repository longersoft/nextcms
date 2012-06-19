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

dojo.provide("media.js.controllers.FlickrMediator");

dojo.require("core.js.base.controllers.Subscriber");

dojo.declare("media.js.controllers.FlickrMediator", null, {
	// _photoToolbar: media.js.views.FlickrPhotoToolbar
	_photoToolbar: null,
	
	// _toolbar: media.js.views.FlickrToolbar
	_toolbar: null,
	
	// _photoIds: Array
	//		Array of imported photo Ids
	_photoIds: [],
	
	// TOPIC_GROUP: [const] String
	TOPIC_GROUP: "/media/js/controllers/FlickrMediator",
	
	constructor: function() {
		core.js.base.controllers.Subscriber.unsubscribe(this.TOPIC_GROUP);
	},
	
	setPhotoToolbar: function(/*media.js.views.FlickrPhotoToolbar*/ photoToolbar) {
		// summary:
		//		Sets the photo toolbar
		this._photoToolbar = photoToolbar;
		
		// Disable the refresh button
		this._photoToolbar.allowToRefresh(false);
		
		core.js.base.controllers.Subscriber.subscribe(this.TOPIC_GROUP, "/app/js/controllers/FlickrController/onSelectSet", this, function(setId) {
			this._photoToolbar.allowToRefresh(true);
		});
	},
	
	setToolbar: function(/*media.js.views.FlickrToolbar*/ toolbar) {
		// summary:
		//		Sets the main toolbar
		this._toolbar = toolbar;
		
		// Disable the import button
		this._toolbar.allowToImport(false);
		
		core.js.base.controllers.Subscriber.subscribe(this.TOPIC_GROUP, "/app/media/flickr/import/onSuccess", this, function(data) {
			this._photoIds = [];
			this._toolbar.allowToImport(false);
		});
	},
	
	addPhoto: function(/*Object*/ photo) {
		// summary:
		//		Adds photo
		this._photoIds.push(photo.id);
		this._toolbar.allowToImport(this._photoIds.length > 0);
	},
	
	removePhoto: function(/*Object*/ photo) {
		// summary:
		//		Removes photo
		var index = dojo.indexOf(this._photoIds, photo.id);
		if (index > -1) {
			this._photoIds.splice(index, 1);
		}
		this._toolbar.allowToImport(this._photoIds.length > 0);
	}
});
