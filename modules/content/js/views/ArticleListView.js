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

dojo.provide("content.js.views.ArticleListView");

dojo.require("dojo.dnd.Source");

dojo.require("core.js.base.controllers.ActionProvider");
dojo.require("content.js.views.ArticleItemView");

dojo.declare("content.js.views.ArticleListView", null, {
	// _id: String
	_id: null,
	
	// _domNode: DomNode
	_domNode: null,
	
	// _articleItemMap: Object
	//		Map the article Id with article item
	_articleItemMap: {},
	
	// _viewSize: String
	//		The thumbnail size of articles in the list
	//		Can be "square", "thumbnail", "small", "crop", "medium" 
	_viewSize: "thumbnail",	
	
	constructor: function(/*String*/ id) {
		this._id	  = id;
		this._domNode = dojo.byId(id);
		this._init();
	},
	
	_init: function() {
		var _this = this;
		this._articleItemMap = {};
		dojo.query(".contentArticleItem", this._id).forEach(function(node, index, arr) {
			var articleItemView = new content.js.views.ArticleItemView(node, _this);
			_this._articleItemMap[articleItemView.getArticle().article_id + ""] = articleItemView;
		});
		
		if (core.js.base.controllers.ActionProvider.get("content_article_activate").isAllowed) {
			var containerNodes = dojo.query(".contentArticleItemsContainer", this._id);
			if (containerNodes.length > 0) {
				new dojo.dnd.AutoSource(containerNodes[0], {
					accept: [],
					selfAccept: false,
					selfCopy: false
				});
			}
		}
		
		if (core.js.base.controllers.ActionProvider.get("content_article_cover").isAllowed) {
			for (var articleId in this._articleItemMap) {
				// JS LESSON: By this way, I can get the articleItemView instance inside the onDropExternal() correctly
				(function() {
					var articleItemView = _this._articleItemMap[articleId + ""];
					var coverNode = dojo.query(".contentArticleItemCover", articleItemView.getDomNode())[0];
					new dojo.dnd.Target(coverNode, {
						accept: ["appDndImage"],
						onDropExternal: function(source, nodes, copy) {
							var thumbnails = dojo.attr(nodes[0], "data-app-dndthumbnails");
							_this.onUpdateCover(articleItemView, dojo.fromJson(thumbnails));
						}
					});
				})();
			}
		}
	},
	
	empty: function() {
		// summary:
		//		Empties the list view. It should be called when user empty the trash
		var nodes = dojo.query(".contentArticleListCounter", this._domNode);
		if (nodes.length > 0) {
			nodes[0].innerHTML = 0;
		}
		
		// Remove the paginator
		dojo.query(".appBottomToolbar", this._domNode).forEach(function(node) {
			dojo.destroy(node);
		});
		
		for (var articleId in this._articleItemMap) {
			this.removeArticleItemView(articleId);
		}
		this._articleItemMap = {};
	},
	
	getArticleItemView: function(/*String*/ articleId) {
		// summary:
		//		Gets the article item view by given Id of article
		return this._articleItemMap[articleId + ""];	// content.js.views.ArticleItemView 
	},
	
	getArticleItemViews: function() {
		// summary:
		//		Returns all the article item views in the order of DomNode
		var articleItemViews = [];
		var _this = this;
		dojo.query(".contentArticleItem", this._id).forEach(function(node, index, arr) {
			articleItemViews.push(new content.js.views.ArticleItemView(node, _this));
		});
		return articleItemViews;
	},
	
	increaseArticleCounter: function(/*Integer*/ increasingNumber) {
		// summary:
		//		Increases (or descreases) the number of articles in the list
		// increasingNumber:
		//		The number of articles that will be added to or removed from the list
		var nodes = dojo.query(".contentArticleListCounter", this._domNode);
		if (nodes.length > 0) {
			nodes[0].innerHTML = parseInt(nodes[0].innerHTML) + increasingNumber;
		}
	},
	
	removeArticleItemView: function(/*String*/ articleId) {
		// summary:
		//		Removes an article item from the list
		// articleId:
		//		Id of article
		var articleItemView = this._articleItemMap[articleId + ""];
		if (articleItemView) {
			delete this._articleItemMap[articleId + ""];
			dojo.destroy(articleItemView.getDomNode());
		}
	},
	
	setContent: function(/*String*/ html) {
		// summary:
		//		Reloads the entire list by HTML content
		// html:
		//		Entire HTML to show the list of articles
		dijit.byId(this._id).setContent(html);
		
		// Re-init
		this._init();
	},
	
	setViewSize: function(/*String*/ size) {
		// summary:
		//		Shows the article in given size of thumbnail
		// size:
		//		The size of thumbnail, can be: square, thumbnail, small, crop, medium
		this._viewSize = size;
		for (var articleId in this._articleItemMap) {
			this._articleItemMap[articleId + ""].setViewSize(size);
		}
	},
	
	////////// CALLBACKS //////////
	
	onMouseDown: function(/*content.js.views.ArticleItemView*/ articleItemView) {
		// summary:
		//		Called when user right-click an article item
		// articleItemView:
		//		The selected article item
		// tags:
		//		callback
	},
	
	onUpdateCover: function(/*content.js.views.ArticleItemView*/ articleItemView, /*Object*/ thumbnails) {
		// summary:
		//		Updates the article's cover
		// tags:
		//		callback
	}
});
