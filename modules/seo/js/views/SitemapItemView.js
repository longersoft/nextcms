/**
 * NextCMS
 * 
 * @author		Nguyen Huu Phuoc <thenextcms@gmail.com>
 * @copyright	Copyright (c) 2011 - 2012, Nguyen Huu Phuoc
 * @license		http://nextcms.org/license.txt	(GNU GPL version 2 or later)
 * @link		http://nextcms.org
 * @category	modules
 * @package		seo
 * @subpackage	js
 * @since		1.0
 * @version		2012-03-28
 */

dojo.provide("seo.js.views.SitemapItemView");

dojo.require("core.js.base.Encoder");

dojo.declare("seo.js.views.SitemapItemView", null, {
	// _domNode: DomNode
	_domNode: null,
	
	// _sitemapItem: Object
	_sitemapItem: null,
	
	// _sitemapListView: seo.js.views.SitemapListView
	_sitemapListView: null,
	
	constructor: function(/*DomNode*/ domNode, /*seo.js.views.SitemapListView*/ sitemapListView) {
		this._domNode		  = domNode;
		this._sitemapListView = sitemapListView;
		this._sitemapItem	  = core.js.base.Encoder.decode(dojo.attr(domNode, "data-app-entity-props"));
		
		this._init();
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
				this._sitemapListView.onMouseDown(this);
			}
		});
		
		// Allow to Dnd
		dojo.addClass(this._domNode, "dojoDndItem");
		dojo.attr(this._domNode, "dndtype", "seoSitemapItemDnd");
	},
	
	getDomNode: function() {
		return this._domNode;	// DomNode
	},
	
	getSitemapItem: function() {
		return this._sitemapItem;	// Object
	},
	
	updateLink: function(/*String*/ link) {
		// summary:
		//		Updates the link attribute
		var a = dojo.query("a", this._domNode)[0];
		dojo.attr(a, {
			href: link,
			innerHTML: link
		});
	}
});
