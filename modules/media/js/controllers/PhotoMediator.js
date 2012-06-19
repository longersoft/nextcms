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

dojo.provide("media.js.controllers.PhotoMediator");

dojo.require("core.js.base.controllers.Subscriber");
dojo.require("core.js.Constant");

dojo.declare("media.js.controllers.PhotoMediator", null, {
	// summary:
	//		This class is used to control the state of controls in the controller
	
	// _photoToolbar: media.js.views.PhotoToolbar,
	_photoToolbar: null,
	
	// _photoContextMenu: media.js.views.PhotoContextMenu
	_photoContextMenu: null,
	
	// _photoListView: media.js.views.PhotoListView
	_photoListView: null,
	
	// _albumListView: media.js.views.AlbumListView
	_albumListView: null,
	
	//_albumToolbar: media.js.views.AlbumToolbar
	_albumToolbar: null,
	
	// TOPIC_GROUP: [const] String
	TOPIC_GROUP: "/media/js/controllers/PhotoMediator",
	
	constructor: function() {
		core.js.base.controllers.Subscriber.unsubscribe(this.TOPIC_GROUP);
	},
	
	////////// SET CONTROLS //////////
	
	setAlbumToolbar: function(/*media.js.views.AlbumToolbar*/ albumToolbar) {
		// summary:
		//		Sets the album toolbar
		this._albumToolbar = albumToolbar;
	},
	
	setPhotoToolbar: function(/*media.js.views.PhotoToolbar*/ photoToolbar) {
		// summary:
		//		Sets the photo toolbar
		this._photoToolbar = photoToolbar;
	},
	
	setPhotoContextMenu: function(/*media.js.views.PhotoContextMenu*/ photoContextMenu) {
		// summary:
		//		Sets the photo's context menu
		this._photoContextMenu = photoContextMenu;
		dojo.connect(photoContextMenu, "onContextMenu", this, "onPhotoItemViewMouseDown");
	},
	
	setPhotoListView: function(/*media.js.views.PhotoListView*/ photoListView) {
		// summary:
		//		Sets the photos list view
		this._photoListView = photoListView;
		dojo.connect(photoListView, "onPopulatePhotos", this, "onPopulatePhotos");
	},
	
	setAlbumListView: function(/*media.js.views.AlbumListView*/ albumListView) {
		// summary:
		//		Sets the albums list view
		this._albumListView = albumListView;
		core.js.base.controllers.Subscriber.subscribe(this.TOPIC_GROUP, "/app/media/album/list/onViewAll", this, "onViewAllAlbum");
	},
	
	////////// UPDATE STATE OF CONTROLS //////////
	
	initPhotoSearchCriteria: function(/*Object*/ criteria) {
		// summary:
		//		Inits the controls with given criteria. It should be called at the first time the page is loaded
		if (criteria.per_page) {
			this._photoToolbar.setPerPageValue(criteria.per_page);
		}
		this._albumToolbar.setLanguage(criteria.language);
		this._photoToolbar.allowToSaveOrder(criteria.album_id)
						  .setKeyword(criteria.title)
						  .setViewSize(criteria.view_size);
	},
	
	onViewAllAlbum: function(/*DomNode*/ viewAllNode) {
		// summary:
		//		This method is called when user click on the ViewAll item
		this._photoToolbar.allowToSaveOrder(false);
	},
	
	onPhotoItemViewMouseDown: function(/*media.js.views.PhotoItemView*/ photoItemView) {
		// Enable the set cover item if the selected photo is not cover of the selected album
		var albumItemView = this._albumListView.getSelectedAlbumItemView();
		this._photoContextMenu.allowToSetCover(albumItemView && albumItemView.getAlbum().cover != photoItemView.getPhoto().photo_id)
							  // Disable the remove item if the selected photo is used as cover of the selected album
							  .allowToRemove(albumItemView && albumItemView.getAlbum().cover != photoItemView.getPhoto().photo_id)
							  // Don't allow to edit the photo if it is stored on other server
							  .allowToEdit(photoItemView.getPhoto().square.substr(0, core.js.Constant.ROOT_URL.length) == core.js.Constant.ROOT_URL);
	},
	
	onPopulatePhotos: function() {
		// Disable some controls in the photo's toolbar if there is no photos
		var numPhotos = this._photoListView.getNumPhotoItemViews();
		this._photoToolbar.allowToShowSlide(numPhotos > 0)
						  .allowToSearch(numPhotos > 0)
						  .allowToViewSize(numPhotos > 0)
						  .allowToChangePageSize(numPhotos > 0)
						  // Disable the save order button if there is no album selected
						  .allowToSaveOrder(numPhotos > 0 && this._albumListView.getSelectedAlbumItemView());
	}
});
