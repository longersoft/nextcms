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
 * @version		2012-04-20
 */

dojo.provide("media.js.views.PhotoItemView");

dojo.require("core.js.base.Encoder");
dojo.require("core.js.Constant");

dojo.declare("media.js.views.PhotoItemView", null, {
	// _domNode: DomNode
	_domNode: null,
	
	// _photoTitleNode: DomNode
	_photoTitleNode: null,
	
	// _photoImageNode: DomNode
	_photoImageNode: null,
	
	// _photoListView: media.js.views.PhotoListView
	_photoListView: null,
	
	// _photo: Object
	// 		Represent photo data
	_photo: null,
	
	constructor: function(/*DomNode*/ domNode, /*media.js.views.PhotoListView*/ photoListView) {
		this._domNode		 = domNode;
		this._photoTitleNode = dojo.query(".mediaPhotoItemTitle", domNode)[0];
		this._photoImageNode = dojo.query("img.mediaPhotoImage", domNode)[0];
		this._photoListView  = photoListView;
		
		// Create an photo object based on the node's property
		this._photo = core.js.base.Encoder.decode(dojo.attr(domNode, "data-app-entity-props"));
		
		// JS LESSON: It seems to be the only solution to get the width and height of the image in Javascript
		var _this = this;
		this._photoImageNode.onload = function() {
			dojo.style(_this._photoTitleNode, "maxWidth", this.width + "px");
		};
		
		this._init();
	},
	
	getDomNode: function() {
		return this._domNode;			// DomNode
	},
	
	getPhotoTitleNode: function() {
		// summary:
		//		Returns the node showing photo's title
		return this._photoTitleNode;	// DomNode
	},
	
	getPhotoImageNode: function() {
		// summary:
		//		Returns the image node
		return this._photoImageNode;	// DomNode
	},
	
	getPhoto: function() {
		// summary:
		//		Gets the photo's properties
		return this._photo;		// Object
	},
	
	_init: function() {
		// summary:
		//		Initializes node
		var _this = this;
		dojo.connect(this._photoImageNode, "ondblclick", this, function(e) {
			this._photoListView.showSlide(this);
		});
		
		dojo.connect(this._domNode, "oncontextmenu", function(e) {
			e.preventDefault();
		});
		dojo.connect(this._domNode, "onmousedown", this, function(e) {
			if (dojo.mouseButtons.isRight(e)) {
				e.preventDefault();
				this._photoListView.onMouseDown(this);
			}
		});
	},
	
	setTitle: function(/*String*/ title) {
		// summary:
		//		Updates the UI when the title is changed
		dojo.attr(this._photoImageNode, "title", title);
	},
	
	setViewSize: function(/*String*/ size) {
		// summary:
		//		Shows the photos in given size of thumbnail
		// size:
		//		The size of thumbnail, can be: square, thumbnail, small, crop, medium
		this._photo.view_size = size;
		dojo.attr(this._photoImageNode, "src", this._photo[size]);
	},
	
	updateThumbnailUrl: function(/*String*/ size, /*String*/ url) {
		// summary:
		//		Updates the new thumbnail's URL after processing the image
		// size:
		//		The size of thumbnail, can be: square, thumbnail, small, crop, medium
		// url:
		//		The URL of thumbnail
		
		// Because I keep the same URL after processing the image (using the PhotoEditor), 
		// so the thumbnail is still cached by the browser.
		// To load the new thumbnail, I add the timestamp to the end of URL
		url = core.js.Constant.normalizeUrl(url) + "?" + new Date().getTime();
		this._photo[size] = url;
		
		if (this._photo.view_size == size) {
			dojo.attr(this._photoImageNode, "src", url);
		}
	}
});
