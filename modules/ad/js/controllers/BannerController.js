/**
 * NextCMS
 * 
 * @author		Nguyen Huu Phuoc <thenextcms@gmail.com>
 * @copyright	Copyright (c) 2011 - 2012, Nguyen Huu Phuoc
 * @license		http://nextcms.org/license.txt	(GNU GPL version 2 or later)
 * @link		http://nextcms.org
 * @category	modules
 * @package		ad
 * @subpackage	js
 * @since		1.0
 * @version		2012-05-16
 */

dojo.provide("ad.js.controllers.BannerController");

dojo.require("core.js.base.controllers.ActionProvider");
dojo.require("core.js.base.controllers.Subscriber");
dojo.require("core.js.base.Encoder");
dojo.require("core.js.base.I18N");
dojo.require("core.js.base.views.Helper");

dojo.declare("ad.js.controllers.BannerController", null, {
	// _id: String
	_id: null,
	
	// _i18n: Object
	_i18n: null,
	
	// _helper: core.js.base.views.Helper
	_helper: null,
	
	// _toolbar: ad.js.views.BannerToolbar
	_toolbar: null,
	
	// _bannerListView: ad.js.views.BannerListView
	_bannerListView: null,
	
	// _bannerContextMenu: ad.js.views.BannerContextMenu
	_bannerContextMenu: null,
	
	// _defaultCriteria: Object
	_defaultCriteria: {
		page: 1,
		per_page: 20
	},
	
	// TOPIC_GROUP: [const] String
	TOPIC_GROUP: "/ad/js/controllers/BannerController",
	
	constructor: function(/*String*/ id) {
		this._id = id;
		
		core.js.base.I18N.requireLocalization("ad/languages");
		this._i18n = core.js.base.I18N.getLocalization("ad/languages");
		
		// Create helper instance
		this._helper = new core.js.base.views.Helper(id);
		this._helper.setLanguageData(this._i18n);
		
		core.js.base.controllers.Subscriber.unsubscribe(this.TOPIC_GROUP);
	},
	
	setBannerToolbar: function(/*ad.js.views.BannerToolbar*/ toolbar) {
		// summary:
		//		Sets the banner toolbar
		this._toolbar = toolbar;
		
		// Add banner handler
		dojo.connect(toolbar, "onAddBanner", this, "addBanner");
		core.js.base.controllers.Subscriber.subscribe(this.TOPIC_GROUP, "/app/ad/banner/add/onCancel", this._helper, "removePane");
		core.js.base.controllers.Subscriber.subscribe(this.TOPIC_GROUP, "/app/ad/banner/add/onStart", this._helper, "showStandby");
		core.js.base.controllers.Subscriber.subscribe(this.TOPIC_GROUP, "/app/ad/banner/add/onComplete", this, function(data) {
			this._helper.hideStandby();
			this._helper.removePane();
			dojo.publish("/app/global/notification", [{
				message: this._i18n.banner.add[(data.result == "APP_RESULT_OK") ? "success" : "error"],
				type: (data.result == "APP_RESULT_OK") ? "message" : "error"
			}]);
			
			if (data.result == "APP_RESULT_OK") {
				this.searchBanners();
			}
		});
		
		// Refresh handler
		dojo.connect(toolbar, "onRefresh", this, "searchBanners");
		
		// Search handler
		dojo.connect(toolbar, "onSearchBanners", this, function(keyword) {
			this.searchBanners({
				keyword: keyword
			});
		});
		
		// Update page size handler
		dojo.connect(toolbar, "onUpdatePageSize", this, function(perPage) {
			this.searchBanners({
				per_page: perPage
			});
		});
		
		return this;	// ad.js.controllers.BannerController
	},
	
	setBannerListView: function(/*ad.js.views.BannerListView*/ bannerListView) {
		// summary:
		//		Sets the banner list view
		this._bannerListView = bannerListView;
		
		// Paging handler
		core.js.base.controllers.Subscriber.subscribe(this.TOPIC_GROUP, "/app/ad/banner/list/onGotoPage", this, function(page) {
			this.searchBanners({
				page: page
			});
		});
		
		dojo.connect(bannerListView, "onMouseDown", this, function(bannerItemView) {
			if (this._bannerContextMenu) {
				this._bannerContextMenu.show(bannerItemView);
			}
		});
		
		return this;	// ad.js.controllers.BannerController
	},
	
	setBannerContextMenu: function(/*ad.js.views.BannerContextMenu*/ bannerContextMenu) {
		// summary:
		//		Sets the banner context menu
		this._bannerContextMenu = bannerContextMenu;
		
		// Activate handler
		dojo.connect(bannerContextMenu, "onActivateBanner", this, "activateBanner");
		
		// Delete handler
		dojo.connect(bannerContextMenu, "onDeleteBanner", this, "deleteBanner");
		core.js.base.controllers.Subscriber.subscribe(this.TOPIC_GROUP, "/app/ad/banner/delete/onCancel", this, function() {
			this._helper.closeDialog();
		});
		core.js.base.controllers.Subscriber.subscribe(this.TOPIC_GROUP, "/app/ad/banner/delete/onComplete", this, function(data) {
			this._helper.closeDialog();
			
			dojo.publish("/app/global/notification", [{
				message: this._i18n.banner["delete"][(data.result == "APP_RESULT_OK") ? "success" : "error"],
				type: (data.result == "APP_RESULT_OK") ? "message" : "error"
			}]);
			
			if (data.result == "APP_RESULT_OK") {
				this.searchBanners();
			}
		});
		
		// Edit handler
		dojo.connect(bannerContextMenu, "onEditBanner", this, "editBanner");
		core.js.base.controllers.Subscriber.subscribe(this.TOPIC_GROUP, "/app/ad/banner/edit/onCancel", this, function() {
			this._helper.removePane();
		});
		core.js.base.controllers.Subscriber.subscribe(this.TOPIC_GROUP, "/app/ad/banner/edit/onStart", this, function() {
			this._helper.showStandby();
		});
		core.js.base.controllers.Subscriber.subscribe(this.TOPIC_GROUP, "/app/ad/banner/edit/onComplete", this, function(data) {
			this._helper.hideStandby();
			dojo.publish("/app/global/notification", [{
				message: this._i18n.banner.edit[(data.result == "APP_RESULT_OK") ? "success" : "error"],
				type: (data.result == "APP_RESULT_OK") ? "message" : "error"
			}]);
			
			if (data.result == "APP_RESULT_OK") {
				this.searchBanners();
			}
		});
		
		return this;	// ad.js.controllers.BannerController
	},
	
	activateBanner: function(/*ad.js.views.BannerItemView*/ bannerItemView) {
		// summary:
		//		Activates/deactivates given banner item
		var bannerId = bannerItemView.getBanner().banner_id;
		var status = bannerItemView.getBanner().status;
		var _this  = this;
		dojo.xhrPost({
			url: core.js.base.controllers.ActionProvider.get("ad_banner_activate").url,
			content: {
				banner_id: bannerId
			},
			handleAs: "json",
			load: function(data) {
				var message = (data.result == "APP_RESULT_OK") 
							  ? (status == "activated" ? "deactivateSuccess" : "activateSuccess") 
							  : (status == "activated" ? "deactivateError" : "activateError");
				dojo.publish("/app/global/notification", [{
					message: _this._i18n.banner.activate[message],
					type: (data.result == "APP_RESULT_OK") ? "message" : "error"
				}]);
				
				if (data.result == "APP_RESULT_OK") {
					var newStatus = (status == "activated") ? "not_activated" : "activated";
					bannerItemView.getBanner().status = newStatus;
				}
			}
		});
	},
	
	addBanner: function() {
		// summary:
		//		Adds new banner
		var url = core.js.base.controllers.ActionProvider.get("ad_banner_add").url;
		this._helper.showPane(url, {
			style: "width: 50%"
		});
	},
	
	deleteBanner: function(/*ad.js.views.BannerItemView*/ bannerItemView) {
		// summary:
		//		Deletes banner
		var params = {
			banner_id: bannerItemView.getBanner().banner_id
		};
		var url = core.js.base.controllers.ActionProvider.get("ad_banner_delete").url + "?" + dojo.objectToQuery(params);
		this._helper.showDialog(url, {
			title: this._i18n.banner["delete"].title,
			style: "width: 250px",
			refreshOnShow: true
		});
	},
	
	editBanner: function(/*ad.js.views.BannerItemView*/ bannerItemView) {
		// summary:
		//		Edits given banner
		var params = {
			banner_id: bannerItemView.getBanner().banner_id
		};
		var url = core.js.base.controllers.ActionProvider.get("ad_banner_edit").url + "?" + dojo.objectToQuery(params);
		this._helper.showPane(url, {
			style: "width: 50%"
		});
	},
	
	initSearchCriteria: function(/*Object*/ criteria) {
		// summary:
		//		Inits controls based on given criteria
		dojo.mixin(this._defaultCriteria, criteria);
		this._toolbar.initSearchCriteria(this._defaultCriteria);
	},
	
	searchBanners: function(/*Object*/ criteria) {
		// summary:
		//		Searches for banners by given criteria
		var _this = this;
		dojo.mixin(this._defaultCriteria, criteria);
		
		var q   = core.js.base.Encoder.encode(this._defaultCriteria);
		var url = core.js.base.controllers.ActionProvider.get("ad_banner_list").url;
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
				_this._bannerListView.setContent(data);
			}
		});
	}
});
