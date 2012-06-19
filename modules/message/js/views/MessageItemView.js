/**
 * NextCMS
 * 
 * @author		Nguyen Huu Phuoc <thenextcms@gmail.com>
 * @copyright	Copyright (c) 2011 - 2012, Nguyen Huu Phuoc
 * @license		http://nextcms.org/license.txt	(GNU GPL version 2 or later)
 * @link		http://nextcms.org
 * @category	modules
 * @package		message
 * @subpackage	js
 * @since		1.0
 * @version		2012-03-28
 */

dojo.provide("message.js.views.MessageItemView");

dojo.require("dojo.fx");

dojo.require("core.js.base.Encoder");
dojo.require("core.js.base.I18N");

dojo.declare("message.js.views.MessageItemView", null, {
	// _domNode: DomNode
	_domNode: null,
	
	// _messageListView: message.js.views.MessageListView
	_messageListView: null,
	
	// _i18n: Object
	_i18n: null,
	
	// _message: Object
	_message: null,
	
	constructor: function(/*DomNode*/ domNode, /*message.js.views.MessageListView*/ messageListView) {
		this._domNode		  = domNode;
		this._messageListView = messageListView;
		this._message		  = core.js.base.Encoder.decode(dojo.attr(domNode, "data-app-entity-props"));
		
		core.js.base.I18N.requireLocalization("message/languages");
		this._i18n = core.js.base.I18N.getLocalization("message/languages");
		
		this._init();
	},
	
	getMessage: function() {
		// summary:
		//		Gets message data
		return this._message;	// Object
	},
	
	getDomNode: function() {
		// summary:
		//		Gets DomNode of the message item
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
				this._messageListView.onMouseDown(this);
			}
		});
		
		// If there is quote content, create a new DIV to toggle the quote
		var quotes = dojo.query(".messageQuote", this._domNode);
		var _this = this;
		dojo.forEach(quotes, function(quoteDiv) {
			var toggleDiv = dojo.create("div", {
				innerHTML: _this._i18n.message.view.showQuoteAction,
				className: "messageToggleQuote"
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
					dojo.attr(toggleDiv, "innerHTML", _this._i18n.message.view.hideQuoteAction);
					toggler.show();
				} else {
					dojo.attr(toggleDiv, "innerHTML", _this._i18n.message.view.showQuoteAction);
					toggler.hide();
				}
			});
		});
	}
});
