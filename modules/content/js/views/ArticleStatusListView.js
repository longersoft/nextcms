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
 * @version		2012-06-12
 */

dojo.provide("content.js.views.ArticleStatusListView");

dojo.require("dojo.dnd.Source");

dojo.require("core.js.base.controllers.ActionProvider");

dojo.declare("content.js.views.ArticleStatusListView", null, {
	// _id: String
	_id: null,
	
	// _trashId: String
	//		Id of div showing the trash
	_trashId: null,
	
	// _numDeletedArticles: Integer
	//		Number of deleted articles
	_numDeletedArticles: 0,
	
	// _domNode: DomNode
	_domNode: null,
	
	constructor: function(/*String*/ id, /*String*/ trashId) {
		this._id = id;
		this._trashId = trashId;
		this._domNode = dojo.byId(id);
		
		this._init();
	},

	_init: function() {
		var _this = this;
		
		// Set trash icon based on the number of deleted articles
		this._numDeletedArticles = parseInt(dojo.attr(this._trashId, "data-app-deleted-articles"));
		dojo.addClass(this._trashId, this._numDeletedArticles == 0 ? "appTrashBigEmpty" : "appTrashBigFull");
		
		dojo.connect(dojo.byId(this._trashId), "onclick", this, function() {
			dojo.query(".contentArticleStatusSelected", this._domNode).removeClass("contentArticleStatusSelected");
			this.onStatusSelected("deleted");
		});
		
		dojo.query(".contentArticleStatusLabel", this._domNode).forEach(function(node, index, attr) {
			dojo.connect(node, "onclick", function(e) {
				dojo.query(".contentArticleStatusSelected", _this._domNode).removeClass("contentArticleStatusSelected");
				dojo.addClass(node.parentNode, "contentArticleStatusSelected");
				
				var status = dojo.attr(this, "data-app-status");
				if (status == "") {
					status = null;
				}
				_this.onStatusSelected(status);
			});
		});
		
		// Allow to drag and drop articles to the status item to update status
		if (core.js.base.controllers.ActionProvider.get("content_article_activate").isAllowed) {
			dojo.query("li", this._domNode).forEach(function(node) {
				var statusNode = dojo.query("a.contentArticleStatusLabel", node)[0];
				var status = dojo.attr(statusNode, "data-app-status");
				
				if (status != "") {
					new dojo.dnd.Target(node, {
						accept: ["contentArticleItemDnd"],
						onDropExternal: function(source, nodes, copy) {
							_this.onUpdateStatus(status, nodes);
						}
					});
				}
			});
		}
		
		if (core.js.base.controllers.ActionProvider.get("content_article_delete").isAllowed) {
			new dojo.dnd.Target(this._trashId, {
				accept: ["contentArticleItemDnd"],
				onDropExternal: function(source, nodes, copy) {
					_this.onDeleteArticles(nodes);
				}
			});
		}
	},
	
	increaseArticleCounter: function(/*String*/ status, /*Integer*/ increasingNumber) {
		// summary:
		//		Increases (or descreases) the number of articles which have the same status
		if (status == "deleted") {
			this._setNumDeletedArticles(parseInt(this._numDeletedArticles) + parseInt(increasingNumber));
		} else {
			this._updateArticleCounter(status, increasingNumber);
			
			// Update the counter of "View all" node
			this._updateArticleCounter("", increasingNumber);
		}
	},
	
	setTrashEmpty: function() {
		// summary:
		//		Sets the trash as empty
		this._setNumDeletedArticles(0);
	},
	
	_updateArticleCounter: function(/*String*/ status, /*Integer*/ increasingNumber) {
		// summary:
		//		Updates the article counter
		var statusItemNodes = dojo.query('.contentArticleStatusLabel[data-app-status="' + status + '"]', this._domNode);
		if (statusItemNodes.length > 0) {
			var counterNode = dojo.query(".contentArticleStatusCounter", statusItemNodes[0].parentNode)[0];
			var numArticles	= parseInt(counterNode.innerHTML);
			counterNode.innerHTML = numArticles + increasingNumber;
		}
	},
	
	getNumDeletedArticles: function() {
		// summary:
		//		Gets the number of articles in the trash
		return this._numDeletedArticles;	// Integer
	},
	
	_setNumDeletedArticles: function(/*Integer*/ numDeletedArticles) {
		// summary:
		//		Sets the number of deleted articles
		this._numDeletedArticles = numDeletedArticles;
		
		dojo.attr(this._trashId, "data-app-articles", this._numDeletedArticles);
		dojo.removeClass(this._trashId, ["appTrashBigEmpty", "appTrashBigFull"]);
		dojo.addClass(this._trashId, this._numDeletedArticles == 0 ? "appTrashBigEmpty" : "appTrashBigFull");
		
		this.onSetNumDeletedArticles(this._numDeletedArticles);
	},
	
	setArticleCounter: function(/*String*/ status, /*Integer*/ numArticles) {
		// summary:
		//		Sets and shows the number of articles which have the same status
		if (status == null) {
			status = "";
		}
		switch (true) {
			case (status == "deleted"):
				// Update the trash icon
				this._setNumDeletedArticles(numArticles);
				break;
			case (status == ""):
			case (status == "activated"):
			case (status == "not_activated"):
				var statusItemNodes = dojo.query('.contentArticleStatusLabel[data-app-status="' + status + '"]', this._domNode);
				if (statusItemNodes.length > 0) {
					var counterNode = dojo.query(".contentArticleStatusCounter", statusItemNodes[0].parentNode)[0];
					counterNode.innerHTML = numArticles;
				}
				break;
			default:
				break;
		}
	},
	
	countArticles: function(/*String*/ language) {
		var _this = this;
		dojo.xhrPost({
			url: core.js.base.controllers.ActionProvider.get("content_article_count").url,
			content: {
				language: language
			},
			handleAs: "json",
			load: function(data) {
				for (var status in data) {
					_this.setArticleCounter("total" == status ? null : status, data[status]);
				}
			}
		});
	},
	
	////////// CALLBACKS //////////
	
	onDeleteArticles: function(/*DomNode[]*/ articleNodes) {
		// summary:
		//		Deletes given articles
		// tags:
		//		callback
	},
	
	onSetNumDeletedArticles: function() {
		// summary:
		//		Called after setting the number of deleted articles
		// tags:
		//		callback
	},
	
	onStatusSelected: function(/*String?*/ status) {
		// summary:
		//		Called when selecting a status item
		// tags:
		//		callback
	},
	
	onUpdateStatus: function(/*String*/ status, /*DomNode[]*/ articleNodes) {
		// summary:
		//		Updates status of multiple article items
		// tags:
		//		callback
	}
});
