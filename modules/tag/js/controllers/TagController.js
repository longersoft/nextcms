/**
 * NextCMS
 * 
 * @author		Nguyen Huu Phuoc <thenextcms@gmail.com>
 * @copyright	Copyright (c) 2011 - 2012, Nguyen Huu Phuoc
 * @license		http://nextcms.org/license.txt	(GNU GPL version 2 or later)
 * @link		http://nextcms.org
 * @category	modules
 * @package		tag
 * @subpackage	js
 * @since		1.0
 * @version		2012-06-18
 */

dojo.provide("tag.js.controllers.TagController");

dojo.require("core.js.base.controllers.ActionProvider");
dojo.require("core.js.base.controllers.Subscriber");
dojo.require("core.js.base.Encoder");
dojo.require("core.js.base.I18N");
dojo.require("core.js.base.views.Helper");

dojo.declare("tag.js.controllers.TagController", null, {
	// _id: String
	_id: null,
	
	// _i18n: Object
	_i18n: null,
	
	// _helper: core.js.base.views.Helper
	_helper: null,
	
	// _toolbar: tag.js.views.TagToolbar
	_toolbar: null,
	
	// _tagListView: tag.js.views.TagListView
	_tagListView: null,
	
	// _tagContextMenu: tag.js.views.TagContextMenu
	_tagContextMenu: null,
	
	// _defaultCriteria: Object
	_defaultCriteria: {
		keyword: null,
		language: null,
		page: 1,
		per_page: 200
	},
	
	// TOPIC_GROUP: [const] String
	TOPIC_GROUP: "/tag/js/controllers/TagController",
	
	constructor: function(/*String*/ id) {
		this._id = id;
		
		core.js.base.I18N.requireLocalization("tag/languages");
		this._i18n = core.js.base.I18N.getLocalization("tag/languages");
		
		// Create helper instance
		this._helper = new core.js.base.views.Helper(id);
		this._helper.setLanguageData(this._i18n);
		
		core.js.base.controllers.Subscriber.unsubscribe(this.TOPIC_GROUP);
	},
	
	setTagToolbar: function(/*tag.js.views.TagToolbar*/ toolbar) {
		// summary:
		//		Sets the tag toolbar
		this._toolbar = toolbar;
		
		// Add tag handler
		dojo.connect(toolbar, "onAddTag", this, "addTag");
		core.js.base.controllers.Subscriber.subscribe(this.TOPIC_GROUP, "/app/tag/tag/add/onCancel", this._helper, "closeDialog");
		core.js.base.controllers.Subscriber.subscribe(this.TOPIC_GROUP, "/app/tag/tag/add/onComplete", this, function(data) {
			dojo.publish("/app/global/notification", [{
				message: this._i18n.tag.add[(data.result == "APP_RESULT_OK") ? "success" : "error"],
				type: (data.result == "APP_RESULT_OK") ? "message" : "error"
			}]);
			
			if (data.result == "APP_RESULT_OK") {
				this.searchTags();
			}
		});
		
		// Refresh handler
		dojo.connect(toolbar, "onRefresh", this, "searchTags");
		
		// Search tag handler
		dojo.connect(toolbar, "onSearchTags", this, function(keyword) {
			this.searchTags({
				keyword: keyword,
				page: 1
			});
		});
		
		// Switch to other language handler
		dojo.connect(toolbar, "onSwitchToLanguage", this, function(language) {
			this.searchTags({
				language: language
			});
		});
		
		// Update page size handler
		dojo.connect(toolbar, "onUpdatePageSize", this, function(perPage) {
			this.searchTags({
				page: 1,
				per_page: perPage
			});
		});
		
		return this;	// tag.js.controllers.TagController 
	},
	
	setTagListView: function(/*tag.js.views.TagListView*/ tagListView) {
		// summary:
		//		Sets the tag list view
		this._tagListView = tagListView;
		
		// Show the context menu
		dojo.connect(tagListView, "onMouseDown", this, function(tagItemView) {
			if (this._tagContextMenu) {
				this._tagContextMenu.show(tagItemView);
			}
		});
		
		core.js.base.controllers.Subscriber.subscribe(this.TOPIC_GROUP, "/app/tag/tag/list/onGotoPage", this, function(page) {
			this.searchTags({
				page: page
			});
		});
		
		return this;	// tag.js.controllers.TagController
	},
	
	setTagContextMenu: function(/*tag.js.views.TagContextMenu*/ tagContextMenu) {
		// summary:
		//		Sets the context menu
		this._tagContextMenu = tagContextMenu;
		
		// Edit tag handler
		dojo.connect(tagContextMenu, "onEditTag", this, "editTag");
		core.js.base.controllers.Subscriber.subscribe(this.TOPIC_GROUP, "/app/tag/tag/edit/onCancel", this._helper, "closeDialog");
		core.js.base.controllers.Subscriber.subscribe(this.TOPIC_GROUP, "/app/tag/tag/edit/onComplete", this, function(data) {
			this._helper.closeDialog();
			dojo.publish("/app/global/notification", [{
				message: this._i18n.tag.edit[(data.result == "APP_RESULT_OK") ? "success" : "error"],
				type: (data.result == "APP_RESULT_OK") ? "message" : "error"
			}]);
			
			if (data.result == "APP_RESULT_OK") {
				this.searchTags();
			}
		});
		
		// Delete tag handler
		dojo.connect(tagContextMenu, "onDeleteTag", this, "deleteTag");
		core.js.base.controllers.Subscriber.subscribe(this.TOPIC_GROUP, "/app/tag/tag/delete/onCancel", this._helper, "closeDialog");
		core.js.base.controllers.Subscriber.subscribe(this.TOPIC_GROUP, "/app/tag/tag/delete/onComplete", this, function(data) {
			this._helper.closeDialog();
			dojo.publish("/app/global/notification", [{
				message: this._i18n.tag["delete"][(data.result == "APP_RESULT_OK") ? "success" : "error"],
				type: (data.result == "APP_RESULT_OK") ? "message" : "error"
			}]);
			
			if (data.result == "APP_RESULT_OK") {
				this.searchTags();
			}
		});
		
		return this;	// tag.js.controllers.TagController
	},
	
	initSearchCriteria: function(/*Object*/ criteria) {
		// summary:
		//		Inits the controls with given criteria
		dojo.mixin(this._defaultCriteria, criteria);
		this._toolbar.initSearchCriteria(criteria);
		
		return this;	// tag.js.controllers.TagController
	},
	
	addTag: function() {
		// summary:
		//		Adds new tag
		var url = core.js.base.controllers.ActionProvider.get("tag_tag_add").url;
		this._helper.showDialog(url, {
			title: this._i18n.tag.add.title,
			style: "width: 250px",
			refreshOnShow: true
		});
	},
	
	deleteTag: function(/*tag.js.views.TagItemView*/ tagItemView) {
		// summary:
		//		Deletes a tag
		var params = {
			tag_id: tagItemView.getTag().tag_id
		};
		var url = core.js.base.controllers.ActionProvider.get("tag_tag_delete").url + "?" + dojo.objectToQuery(params);
		this._helper.showDialog(url, {
			title: this._i18n.tag["delete"].title,
			style: "width: 250px",
			refreshOnShow: true
		});
	},
	
	editTag: function(/*tag.js.views.TagItemView*/ tagItemView) {
		// summary:
		//		Edits a tag
		var params = {
			tag_id: tagItemView.getTag().tag_id
		};
		var url = core.js.base.controllers.ActionProvider.get("tag_tag_edit").url + "?" + dojo.objectToQuery(params);
		this._helper.showDialog(url, {
			title: this._i18n.tag.edit.title,
			style: "width: 250px",
			refreshOnShow: true
		});
	},
	
	searchTags: function(/*Object*/ criteria) {
		// summary:
		//		Searches for tags
		dojo.mixin(this._defaultCriteria, criteria);
		var q   = core.js.base.Encoder.encode(this._defaultCriteria);
		var url = core.js.base.controllers.ActionProvider.get("tag_tag_list").url;
		dojo.hash("u=" + url + "/?q=" + q);
		
		var _this = this;
		this._helper.showStandby();
		dojo.xhrPost({
			url: url,
			content: {
				q: q,
				format: "html"
			},
			load: function(data) {
				_this._tagListView.setContent(data);
				_this._helper.hideStandby();
			}
		});
	}
});
