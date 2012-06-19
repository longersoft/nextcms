/**
 * NextCMS
 * 
 * @author		Nguyen Huu Phuoc <thenextcms@gmail.com>
 * @copyright	Copyright (c) 2011 - 2012, Nguyen Huu Phuoc
 * @license		http://nextcms.org/license.txt	(GNU GPL version 2 or later)
 * @link		http://nextcms.org
 * @category	modules
 * @package		content
 * @subpackage	js
 * @since		1.0
 * @version		2012-03-28
 */

dojo.provide("content.js.views.ArticleItemView");

dojo.require("core.js.base.Encoder");
dojo.require("core.js.Constant");

dojo.declare("content.js.views.ArticleItemView", null, {
	// _domNode: DomNode
	_domNode: null,
	
	// _articleListView: content.js.views.ArticleListView
	_articleListView: null,
	
	// _article: Object
	// 		Represent article's data
	_article: null,
	
	// _articleTitleNode: DomNode
	_articleTitleNode: null,
	
	// _articleCoverNode: DomNode
	_articleCoverNode: null,
	
	constructor: function(/*DomNode*/ domNode, /*content.js.views.ArticleListView*/ articleListView) {
		this._domNode		   = domNode;
		this._articleTitleNode = dojo.query(".contentArticleItemTitle", domNode)[0];
		this._articleCoverNode = dojo.query(".contentArticleItemCover img", domNode)[0];
		this._articleListView  = articleListView;
		
		this._article = core.js.base.Encoder.decode(dojo.attr(domNode, "data-app-entity-props"));
		
		var _this = this;
		this._articleCoverNode.onload = function() {
			dojo.style(_this._articleTitleNode, "maxWidth", this.width + "px");
		};
		
		this._init();
	},
	
	getDomNode: function() {
		return this._domNode;		// DomNode
	},
	
	getArticle: function() {
		// summary:
		//		Gets the article's properties
		return this._article;		// Object
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
				this._articleListView.onMouseDown(this);
			}
		});
	},
	
	setViewSize: function(/*String*/ size) {
		// summary:
		//		Shows the article in given size of thumbnail
		// size:
		//		The size of thumbnail, can be: square, thumbnail, small, crop, medium
		this._article.view_size = size;
		dojo.attr(this._articleCoverNode, "src", this._article[size]);
	},
	
	updateCoverThumbnails: function(/*Object*/ thumbnails) {
		// summary:
		//		Updates article's cover
		// thumbnails:
		//		Contains the thumbnails of cover
		var size = this._article.view_size;
		if (thumbnails[size]) {
			dojo.attr(this._articleCoverNode, "src", core.js.Constant.ROOT_URL + thumbnails[size]);
		}
		for (var thumb in thumbnails) {
			this._article[thumb] = core.js.Constant.ROOT_URL + thumbnails[thumb];
		}
	}
});
