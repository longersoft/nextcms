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
 * @version		2011-10-18
 */

dojo.provide("media.js.views.PhotoToolbar");

dojo.require("dijit.form.Button");
dojo.require("dijit.form.Select");
dojo.require("dijit.form.Slider");
dojo.require("dijit.form.TextBox");
dojo.require("dijit.Toolbar");
dojo.require("dijit.ToolbarSeparator");

dojo.require("core.js.base.controllers.ActionProvider");
dojo.require("core.js.base.I18N");

dojo.declare("media.js.views.PhotoToolbar", null, {
	// _toolbar: dijit.Toolbar
	_toolbar: null,
	
	// _i18n: Object
	_i18n: null,
	
	// _slideShowButton: dijit.form.Button
	_slideShowButton: null,
	
	// _saveOrderButton: dijit.form.Button
	_saveOrderButton: null,
	
	// _searchTextBox: dijit.form.TextBox
	_searchTextBox: null,
	
	// _searchButton: dijit.form.Button
	_searchButton: null,
	
	// _sizeSlider: dijit.form.HorizontalSlider
	_sizeSlider: null,
	
	// _sizeSliderValueHash: Object
	_sizeSliderValueHash: {
		"0": "square",
		"10": "thumbnail",
		"20": "small",
		"30": "crop",
		"40": "medium"
	},
	
	// _sizeSliderLabelHash: Object
	_sizeSliderLabelHash: {
		"square": "0",
		"thumbnail": "10",
		"small": "20",
		"crop": "30",
		"medium": "40"
	},
	
	// _perPageSelect: dijit.form.Select
	_perPageSelect: null,
	
	// _numPhotosPerPage: Array
	//		Each item is number of photos per page that user want to view
	_numPhotosPerPage: [ 20, 40, 60, 80, 100 ],
	
	constructor: function(/*String*/ id) {
		this._toolbar = new dijit.Toolbar({}, id);
		
		core.js.base.I18N.requireLocalization("media/languages");
		this._i18n = core.js.base.I18N.getLocalization("media/languages");
		
		// Add toolbar items
		var _this = this;
		
		// Upload button
		this._toolbar.addChild(new dijit.form.Button({
			label: this._i18n.global._share.uploadAction,
			showLabel: false,
			iconClass: "appIcon appUploadIcon",
			disabled: !core.js.base.controllers.ActionProvider.get("media_photo_upload").isAllowed,
			onClick: function(e) {
				_this.onUploadPhotos();
			}
		}));
		
		// Refresh button
		this._toolbar.addChild(new dijit.form.Button({
			label: this._i18n.global._share.refreshAction,
			showLabel: false,
			iconClass: "appIcon appRefreshIcon",
			disabled: !core.js.base.controllers.ActionProvider.get("media_photo_list").isAllowed,
			onClick: function(e) {
				_this.onRefresh();
			}
		}));
		
		// Slideshow
		this._slideShowButton = new dijit.form.Button({
			label: this._i18n.global._share.showSlideAction,
			showLabel: false,
			iconClass: "appIcon mediaPhotoSlideshowIcon",
			onClick: function(e) {
				_this.onShowSlide();
			}
		});
		this._toolbar.addChild(this._slideShowButton);
		this._toolbar.addChild(new dijit.ToolbarSeparator());
		
		// Save orders button
		this._saveOrderButton = new dijit.form.Button({
			label: this._i18n.global._share.saveOrderAction,
			showLabel: true,
			iconClass: "appIcon appSaveIcon",
			disabled: !core.js.base.controllers.ActionProvider.get("media_photo_order").isAllowed,
			onClick: function(e) {
				_this.onSaveOrder();
			}
		});
		this._toolbar.addChild(this._saveOrderButton);
		this._toolbar.addChild(new dijit.ToolbarSeparator());
		
		// Search control
		this._searchTextBox = new dijit.form.TextBox({
			style: "width: 150px",
			placeHolder: this._i18n.photo.list.searchPhotoHelp
		});
		this._searchButton = new dijit.form.Button({
			label: this._i18n.global._share.searchAction,
			showLabel: false,
			iconClass: "appIcon appSearchIcon",
			disabled: !core.js.base.controllers.ActionProvider.get("media_photo_list").isAllowed,
			onClick: function(e) {
				var title = _this._searchTextBox.get("value");
				_this.onSearchPhotos(title);
			}
		});
		this._toolbar.addChild(this._searchTextBox);
		this._toolbar.addChild(this._searchButton);
		
		// View the thumbnails in various size
		this._sizeSlider = new dijit.form.HorizontalSlider({
			value: 10,
			minimum: 0,
			maximum: 40,
			discreteValues: 5,
			intermediateChanges: true,
			style: "width: 200px;",
			showButtons: true,
			onChange: function(value) {
				_this.onViewSize(_this._sizeSliderValueHash[value + ""]);
			}
		});
		
		// Put the slider at the right side
		dojo.addClass(this._sizeSlider.domNode, "appRight");
		this._toolbar.addChild(this._sizeSlider);
		
		var separator = new dijit.ToolbarSeparator();
		dojo.addClass(separator.domNode, "appRight");
		this._toolbar.addChild(separator);
		
		// Select the number of photos per page
		var options = [];
		dojo.forEach(this._numPhotosPerPage, function(value, index) {
			options.push({
				label: value,
				value: value + ""
			});
		});
		this._perPageSelect = new dijit.form.Select({ 
			options: options, 
			style: "height: 20px",
			disabled: !core.js.base.controllers.ActionProvider.get("media_photo_list").isAllowed,
			onChange: function(value) {
				_this.onUpdatePageSize(parseInt(value));
			}
		});
		dojo.addClass(this._perPageSelect.domNode, "appRight");
		this._toolbar.addChild(this._perPageSelect);
	},
	
	////////// ENABLE/DISABLE CONTROLS //////////
	
	allowToShowSlide: function(/*Boolean*/ isAllowed) {
		// summary:
		//		Allows/disallows to show the slide of photos
		this._slideShowButton.set("disabled", !isAllowed);
		return this;	// media.js.views.PhotoToolbar
	},
	
	allowToViewSize: function(/*Boolean*/ isAllowed) {
		// summary:
		//		Allows/disallows to change the view size
		this._sizeSlider.set("disabled", !isAllowed);
		return this;	// media.js.views.PhotoToolbar
	},
	
	allowToSearch: function(/*Boolean*/ isAllowed) {
		// summary:
		//		Allows/disallows to search for photos
		this._searchTextBox.set("disabled", !isAllowed || !core.js.base.controllers.ActionProvider.get("media_photo_list").isAllowed);
		this._searchButton.set("disabled", !isAllowed || !core.js.base.controllers.ActionProvider.get("media_photo_list").isAllowed);
		return this;	// media.js.views.PhotoToolbar
	},
	
	allowToSaveOrder: function(/*Boolean*/ isAllowed) {
		// summary:
		//		Allows/disallows to save order of photos in album
		this._saveOrderButton.set("disabled", !isAllowed || !core.js.base.controllers.ActionProvider.get("media_photo_order").isAllowed);
		return this;	// media.js.views.PhotoToolbar
	},
	
	allowToChangePageSize: function(/*Boolean*/ isAllowed) {
		// summary:
		//		Allows/disallows to change the number of photos per page
		this._perPageSelect.set("disabled", !isAllowed || !core.js.base.controllers.ActionProvider.get("media_photo_list").isAllowed);
		return this;	// media.js.views.PhotoToolbar
	},
	
	setPerPageValue: function(/*Number*/ perPage) {
		var index = dojo.indexOf(this._numPhotosPerPage, perPage);
		if (index != -1) {
			// DOJO LESSON: To set the value for the select widget and make it display the item as the selected one,
			// I have to convert the value to string
			this._perPageSelect.set("value", perPage + "");
		}
		return this;	// media.js.views.PhotoToolbar
	},
	
	setKeyword: function(/*String*/ keyword) {
		if (keyword) {
			this._searchTextBox.set("value", keyword);
		}
		return this;	// media.js.views.PhotoToolbar
	},
	
	setViewSize: function(/*String*/ viewSize) {
		this._sizeSlider.set("value", this._sizeSliderLabelHash[viewSize]);
		return this;	// media.js.views.PhotoToolbar
	},
	
	////////// CALLBACKS //////////
	
	onRefresh: function() {
		// summary:
		//		This method is called when the refresh button is clicked
		// tags:
		//		callback
	},
	
	onSaveOrder: function() {
		// summary:
		//		Save the order of photos in the album
		// tags:
		//		callback
	},
	
	onSearchPhotos: function(/*String*/ title) {
		// summary:
		//		This method is called when the search button is clicked
		// tags:
		//		callback
	},
	
	onShowSlide: function() {
		// summary:
		//		Show the slide. It is called when the slideshow button is clicked
		// tags:
		//		callback
	},
	
	onUpdatePageSize: function(/*Integer*/ perPage) {
		// summary:
		//		This method is called when the page size select changes its value
		// perPage:
		//		The number of photos per page
		// tags:
		//		callback
	},
	
	onUploadPhotos: function() {
		// summary:
		//		This method is called when the upload button is clicked
		// tags:
		//		callback
	},
	
	onViewSize: function(/*String*/ size) {
		// summary:
		//		This method is called when the size slider changed value
		// size:
		//		The thumbnail size: square, thumbnail, small, crop, medium 
		// tags:
		//		callback
	}
});
