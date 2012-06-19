/**
 * NextCMS
 * 
 * @author		Nguyen Huu Phuoc <thenextcms@gmail.com>
 * @copyright	Copyright (c) 2011 - 2012, Nguyen Huu Phuoc
 * @license		http://nextcms.org/license.txt	(GNU GPL version 2 or later)
 * @link		http://nextcms.org
 * @category	modules
 * @package		file
 * @subpackage	js
 * @since		1.0
 * @version		2012-06-18
 */

dojo.provide("file.js.controllers.AttachmentController");

dojo.require("core.js.base.controllers.ActionProvider");
dojo.require("core.js.base.controllers.Subscriber");
dojo.require("core.js.base.Encoder");
dojo.require("core.js.base.I18N");
dojo.require("core.js.base.views.Helper");

dojo.declare("file.js.controllers.AttachmentController", null, {
	// _id: String
	_id: null,
	
	// _i18n: Object
	_i18n: null,
	
	// _helper: core.js.base.views.Helper
	_helper: null,
	
	// _attachmentToolbar: file.js.views.AttachmentToolbar
	_attachmentToolbar: null,
	
	// _attachmentGrid: file.js.views.AttachmentGrid
	_attachmentGrid: null,
	
	// _paginatorContainer: String
	_paginatorContainer: null,
	
	// _defaultCriteria: Object
	_defaultCriteria: {
		keyword: null,
		page: 1,
		per_page: 20,
		language: null
	},
	
	// TOPIC_GROUP: [const] String
	TOPIC_GROUP: "/file/js/controllers/AttachmentController",
	
	constructor: function(/*String*/ id) {
		this._id = id;
		
		core.js.base.I18N.requireLocalization("file/languages");
		this._i18n = core.js.base.I18N.getLocalization("file/languages");
		
		// Create helper instance
		this._helper = new core.js.base.views.Helper(id);
		this._helper.setLanguageData(this._i18n);
		
		core.js.base.controllers.Subscriber.unsubscribe(this.TOPIC_GROUP);
	},
	
	setAttachmentToolbar: function(/*file.js.views.AttachmentToolbar*/ attachmentToolbar) {
		// summary:
		//		Sets the attachment toolbar
		this._attachmentToolbar = attachmentToolbar;
		
		// Add new attachment handler
		dojo.connect(attachmentToolbar, "onUploadAttachment", this, "uploadAttachment");
		core.js.base.controllers.Subscriber.subscribe(this.TOPIC_GROUP, "/app/file/attachment/add/onCancel", this, function() {
			this._helper.removePane();
		});
		core.js.base.controllers.Subscriber.subscribe(this.TOPIC_GROUP, "/app/file/attachment/add/onStart", this, function() {
			this._helper.showStandby();
		});
		core.js.base.controllers.Subscriber.subscribe(this.TOPIC_GROUP, "/app/file/attachment/add/onComplete", this, function(data) {
			this._helper.hideStandby();
			this._helper.removePane();
			dojo.publish("/app/global/notification", [{
				message: this._i18n.attachment.add[(data.length == 0) ? "error" : "success"],
				type: (data.length > 0) ? "message" : "error"
			}]);
			
			if (data.length > 0) {
				this.searchAttachments();
			}
		});
		
		// Refresh handler
		dojo.connect(attachmentToolbar, "onRefresh", this, "searchAttachments");
		
		// Search handler
		dojo.connect(attachmentToolbar, "onSearchAttachments", this, function(criteria) {
			criteria.page = 1;
			this.searchAttachments(criteria);
		});
		dojo.connect(attachmentToolbar, "onUpdatePageSize", this, function(perPage) {
			this.searchAttachments({
				page: 1,
				per_page: perPage
			});
		});
		
		// Switch to other language handler
		dojo.connect(attachmentToolbar, "onSwitchToLanguage", this, function(language) {
			this.searchAttachments({
				language: language,
				page: 1
			});
		});
		
		return this;	// file.js.controllers.AttachmentController
	},
	
	setAttachmentGrid: function(/*file.js.views.AttachmentGrid*/ attachmentGrid) {
		// summary:
		//		Sets the attachments grid
		this._attachmentGrid = attachmentGrid;
		
		// Edit attachment handler
		dojo.connect(attachmentGrid, "onEditAttachment", this, "editAttachment");
		core.js.base.controllers.Subscriber.subscribe(this.TOPIC_GROUP, "/app/file/attachment/edit/onCancel", this, function() {
			this._helper.removePane();
		});
		core.js.base.controllers.Subscriber.subscribe(this.TOPIC_GROUP, "/app/file/attachment/edit/onStart", this, function() {
			this._helper.showStandby();
		});
		core.js.base.controllers.Subscriber.subscribe(this.TOPIC_GROUP, "/app/file/attachment/edit/onComplete", this, function(data) {
			this._helper.hideStandby();
			this._helper.removePane();
			dojo.publish("/app/global/notification", [{
				message: this._i18n.attachment.edit[(data.length == 0) ? "error" : "success"],
				type: (data.length > 0) ? "message" : "error"
			}]);
			
			if (data.length > 0) {
				this.searchAttachments();
			}
		});
		
		// Delete attachment handler
		dojo.connect(attachmentGrid, "onDeleteAttachment", this, "deleteAttachment");
		core.js.base.controllers.Subscriber.subscribe(this.TOPIC_GROUP, "/app/file/attachment/delete/onCancel", this, function() {
			this._helper.closeDialog();
		});
		core.js.base.controllers.Subscriber.subscribe(this.TOPIC_GROUP, "/app/file/attachment/delete/onComplete", this, function(data) {
			this._helper.closeDialog();
			dojo.publish("/app/global/notification", [{
				message: this._i18n.attachment["delete"][(data.result == "APP_RESULT_OK") ? "success" : "error"],
				type: (data.result == "APP_RESULT_OK") ? "message" : "error"
			}]);
			
			if (data.result == "APP_RESULT_OK") {
				this.searchAttachments();
			}
		});
		
		// Create a copy of the attachment in other language handler
		dojo.connect(attachmentGrid, "onTranslateAttachment", this, "translateAttachment");
		
		return this;	// file.js.controllers.AttachmentController
	},
	
	setAttachmentPaginator: function(/*String*/ paginatorContainer) {
		// summary:
		//		Sets the container of paginator
		this._paginatorContainer = paginatorContainer;
		
		core.js.base.controllers.Subscriber.subscribe(this.TOPIC_GROUP, "/app/file/attachment/list/onGotoPage", this, function(page) {
			this.searchAttachments({
				page: page
			});
		});
		
		return this;	// file.js.controllers.AttachmentController
	},
	
	deleteAttachment: function(/*dojo.data.Item*/ item) {
		// summary:
		//		Deletes given attachment
		var params = {
			attachment_id: item.attachment_id[0]
		};
		var url = core.js.base.controllers.ActionProvider.get("file_attachment_delete").url + "?" + dojo.objectToQuery(params);
		this._helper.showDialog(url, {
			title: this._i18n.attachment["delete"].title,
			style: "width: 250px",
			refreshOnShow: true
		});
	},
	
	editAttachment: function(/*dojo.data.Item|String*/ item) {
		// summary:
		//		Edits given attachment
		var params = {
			attachment_id: dojo.isObject(item) ? item.attachment_id[0] : item
		};
		var url = core.js.base.controllers.ActionProvider.get("file_attachment_edit").url + "?" + dojo.objectToQuery(params);
		this._helper.showPane(url, {
			style: "width: 50%"
		});
	},
	
	uploadAttachment: function() {
		// summary:
		//		Uploads new attachment
		var params = {
			language: this._defaultCriteria.language
		};
		var url = core.js.base.controllers.ActionProvider.get("file_attachment_add").url + "?" + dojo.objectToQuery(params);
		this._helper.showPane(url, {
			style: "width: 50%"
		});
	},
	
	initSearchCriteria: function(/*Object*/ criteria) {
		// summary:
		//		Inits the controls with given criteria
		dojo.mixin(this._defaultCriteria, criteria);
		this._attachmentToolbar.initSearchCriteria(criteria);
		
		return this;	// file.js.controllers.AttachmentController
	},
	
	searchAttachments: function(/*Object*/ criteria) {
		// summary:
		//		Searches for attachments
		dojo.mixin(this._defaultCriteria, criteria);
		var q   = core.js.base.Encoder.encode(this._defaultCriteria);
		var url = core.js.base.controllers.ActionProvider.get("file_attachment_list").url;
		dojo.hash("u=" + url + "/?q=" + q);
		
		var _this = this;
		this._helper.showStandby();
		dojo.xhrPost({
			url: url,
			content: {
				q: q,
				format: "json"
			},
			handleAs: "json",
			load: function(data) {
				_this._helper.hideStandby();
				_this._attachmentGrid.showAttachments(data.data);
				
				// Update the paginator
				dijit.byId(_this._paginatorContainer).setContent(data.paginator);
			}
		});
	},
	
	translateAttachment: function(/*dojo.data.Item*/ item, /*String*/ language) {
		// summary:
		//		Translates given attachment item to other language
		var params = {
			source_id: item.attachment_id[0],
			language: language
		};
		var url = core.js.base.controllers.ActionProvider.get("file_attachment_add").url + "?" + dojo.objectToQuery(params);
		this._helper.showPane(url, {
			style: "width: 50%"
		});
	}
});
