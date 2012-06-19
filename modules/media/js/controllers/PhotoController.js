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
 * @version		2012-06-05
 */

dojo.provide("media.js.controllers.PhotoController");

dojo.require("dijit.form.Textarea");
dojo.require("dijit.form.TextBox");
dojo.require("dijit.InlineEditBox");
dojo.require("dojo.io.iframe");

dojo.require("core.js.base.controllers.ActionProvider");
dojo.require("core.js.base.controllers.Subscriber");
dojo.require("core.js.base.I18N");
dojo.require("core.js.base.Encoder");
dojo.require("core.js.base.views.Helper");
dojo.require("media.js.controllers.PhotoMediator");

dojo.declare("media.js.controllers.PhotoController", null, {
	// _id: String
	_id: null,
	
	// _i18n: Object
	_i18n: null,
	
	// _helper: core.js.base.views.Helper
	_helper: null,
	
	// _photoMediator: media.js.controllers.PhotoMediator
	_photoMediator: new media.js.controllers.PhotoMediator(),
	
	// TOPIC_GROUP: [const] String
	TOPIC_GROUP: "/media/js/controllers/PhotoController",
	
	constructor: function(/*String*/ id) {
		this._id = id;
		
		core.js.base.I18N.requireLocalization("media/languages");
		this._i18n = core.js.base.I18N.getLocalization("media/languages");
		
		// Create helper instance
		this._helper = new core.js.base.views.Helper(id);
		this._helper.setLanguageData(this._i18n);
		
		core.js.base.controllers.Subscriber.unsubscribe(this.TOPIC_GROUP);
	},

	////////// MANAGE ALBUMS //////////
	
	//_albumToolbar: media.js.views.AlbumToolbar
	_albumToolbar: null,
	
	// _albumListView: media.js.views.AlbumListView
	_albumListView: null,
	
	// _albumContextMenu: media.js.views.AlbumContextMenu
	_albumContextMenu: null,
	
	// _albumSearchCriteria: Object
	// 		Can contain the following members:
	//		- status
	//		- user_id
	//		- title
	//		- page: The page index
	//		- active_album_id: The id of current active album
	//		- view_type: Can be "list" or "grid"
	_albumSearchCriteria: {
		status: null,
		title: null,
		page: 1,
		active_album_id: null,
		view_type: "list",
		language: null
	},
	
	setAlbumToolbar: function(/*media.js.views.AlbumToolbar*/ albumToolbar) {
		// summary:
		//		Sets the album toolbar
		this._albumToolbar = albumToolbar;
		this._photoMediator.setAlbumToolbar(albumToolbar);
		
		// Adding album handler
		dojo.connect(albumToolbar, "onAddAlbum", this, "addAlbum");
		core.js.base.controllers.Subscriber.subscribe(this.TOPIC_GROUP, "/app/media/album/add/onCancel", this, function() {
			this._helper.closeDialog();
		});
		core.js.base.controllers.Subscriber.subscribe(this.TOPIC_GROUP, "/app/media/album/add/onSuccess", this, function() {
			this._helper.closeDialog();
			dojo.publish("/app/global/notification", [{ message: this._i18n.album.add.success }]);
			this.searchAlbums();
		});
		
		// Refresh handler
		dojo.connect(albumToolbar, "onRefresh", this, "searchAlbums");
		
		// Search handler
		dojo.connect(albumToolbar, "onSearchAlbums", this, function(title) {
			this.searchAlbums({
				title: title,
				page: 1
			});
		});
		
		// Switch to other language handler
		dojo.connect(albumToolbar, "onSwitchToLanguage", this, function(language) {
			this.searchAlbums({
				active_album_id: null,
				title: null,
				page: 1,
				language: language
			});
			this.searchPhotos({
				title: null,
				album_id: null,
				page: 1,
				language: language
			});
		});
		
		// Change view type handler
		dojo.connect(albumToolbar, "onChangeViewType", this, function(viewType) {
			this._albumSearchCriteria.view_type = viewType;
			if (this._albumListView) {
				this._albumListView.setViewType(viewType);
			}
		});
		
		return this;	// media.js.controllers.PhotoController
	},
	
	setAlbumListView: function(/*media.js.views.AlbumListView*/ albumListView) {
		// summary:
		//		Sets the albums list view
		this._albumListView = albumListView;
		this._photoMediator.setAlbumListView(albumListView);
		
		// Show the context menu
		dojo.connect(albumListView, "onMouseDown", this, function(albumItemView) {
			if (this._albumContextMenu) {
				this._albumContextMenu.show(albumItemView);
			}
		});
		
		core.js.base.controllers.Subscriber.subscribe(this.TOPIC_GROUP, "/app/media/album/list/onGotoPage", this, function(page) {
			this.searchAlbums({
				page: page
			});
		});
		
		// View all
		core.js.base.controllers.Subscriber.subscribe(this.TOPIC_GROUP, "/app/media/album/list/onViewAll", this, function(viewAllNode) {
			this._albumListView.setSelectedAlbumItemView(null);
			
			dojo.query(".mediaAlbumItemSelected", this._albumListView.getDomNode()).removeClass("mediaAlbumItemSelected");
			dojo.query(viewAllNode).addClass("mediaAlbumItemSelected");
			this._albumSearchCriteria.active_album_id = null;
			this.searchPhotos({
				page: 1,
				album_id: null
			});
		});
		
		// Loads the list of photos in the selected album when selecting an album
		dojo.connect(albumListView, "onClickAlbum", this, function(albumItemView) {
			this._albumListView.setSelectedAlbumItemView(albumItemView);
			
			var albumId = albumItemView.getAlbum().album_id;
			this._albumSearchCriteria.active_album_id = albumId;
			this.searchPhotos({
				page: 1,
				album_id: albumId
			});
		});
		
		dojo.connect(albumListView, "onDropPhotos", this, "dropPhotos");
		
		// Update cover handler
		dojo.connect(albumListView, "onUpdateCover", this, "updateCover");
		
		return this;	// media.js.controllers.PhotoController
	},
	
	setAlbumContextMenu: function(/*media.js.views.AlbumContextMenu*/ albumContextMenu) {
		// summary:
		//		Sets the album's context menu
		this._albumContextMenu = albumContextMenu;
		
		// Activate handler
		dojo.connect(albumContextMenu, "onActivateAlbum", this, "activateAlbum");
		
		// Delete album handler
		dojo.connect(albumContextMenu, "onDeleteAlbum", this, "deleteAlbum");
		core.js.base.controllers.Subscriber.subscribe(this.TOPIC_GROUP, "/app/media/album/delete/onSuccess", this, function() {
			dojo.publish("/app/global/notification", [{ message: this._i18n.album["delete"].success }]);
			this.searchAlbums();
		});
		core.js.base.controllers.Subscriber.subscribe(this.TOPIC_GROUP, "/app/media/album/delete/onCancel", this, function() {
			this._helper.closeDialog();
		});
		
		dojo.connect(albumContextMenu, "onRenameAlbum", this, "renameAlbum");
		dojo.connect(albumContextMenu, "onUploadToAlbum", this, function(albumItemView) {
			this.uploadPhotos(albumItemView.getAlbum().album_id);
		});
		
		return this;	// media.js.controllers.PhotoController
	},
	
	activateAlbum: function(/*media.js.views.AlbumItemView*/ albumItemView) {
		// summary:
		//		Activates/deactivates given album item
		var status = albumItemView.getAlbum().status;
		var _this  = this;
		dojo.xhrPost({
			url: core.js.base.controllers.ActionProvider.get("media_album_activate").url,
			content: {
				album_id: albumItemView.getAlbum().album_id
			},
			handleAs: "json",
			load: function(data) {
				var message = (data.result == "APP_RESULT_OK") 
							  ? (status == "activated" ? "deactivateSuccess" : "activateSuccess") 
							  : (status == "activated" ? "deactivateError" : "activateError");
				dojo.publish("/app/global/notification", [{
					message: _this._i18n.album.activate[message],
					type: (data.result == "APP_RESULT_OK") ? "message" : "error"
				}]);
				
				if (data.result == "APP_RESULT_OK") {
					albumItemView.getAlbum().status = (status == "activated") ? "not_activated" : "activated";
				}
			}
		});
	},
	
	addAlbum: function() {
		// summary:
		// 		This method is called when user click on Add New Album button. It shows a dialog for adding new album
		var params = {
			language: this._albumSearchCriteria.language
		};
		var url = core.js.base.controllers.ActionProvider.get("media_album_add").url + "?" + dojo.objectToQuery(params);
		this._helper.showDialog(url, {
			title: this._i18n.album.add.title,
			style: "width: 400px",
			refreshOnShow: true
		});
	},
	
	deleteAlbum: function(/*media.js.views.AlbumItemView*/ albumItemView) {
		// summary:
		//		Deletes given album item
		var params = {
			album_id: albumItemView.getAlbum().album_id	
		};
		var url = core.js.base.controllers.ActionProvider.get("media_album_delete").url + "?" + dojo.objectToQuery(params);
		this._helper.showDialog(url, {
			title: this._i18n.album["delete"].title,
			style: "width: 250px",
			refreshOnShow: true
		});
	},
	
	renameAlbum: function(/*media.js.views.AlbumItemView*/ albumItemView) {
		// summary:
		//		Renames given album
		var _this   = this;
		var albumId = albumItemView.getAlbum().album_id;

		// Create InlineEditBox element
		// DOJO LESSON: There is an issue said that the widget is already registered if I try to rename an album at the second time.
		// There are two solutions to solve this issue:
		// - First, generate an unique Id for the widget as follow:
		//		var inlineEditBox = new dijit.InlineEditBox({
		//			id: "media.js.views.AlbumItemView_InlineEditBox_" + albumId + "_" + new Date().getTime(),
		//			...
		//		}, albumItemView.getAlbumTitleNode());
		// - Second one, unregistry the widget:
		//		var inlineEditBox = new dijit.InlineEditBox({
		//			onChange: function(value) {
		//				dijit.registry.remove(this.id);
		//				...
		//			onCancel: function() {
		//				dijit.registry.remove(this.id);
		//				...
		//			}
		//		}, albumItemView.getAlbumTitleNode());
		// In both of solutions, Dojo generates new widgets without removing the old one.
		// So, my final soltuion is attach an InlineEditBox instance to the albumItemView and it is possible 
		// to get it in the next time.
		if (!albumItemView.titleEditBox) {
			albumItemView.titleEditBox = new dijit.InlineEditBox({
				editor: "dijit.form.TextBox", 
				autoSave: true, 
				disabled: false,
				editorParams: {
					required: true
				},
				onChange: function(newTitle) {
					this.set("disabled", true);
					if (newTitle != "") {
						dojo.xhrPost({
							url: core.js.base.controllers.ActionProvider.get("media_album_rename").url,
							content: {
								album_id: albumId,
								title: newTitle
							},
							handleAs: "json",
							load: function(data) {
								if (data.result == "APP_RESULT_OK") {
									albumItemView.getAlbum().title 		 = data.title;
									albumItemView.getAlbum().short_title = data.short_title;
									// Push a notification
									dojo.publish("/app/global/notification", [{ message: _this._i18n.album.rename.success }]);
								}
							}
						});
					}
				}, 
				onCancel: function() {
					this.set("disabled", true);
				}
			}, albumItemView.getAlbumTitleNode());
		}
		albumItemView.titleEditBox.set("disabled", false);
		albumItemView.titleEditBox.startup();
		albumItemView.titleEditBox.edit();
	},
	
	searchAlbums: function(/*Object*/ criteria) {
		// summary:
		//		This method is called when user search for albums
		this._helper.closeDialog();
		
		var _this = this;
		dojo.mixin(this._albumSearchCriteria, criteria);
		var q = core.js.base.Encoder.encode(this._albumSearchCriteria);
		
		var url = core.js.base.controllers.ActionProvider.get("media_album_list").url;
		// There are two ways to update the content of album list view.
		// 1) Because _albumListView is Dojo ContentPane, I can set the href attribute:
		//		dijit.byId(this._albumListView.getId()).set("href", url + "?q=" + q);
		//		this._albumListView.init();
		// Becareful with this, because it will not work when the DomNode of album list view 
		// is not loaded (for example, when I call this searchAlbums() method in setAlbumListView())
		//		console.log(dijit.byId(this._albumListView.getId()));	// undefined
		//
		// 2) Make an Ajax request to the page of listing albums as usual:
		dojo.xhrPost({
			url: url,
			content: {
				q: q
			},
			load: function(data) {
				_this._albumListView.setContent(data);
			}
		});
	},
	
	updateCover: function(/*media.js.views.AlbumItemView*/ albumItemView, /*Object*/ thumbnails) {
		// summary:
		//		Updates the album's cover. Called after dropping an image from 
		// 		the Image Editor toolbox
		// tags:
		//		callback
		this._helper.showStandby();
		var _this = this;
		dojo.xhrPost({
			url: core.js.base.controllers.ActionProvider.get("media_album_cover").url,
			content: {
				album_id: albumItemView.getAlbum().album_id,
				thumbnails: dojo.toJson(thumbnails)
			},
			handleAs: "json",
			load: function(data) {
				_this._helper.hideStandby();
				if (data.result == "APP_RESULT_OK") {
					dojo.publish("/app/global/notification", [{ message: _this._i18n.album.cover.success }]);
					albumItemView.updateCover(data.thumbnails);
				}
			}
		});
	},
	
	////////// MANAGE PHOTOS //////////
	
	// _photoToolbar: media.js.views.PhotoToolbar
	_photoToolbar: null,
	
	// _photoListView: media.js.views.PhotoListView
	_photoListView: null,
	
	// _photoContextMenu: media.js.views.PhotoContextMenu
	_photoContextMenu: null,
	
	// _photoSearchCriteria: Object
	_photoSearchCriteria: {
		album_id: null,
		status: null,
		page: 1,
		view_size: "thumbnail",
		per_page: 20,
		language: null
	},
	
	setPhotoToolbar: function(/*media.js.views.PhotoToolbar*/ photoToolbar) {
		// summary:
		//		Sets the photo toolbar
		this._photoToolbar = photoToolbar;
		this._photoMediator.setPhotoToolbar(photoToolbar);
		
		// Upload handler
		dojo.connect(photoToolbar, "onUploadPhotos", this, "uploadPhotos");
		core.js.base.controllers.Subscriber.subscribe(this.TOPIC_GROUP, "/app/media/photo/upload/onCancel", this, function() {
			this._helper.removePane();
		});
		core.js.base.controllers.Subscriber.subscribe(this.TOPIC_GROUP, "/app/media/photo/upload/onSuccess", this, function(data) {
			this._helper.removePane();
			
			// Activate the album
			var albumItemView = this._albumListView.getAlbumItemView(data.album_id);
			if (albumItemView) {
				this._albumListView.setSelectedAlbumItemView(albumItemView);
				
				// Update the number of photos in the album
				albumItemView.increasePhotoCounter(data.num_photos);
			}
			
			// Load the photos in the albums
			this.searchPhotos({
				page: 1,
				album_id: data.album_id
			});
		});
		
		// Refresh handler
		dojo.connect(photoToolbar, "onRefresh", this, "searchPhotos");
		
		// Show slide
		dojo.connect(photoToolbar, "onShowSlide", this, function() {
			if (this._photoListView) {
				this._photoListView.showSlide();
			}
		});
		
		// Save order of photos in album
		dojo.connect(photoToolbar, "onSaveOrder", this, "savePhotoOrder");
		
		// Search by title
		dojo.connect(photoToolbar, "onSearchPhotos", this, function(title) {
			this.searchPhotos({
				title: title,
				page: 1
			});
		});
		
		// Update page size
		dojo.connect(photoToolbar, "onUpdatePageSize", this, function(perPage) {
			if (this._photoSearchCriteria.per_page != perPage) {
				this.searchPhotos({
					page: 1,
					per_page: perPage
				});
			}
		});
		
		// View in various size
		dojo.connect(photoToolbar, "onViewSize", this, function(size) {
			this._photoSearchCriteria.view_size = size;
			if (this._photoListView) {
				this._photoListView.setViewSize(size);
			}
		});
		
		return this;	// media.js.controllers.PhotoController
	},
	
	setPhotoListView: function(/*media.js.views.PhotoListView*/ photoListView) {
		// summary:
		//		Sets the photos list view
		this._photoListView = photoListView;
		this._photoMediator.setPhotoListView(photoListView);
		
		// Show the context menu
		dojo.connect(photoListView, "onMouseDown", this, function(photoItemView) {
			if (this._photoContextMenu) {
				this._photoContextMenu.show(photoItemView);
			}
		});
		
		// Pager handler
		core.js.base.controllers.Subscriber.subscribe(this.TOPIC_GROUP, "/app/media/photo/list/onGotoPage", this, function(page) {
			this.searchPhotos({
				page: page
			});
		});
		
		return this;	// media.js.controllers.PhotoController
	},
	
	setPhotoContextMenu: function(/*media.js.views.PhotoContextMenu*/ photoContextMenu) {
		// summary:
		//		Sets the photo's context menu
		this._photoContextMenu = photoContextMenu;
		this._photoMediator.setPhotoContextMenu(photoContextMenu);
		
		// Activate handler
		dojo.connect(photoContextMenu, "onActivatePhoto", this, "activatePhoto");
		
		// Update information handler
		dojo.connect(photoContextMenu, "onUpdatePhoto", this, "updatePhoto");
		core.js.base.controllers.Subscriber.subscribe(this.TOPIC_GROUP, "/app/media/photo/update/onSuccess", this, function(data) {
			dojo.publish("/app/global/notification", [{ message: this._i18n.photo.update.success }]);
			this.searchPhotos();
		});
		core.js.base.controllers.Subscriber.subscribe(this.TOPIC_GROUP, "/app/media/photo/update/onCancel", this, function() {
			this._helper.removePane();
		});
		
		// Delete handler
		dojo.connect(photoContextMenu, "onDeletePhoto", this, "deletePhoto");
		core.js.base.controllers.Subscriber.subscribe(this.TOPIC_GROUP, "/app/media/photo/delete/onSuccess", this, function() {
			dojo.publish("/app/global/notification", [{ message: this._i18n.photo["delete"].success }]);
			this.searchAlbums();
			this.searchPhotos();
		});
		core.js.base.controllers.Subscriber.subscribe(this.TOPIC_GROUP, "/app/media/photo/delete/onCancel", this, function() {
			this._helper.closeDialog();
		});
		
		// Rename handler
		dojo.connect(photoContextMenu, "onRenamePhoto", this, "renamePhoto");
		
		// Replace handler
		dojo.connect(photoContextMenu, "onReplacePhoto", this, "replacePhoto");
		core.js.base.controllers.Subscriber.subscribe(this.TOPIC_GROUP, "/app/media/photo/replace/onSuccess", this, function(data) {
			this._helper.closeDialog();
			dojo.publish("/app/global/notification", [{
				message: this._i18n.photo.replace.success,
				type: "message"
			}]);
			// Update the thumbnail
			var photoItemView = this._photoListView.getPhotoItemView(data[0].photo_id);
			if (photoItemView) {
				for (var i in data) {
					photoItemView.updateThumbnailUrl(data[i].size, data[i].path);
				}
			}
		});
		core.js.base.controllers.Subscriber.subscribe(this.TOPIC_GROUP, "/app/media/photo/replace/onCancel", this, function() {
			this._helper.closeDialog();
		});
		
		// Download handler
		dojo.connect(photoContextMenu, "onDownloadPhoto", this, "downloadPhoto");
		
		// Set cover handler
		dojo.connect(photoContextMenu, "onSetCover", this, "setAlbumCover");
		
		// Remove from album handler
		dojo.connect(photoContextMenu, "onRemoveFromAlbum", this, "removePhoto");
		
		// Edit handler
		dojo.connect(photoContextMenu, "onEditPhoto", this, "editPhoto");
		core.js.base.controllers.Subscriber.subscribe(this.TOPIC_GROUP, "/app/media/photo/edit/onSaved", this, function(data) {
			// Show the new thumbnail
			var photoItemView = this._photoListView.getPhotoItemView(data.photo_id);
			if (photoItemView) {
				photoItemView.updateThumbnailUrl(data.size, data.url);
			}
		});
		core.js.base.controllers.Subscriber.subscribe(this.TOPIC_GROUP, "/app/media/photo/edit/onCleaned", this, function() {
			this._helper.removePane();
		});
		
		return this;	// media.js.controllers.PhotoController
	},
	
	initPhotoSearchCriteria: function(/*Object*/ criteria) {
		// summary:
		//		Inits the controls with given criteria
		dojo.mixin(this._photoSearchCriteria, criteria);
		this._albumSearchCriteria.language        = criteria.language;
		this._albumSearchCriteria.active_album_id = criteria.album_id;
		this._photoMediator.initPhotoSearchCriteria(this._photoSearchCriteria);
	},
	
	activatePhoto: function(/*media.js.views.PhotoItemView*/ photoItemView) {
		// summary:
		//		Activates/deactivates given photo item
		var status = photoItemView.getPhoto().status;
		var _this  = this;
		dojo.xhrPost({
			url: core.js.base.controllers.ActionProvider.get("media_photo_activate").url,
			content: {
				photo_id: photoItemView.getPhoto().photo_id
			},
			handleAs: "json",
			load: function(data) {
				var message = (data.result == "APP_RESULT_OK") 
							  ? (status == "activated" ? "deactivateSuccess" : "activateSuccess") 
							  : (status == "activated" ? "deactivateError" : "activateError");
				dojo.publish("/app/global/notification", [{
					message: _this._i18n.photo.activate[message],
					type: (data.result == "APP_RESULT_OK") ? "message" : "error"
				}]);
				
				if (data.result == "APP_RESULT_OK") {
					photoItemView.getPhoto().status = (status == "activated") ? "not_activated" : "activated";
				}
			}
		});
	},
	
	deletePhoto: function(/*media.js.views.PhotoItemView*/ photoItemView) {
		// summary:
		// 		Deletes given photo item
		var params = {
			photo_id: photoItemView.getPhoto().photo_id
		};
		var url = core.js.base.controllers.ActionProvider.get("media_photo_delete").url + "?" + dojo.objectToQuery(params);
		this._helper.showDialog(url, {
			title: this._i18n.photo["delete"].title,
			style: "width: 250px",
			refreshOnShow: true
		});
	},
	
	downloadPhoto: function(/*media.js.views.PhotoItemView*/ photoItemView, /*String*/ size) {
		// summary:
		//		Downloads given photo in certain size of thumbnail
		var photoId = photoItemView.getPhoto().photo_id;
		
		// DOJO LESSON: Use dojo.io.iframe to download file
		// FIXME: I don't know why the browser cannot load the blank page provided by Dojo
		// (/static/js/dojo/[APP_DOJO_VER]/dojo/resources/blank.html)
		dojo.io.iframe.send({
			url: core.js.base.controllers.ActionProvider.get("media_photo_download").url,
			method: "GET",
			content: {
				photo_id: photoId,
				size: size
			}
		});
	},
	
	editPhoto: function(/*media.js.views.PhotoItemView*/ photoItemView, /*String*/ size) {
		// summary:
		//		Shows a photo editor
		var params = dojo.objectToQuery({
			photo_id: photoItemView.getPhoto().photo_id,
			size: size
		});
		this._helper.showPane(core.js.base.controllers.ActionProvider.get("media_photo_edit").url + "?" + params);
	},
	
	dropPhotos: function(/*media.js.views.AlbumItemView*/ albumItemView, /*DomNode[]*/ photoNodes) {
		// summary:
		//		Uses when moving collection of photos to another album
		var newAlbumId = albumItemView.getAlbum().album_id;
		var _this = this;
		var index = 0;
		var move = function() {
			var photoNode = photoNodes[0];
			var photoId   = core.js.base.Encoder.decode(dojo.attr(photoNode, "data-app-entity-props")).photo_id;
			
			dojo.xhrPost({
				url: core.js.base.controllers.ActionProvider.get("media_photo_copy").url,
				content: {
					photo_id: photoId,
					album_id: newAlbumId,
					index: index
				},
				handleAs: "json",
				load: function(data) {
					if (data.result == "APP_RESULT_OK") {
						photoNodes.splice(0, 1);
						if (photoNodes.length > 0) {
							index++;
							move();
						} else {
							_this._helper.hideStandby();
							_this.searchAlbums();
						}
					} else {
						_this._helper.showStandby();
						_this.searchAlbums();
					}
				}
			});
		};
		
		this._helper.showStandby();
		move();
	},
	
	removePhoto: function(/*media.js.views.PhotoItemView*/ photoItemView) {
		// summary:
		//		Removes photo from album
		if (!this._photoSearchCriteria.album_id) {
			return;
		}
		this._helper.showStandby();
		var _this   = this,
			albumId = this._photoSearchCriteria.album_id;
		dojo.xhrPost({
			url: core.js.base.controllers.ActionProvider.get("media_photo_remove").url,
			content: {
				album_id: albumId,
				photo_id: photoItemView.getPhoto().photo_id
			},
			handleAs: "json",
			load: function(data) {
				_this._helper.hideStandby();
				if (data.result == "APP_RESULT_OK") {
					dojo.publish("/app/global/notification", [{ message: _this._i18n.photo.remove.success }]);
					
					_this._photoListView.removePhotoItemView(photoItemView);
					_this._photoListView.increasePhotoCounter(-1);
					var albumItemView = _this._albumListView.getAlbumItemView(albumId);
					if (albumItemView) {
						albumItemView.increasePhotoCounter(-1);
					}
				}
			}
		});
	},
	
	renamePhoto: function(/*media.js.views.PhotoItemView*/ photoItemView) {
		// summary:
		//		Renames given photo item
		var _this   = this;
		var photoId = photoItemView.getPhoto().photo_id;
		
		// Create InlineEditBox element
		if (!photoItemView.titleEditBox) {
			photoItemView.titleEditBox = new dijit.InlineEditBox({
				editor: "dijit.form.Textarea",
				autoSave: true,
				disabled: false,
				editorParams: {
					required: true
				},
				onChange: function(newTitle) {
					this.set("disabled", true);
					if (newTitle != "") {
						dojo.xhrPost({
							url: core.js.base.controllers.ActionProvider.get("media_photo_rename").url,
							content: {
								photo_id: photoId,
								title: newTitle
							},
							handleAs: "json",
							load: function(data) {
								if (data.result == "APP_RESULT_OK") {
									photoItemView.setTitle(newTitle);
									dojo.publish("/app/global/notification", [{ message: _this._i18n.photo.rename.success }]);
								}
							}
						});
					}
				},
				onCancel: function() {
					this.set("disabled", true);
				}
			}, photoItemView.getPhotoTitleNode());
		}
		photoItemView.titleEditBox.set("disabled", false);
		photoItemView.titleEditBox.startup();
		photoItemView.titleEditBox.edit();
	},
	
	replacePhoto: function(/*media.js.views.PhotoItemView*/ photoItemView) {
		// summary:
		//		Replaces given photo item
		var params = {
			photo_id: photoItemView.getPhoto().photo_id
		};
		var url = core.js.base.controllers.ActionProvider.get("media_photo_replace").url + "?" + dojo.objectToQuery(params);
		this._helper.showDialog(url, {
			title: this._i18n.photo.replace.title,
			style: "width: 250px",
			refreshOnShow: true
		});
	},
	
	savePhotoOrder: function() {
		// summary:
		//		Saves the order of photos in the selected album
		var albumItemView  = this._albumListView.getSelectedAlbumItemView();
		if (!albumItemView) {
			return;
		}
		var photoItemViews = this._photoListView.getPhotoItemViews(),
			data = [],
			startIndex = this._photoSearchCriteria.per_page * (this._photoSearchCriteria.page - 1) + 1;  
		for (var i = 0; i < photoItemViews.length; i++) {
			data.push({
				photo_id: photoItemViews[i].getPhoto().photo_id,
				album_id: albumItemView.getAlbum().album_id,
				index: startIndex + i
			});
		}
		
		this._helper.showStandby();
		var _this = this;
		dojo.xhrPost({
			url: core.js.base.controllers.ActionProvider.get("media_photo_order").url,
			content: {
				data: dojo.toJson(data)
			},
			handleAs: "json",
			load: function(data) {
				_this._helper.hideStandby();
				if (data.result == "APP_RESULT_OK") {
					dojo.publish("/app/global/notification", [{
						message: _this._i18n.photo.order.success,
						type: "message"
					}]);
				}
			}
		});
	},
	
	searchPhotos: function(/*Object*/ criteria) {
		// summary:
		//		Searches for photos
		var _this = this;
		this._helper.closeDialog();
		
		dojo.mixin(this._photoSearchCriteria, criteria);
		var q   = core.js.base.Encoder.encode(this._photoSearchCriteria);
		var url = core.js.base.controllers.ActionProvider.get("media_photo_list").url;
		dojo.hash("u=" + url + "/?q=" + q);
		
		this._helper.showStandby();
		dojo.xhrPost({
			url: url,
			content: {
				q: q,
				format: "html"
			},
			load: function(data) {
				_this._helper.hideStandby();
				_this._photoListView.setContent(data);
			}
		});
	},
	
	setAlbumCover: function(/*media.js.views.PhotoItemView*/ photoItemView) {
		// summary:
		//		Updates album's cover
		if (!this._photoSearchCriteria.album_id) {
			return;
		}
		this._helper.showStandby();
		var _this   = this,
			albumId = this._photoSearchCriteria.album_id;
		dojo.xhrPost({
			url: core.js.base.controllers.ActionProvider.get("media_album_cover").url,
			content: {
				album_id: albumId,
				photo_id: photoItemView.getPhoto().photo_id
			},
			handleAs: "json",
			load: function(data) {
				_this._helper.hideStandby();
				if (data.result == "APP_RESULT_OK") {
					dojo.publish("/app/global/notification", [{ message: _this._i18n.album.cover.success }]);
					var albumItemView = _this._albumListView.getAlbumItemView(albumId);
					if (albumItemView) {
						albumItemView.updateCover(data.thumbnails);
					}
				}
			}
		});
	},
	
	updatePhoto: function(/*media.js.views.PhotoItemView*/ photoItemView) {
		// summary:
		//		Updates given photo
		var params = {
			photo_id: photoItemView.getPhoto().photo_id
		};
		this._helper.showPane(core.js.base.controllers.ActionProvider.get("media_photo_update").url + "?" + dojo.objectToQuery(params));
	},
	
	uploadPhotos: function(/*String?*/ albumId) {
		// summary:
		//		Uploads photos to given album
		// albumId:
		//		Id of album
		var params = {
			language: this._photoSearchCriteria.language
		};
		if (albumId) {
			params.album_id = albumId;
		} else if (this._photoSearchCriteria.album_id) {
			params.album_id = this._photoSearchCriteria.album_id;
		}
		var url = core.js.base.controllers.ActionProvider.get("media_photo_upload").url + "?" + dojo.objectToQuery(params);
		this._helper.showPane(url);
	}
});
