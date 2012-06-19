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
 * @version		2012-03-28
 */

dojo.provide("ad.js.views.BannerListView");

dojo.require("ad.js.views.BannerItemView");

dojo.declare("ad.js.views.BannerListView", null, {
	// _id: String
	_id: null,
	
	// _domNode: DomNode
	_domNode: null,
	
	constructor: function(/*String*/ id) {
		this._id 	  = id;
		this._domNode = dojo.byId(id);
		this._init();
	},
	
	_init: function() {
		var _this = this;
		dojo.query(".adBannerItem", this._id).forEach(function(node, index, arr) {
			var bannerItemView = new ad.js.views.BannerItemView(node, _this);
		});
	},
	
	setContent: function(/*String*/ html) {
		// summary:
		//		Reloads the entire list by HTML content
		// html:
		//		Entire HTML to show the list of banners
		dijit.byId(this._id).setContent(html);
		
		// Re-init
		this._init();
	},
	
	////////// CALLBACKS //////////
	
	onMouseDown: function(/*ad.js.views.BannerItemView*/ bannerItemView) {
		// summary:
		//		Called when user right-click a banner item
		// bannerItemView:
		//		The selected banner item
		// tags:
		//		callback
	}
});
