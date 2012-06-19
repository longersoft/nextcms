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
 * @version		2012-02-14
 */

dojo.provide("ad.js.controllers.LayoutController");

dojo.require("ad.js.controllers.LayoutMediator");
dojo.require("core.js.base.controllers.ActionProvider");

dojo.declare("ad.js.controllers.LayoutController", null, {
	// _id: String
	_id: null,
	
	// _i18n: Object
	_i18n: null,
	
	// _helper: core.js.base.views.Helper
	_helper: null,
	
	// _toolbar: ad.js.views.LayoutToolbar
	_toolbar: null,
	
	// _layoutContainer: ad.js.views.LayoutContainer
	_layoutContainer: null,
	
	// _page: Object
	//		The current page
	_page: null,
	
	// _mediator: ad.js.controllers.LayoutMediator
	_mediator: new ad.js.controllers.LayoutMediator(),
	
	constructor: function(/*String*/ id) {
		this._id = id;
		
		core.js.base.I18N.requireLocalization("ad/languages");
		this._i18n = core.js.base.I18N.getLocalization("ad/languages");
		
		// Create helper instance
		this._helper = new core.js.base.views.Helper(id);
		this._helper.setLanguageData(this._i18n);
	},
	
	setLayoutToolbar: function(/*ad.js.views.LayoutToolbar*/ toolbar) {
		// summary:
		//		Sets the layout toolbar
		this._toolbar = toolbar;
		this._mediator.setLayoutToolbar(toolbar);
	
		// Load page handler
		dojo.connect(toolbar, "onLoadPage", this, "loadPage");
		
		// Save layout handler
		dojo.connect(toolbar, "onSaveLayout", this, "saveLayout");
		
		return this;	// ad.js.controllers.LayoutController
	},
	
	setLayoutContainer: function(/*ad.js.views.LayoutContainer*/ layoutContainer) {
		// summary:
		//		Sets the layout container
		this._layoutContainer = layoutContainer;
		return this;	// // ad.js.controllers.LayoutController
	},
	
	loadPage: function(/*Object*/ page) {
		// summary:
		//		Loads the layout of page
		// page:
		//		The page data, including the following properties:
		//		- template: The template's name
		//		- page_id: The page's Id
		//		- layout: Layout of page
		//		- banners: Array of banners on the page
		if (!page.layout) {
			return;
		}
		this._page = page;
		this._layoutContainer.loadPage(page);
	},
	
	saveLayout: function() {
		// summary:
		//		Saves the banner and zone positions on the page
		if (!this._page) {
			return;
		}
		var banners = this._layoutContainer.getBanners();
		var _this   = this;
		this._helper.showStandby();
		
		dojo.xhrPost({
			url: core.js.base.controllers.ActionProvider.get("ad_banner_place").url,
			content: {
				page_id: this._page.page_id,
				banners: dojo.toJson(banners),
				format: "json"
			},
			handleAs: "json",
			load: function(data) {
				_this._helper.hideStandby();
				dojo.publish("/app/global/notification", [{
					message: _this._i18n.banner.place[(data.result == "APP_RESULT_OK") ? "success" : "error"],
					type: (data.result == "APP_RESULT_OK") ? "message" : "error"
				}]);
				
				if (data.result == "APP_RESULT_OK") {
					// Update the array of banners for the toolbar
					var templates = _this._toolbar.getTemplates();
					templates[_this._page.template][_this._page.page_id + ""].banners = banners;
				}
			}
		});
	}
});
