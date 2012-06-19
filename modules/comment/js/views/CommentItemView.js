/**
 * NextCMS
 * 
 * @author		Nguyen Huu Phuoc <thenextcms@gmail.com>
 * @copyright	Copyright (c) 2011 - 2012, Nguyen Huu Phuoc
 * @license		http://nextcms.org/license.txt	(GNU GPL version 2 or later)
 * @link		http://nextcms.org
 * @category	modules
 * @package		comment
 * @subpackage	js
 * @since		1.0
 * @version		2012-03-28
 */

dojo.provide("comment.js.views.CommentItemView");

dojo.require("dojo.fx");

dojo.require("core.js.base.Encoder");
dojo.require("core.js.base.I18N");

dojo.declare("comment.js.views.CommentItemView", null, {
	// _domNode: DomNode
	_domNode: null,
	
	// _comment: Object
	_comment: null,
	
	// _i18n: Object
	_i18n: null,
	
	// _commentListView: comment.js.views.CommentListView
	_commentListView: null,
	
	constructor: function(/*DomNode*/ domNode, /*comment.js.views.CommentListView*/ commentListView) {
		this._domNode		  = domNode;
		this._commentListView = commentListView;
		this._comment		  = core.js.base.Encoder.decode(dojo.attr(domNode, "data-app-entity-props"));
		
		core.js.base.I18N.requireLocalization("comment/languages");
		this._i18n = core.js.base.I18N.getLocalization("comment/languages");
		
		this._init();
	},
	
	getComment: function() {
		// summary:
		//		Gets comment data
		return this._comment;	// Object
	},
	
	getDomNode: function() {
		// summary:
		//		Gets DomNode of the comment item
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
				this._commentListView.onMouseDown(this);
			}
		});
		
		this.setStatus(this._comment.status);
		
		// If there is quote content, create a new DIV to toggle the quote
		var quotes = dojo.query(".commentCommentQuote", this._domNode);
		var _this = this;
		dojo.forEach(quotes, function(quoteDiv) {
			var toggleDiv = dojo.create("div", {
				innerHTML: _this._i18n.global._share.showQuoteAction,
				className: "commentCommentToggleQuote"
			}, quoteDiv, "before");
			dojo.style(quoteDiv, "display", "none");
			
			dojo.connect(toggleDiv, "onclick", function() {
				var toggler = new dojo.fx.Toggler({
		            node: quoteDiv,
		            showFunc: dojo.fx.wipeIn,
		            hideFunc: dojo.fx.wipeOut
		        });
				var displayed = dojo.style(quoteDiv, "display");
				if (displayed == "none") {
					dojo.attr(toggleDiv, "innerHTML", _this._i18n.global._share.hideQuoteAction);
					toggler.show();
				} else {
					dojo.attr(toggleDiv, "innerHTML", _this._i18n.global._share.showQuoteAction);
					toggler.hide();
				}
			});
		});
	},
	
	setStatus: function(/*String*/ status) {
		// summary:
		//		Sets comment status
		// status:
		//		Comment's status, can be "activated", "not_activated", "spam"
		this._comment.status = status;
		dojo.removeClass(this._domNode, ["commentCommentItemActivated", "commentCommentItemNotActivated", "commentCommentItemSpam"]);
		
		switch (status) {
			case "activated":
				dojo.addClass(this._domNode, "commentCommentItemActivated"); 
				break;
			case "not_activated":
				dojo.addClass(this._domNode, "commentCommentItemNotActivated");
				break;
			case "spam":
				dojo.addClass(this._domNode, "commentCommentItemSpam");
				break;
			default:
				break;
		}
	}
});
