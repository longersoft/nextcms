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

dojo.provide("ad.js.views.BannerItemView");

dojo.require("core.js.base.Encoder");

dojo.declare("ad.js.views.BannerItemView", null, {
	// _domNode: DomNode
	_domNode: null,
	
	// _i18n: Object
	_i18n: null,
	
	// _banner: Object
	_banner: null,
	
	// _bannerListView: ad.js.views.BannerListView
	_bannerListView: null,
	
	constructor: function(/*DomNode*/ domNode, /*ad.js.views.BannerListView*/ bannerListView) {
		this._domNode		 = domNode;
		this._bannerListView = bannerListView;
		this._banner		 = core.js.base.Encoder.decode(dojo.attr(domNode, "data-app-entity-props"));
		this._init();
	},
	
	getBanner: function() {
		// summary:
		//		Gets banner data
		return this._banner;	// Object
	},
	
	getDomNode: function() {
		// summary:
		//		Gets DomNode of the banner item
		return this._domNode;	// DomNode
	},
	
	_init: function() {
		// summary:
		//		Initializes node
		var _this = this;
		
		dojo.connect(this._domNode, "oncontextmenu", function(e) {
			e.preventDefault();
		});
		dojo.connect(this._domNode, "onmousedown", this, function(e) {
			if (dojo.mouseButtons.isRight(e)) {
				e.preventDefault();
				this._bannerListView.onMouseDown(this);
			}
		});
	}
});
