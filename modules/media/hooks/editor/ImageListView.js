/**
 * NextCMS
 * 
 * @author		Nguyen Huu Phuoc <thenextcms@gmail.com>
 * @copyright	Copyright (c) 2011 - 2012, Nguyen Huu Phuoc
 * @license		http://nextcms.org/license.txt	(GNU GPL version 2 or later)
 * @link		http://nextcms.org
 * @category	modules
 * @package		media
 * @subpackage	hooks
 * @since		1.0
 * @version		2011-10-24
 */

dojo.provide("media.hooks.editor.ImageListView");

dojo.require("dojo.dnd.Source");
dojo.require("dojox.NodeList.delegate");

dojo.require("core.js.base.dnd.TargetManager");
dojo.require("core.js.Constant");
dojo.require("media.hooks.editor.ImageContextMenu");

dojo.declare("media.hooks.editor.ImageListView", null, {
	// _id: String
	_id: null,
	
	// _contextMenu: media.hooks.editor.ImageContextMenu
	_contextMenu: null,
	
	// _selectedImage: DomNode
	_selectedImage: null,
	
	constructor: function(/*String*/ id) {
		this._id = id;
		new dojo.dnd.AutoSource(id, {
			accept: ["appDndImage"],
			selfAccept: false,
			selfCopy: false
		});
		
		this._contextMenu = new media.hooks.editor.ImageContextMenu(id);
		dojo.connect(this._contextMenu, "onContextMenu", this, "onContextMenu");
		dojo.connect(this._contextMenu, "onEditSize", this, "editImage");
	},
	
	addImageItem: function(/*Object*/ thumbnails) {
		var img = dojo.create("img", {
			className: "dojoDndItem mediaHooksEditorImageItem", 
			dndtype: "appDndImage",
			src: core.js.Constant.ROOT_URL + thumbnails.original.url,
			title: thumbnails.original.name
		}, this._id);
		
		var urls = {};
		for (var size in thumbnails) {
			urls[size] = thumbnails[size].url;
		}
		
		dojo.attr(img, {
			"_appImagePath": thumbnails.original.url,
			"data-app-dndimage": dojo.toJson({
				url: thumbnails.original.url,
				title: thumbnails.original.name
			}),
			"data-app-dndthumbnails": dojo.toJson(urls)
		});
	},
	
	editImage: function(/*String*/ size) {
		if (!this._selectedImage) {
			return;
		}
		var thumbnails = dojo.fromJson(dojo.attr(this._selectedImage, "data-app-dndthumbnails"));
		this.onEditSize(thumbnails[size]);
	},
	
	onContextMenu: function(/*DomNode*/ target) {
		switch (true) {
			case (dojo.attr(target, "id") == this._id):
				this._contextMenu.allowToEdit(null, false);
				this._selectedImage = null;
				break;
			case dojo.hasClass(target, "mediaHooksEditorImageItem"):
				// Get the available thumbnails
				var thumbnails = dojo.fromJson(dojo.attr(target, "data-app-dndthumbnails"));
				this._contextMenu.allowToEdit(null, false);
				
				// Enable the menu item that the associating thumbnail is available
				for (var thumb in thumbnails) {
					this._contextMenu.allowToEdit(thumb, true);
				}
				this._selectedImage = target;
				break;
			default:
				break;
		}
	},
	
	updateImage: function(/*String*/ size, /*String*/ path) {
		// summary:
		//		Updates image. It should be called after saving the image
		var images = dojo.query("img[_appImagePath='" + path + "']", this._id);
		if (images.length == 0) {
			return;
		}
		var image = images[0];
		
		// Update attributes of the image
		var path = path + "?" + new Date().getTime();
		dojo.attr(image, "src", core.js.Constant.ROOT_URL + path);
		var thumbnails = dojo.fromJson(dojo.attr(image, "data-app-dndthumbnails"));
		thumbnails[size] = path;
		dojo.attr(image, "data-app-dndthumbnails", dojo.toJson(thumbnails));
	},
	
	////////// CALLBACKS //////////
	
	onEditSize: function(/*String*/ path) {
		// tags:
		//		callback
	}
});
