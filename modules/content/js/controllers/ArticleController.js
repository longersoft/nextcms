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
 * @version		2012-06-18
 */

dojo.provide("content.js.controllers.ArticleController");

dojo.require("dojox.string.sprintf");

dojo.require("core.js.base.controllers.ActionProvider");
dojo.require("core.js.base.controllers.Subscriber");
dojo.require("core.js.base.Encoder");
dojo.require("core.js.base.I18N");
dojo.require("core.js.base.views.Helper");
dojo.require("content.js.controllers.ArticleMediator");

dojo.declare("content.js.controllers.ArticleController", null, {
	// _id: String
	_id: null,
	
	// _i18n: Object
	_i18n: null,
	
	// _helper: core.js.base.views.Helper
	_helper: null,
	
	// _articleToolbar: content.js.views.ArticleToolbar
	_articleToolbar: null,
	
	// _articleListView: content.js.views.ArticleListView
	_articleListView: null,
	
	// _articleContextMenu: content.js.views.ArticleContextMenu
	_articleContextMenu: null,
	
	// _articleSearchCriteria: Object
	_articleSearchCriteria: {
		category_id: null,
		folder_id: null,
		status: null,
		page: 1,
		view_size: "thumbnail",
		per_page: 20,
		language: null
	},
	
	// _articleMediator: content.js.controllers.ArticleMediator
	_articleMediator: new content.js.controllers.ArticleMediator(),
	
	// TOPIC_GROUP: [const] String
	TOPIC_GROUP: "/content/js/controllers/ArticleController",
	
	constructor: function(/*String*/ id) {
		this._id = id;
		
		core.js.base.I18N.requireLocalization("content/languages");
		this._i18n = core.js.base.I18N.getLocalization("content/languages");
		
		// Create helper instance
		this._helper = new core.js.base.views.Helper(id);
		this._helper.setLanguageData(this._i18n);
		
		core.js.base.controllers.Subscriber.unsubscribe(this.TOPIC_GROUP);
	},
	
	setArticleToolbar: function(/*content.js.views.ArticleToolbar*/ toolbar) {
		// summary:
		//		Sets the article toolbar
		this._articleToolbar = toolbar;
		this._articleMediator.setArticleToolbar(toolbar);
		
		// Add article handler
		dojo.connect(toolbar, "onAddArticle", this, "addArticle");
		core.js.base.controllers.Subscriber.subscribe(this.TOPIC_GROUP, "/app/content/article/add/onCancel", this, function() {
			this._helper.removePane();
		});
		core.js.base.controllers.Subscriber.subscribe(this.TOPIC_GROUP, "/app/content/article/add/onStart", this, function() {
			this._helper.showStandby();
		});
		core.js.base.controllers.Subscriber.subscribe(this.TOPIC_GROUP, "/app/content/article/add/onComplete", this, function(data) {
			this._helper.hideStandby();
			dojo.publish("/app/global/notification", [{
				message: this._i18n.article.add[(data.result == "APP_RESULT_OK") ? "success" : "error"],
				type: (data.result == "APP_RESULT_OK") ? "message" : "error"
			}]);
			
			if (data.result == "APP_RESULT_OK") {
				dojo.publish("/app/content/article/add/onSuccess", [ data ]);
				this.searchArticles();
			}
		});
		
		// Refresh handler
		dojo.connect(toolbar, "onRefresh", this, "searchArticles");
		
		// Save order of articles
		dojo.connect(toolbar, "onSaveOrder", this, "saveArticleOrder");
		
		// Empty trash handler
		dojo.connect(toolbar, "onEmptyTrash", this, "emptyTrash");
		core.js.base.controllers.Subscriber.subscribe(this.TOPIC_GROUP, "/app/content/article/empty/onCancel", this, function() {
			this._helper.closeDialog();
		});
		core.js.base.controllers.Subscriber.subscribe(this.TOPIC_GROUP, "/app/content/article/empty/onComplete", this, function(data) {
			this._helper.closeDialog();
			dojo.publish("/app/global/notification", [{
				message: this._i18n.article.empty[(data.result == "APP_RESULT_OK") ? "success" : "error"],
				type: (data.result == "APP_RESULT_OK") ? "message" : "error"
			}]);
			
			if (data.result == "APP_RESULT_OK") {
				dojo.publish("/app/content/article/empty/onSuccess");
			}
		});
		
		// Search handler
		dojo.connect(toolbar, "onSearchArticles", this, function(keyword) {
			this.searchArticles({
				keyword: keyword,
				page: 1
			});
		});
		
		// View size handler
		dojo.connect(toolbar, "onViewSize", this, function(size) {
			this._articleSearchCriteria.view_size = size;
			if (this._articleListView) {
				this._articleListView.setViewSize(size);
			}
		});
		
		// Update page size handler
		dojo.connect(toolbar, "onUpdatePageSize", this, function(perPage) {
			if (this._articleSearchCriteria.per_page != perPage) {
				this.searchArticles({
					page: 1,
					per_page: perPage
				});
			}
		});
		
		return this;	// content.js.controllers.ArticleController
	},
	
	setArticleListView: function(/*content.js.views.ArticleListView*/ articleListView) {
		// summary:
		//		Sets the articles list view
		this._articleListView = articleListView;
		
		// Update cover handler
		dojo.connect(articleListView, "onUpdateCover", this, "updateCover");
		
		// Show context menu
		dojo.connect(articleListView, "onMouseDown", this, function(articleItemView) {
			if (this._articleContextMenu) {
				this._articleContextMenu.show(articleItemView);
			}
		});
		
		// Paging handler
		core.js.base.controllers.Subscriber.subscribe(this.TOPIC_GROUP, "/app/content/article/list/onGotoPage", this, function(page) {
			this.searchArticles({
				page: page
			});
		});
		
		// Load articles when selecting a category
		core.js.base.controllers.Subscriber.subscribe(this.TOPIC_GROUP, "/app/category/controllers/CategoryController/onSelectCategory_content", this, function(categoryId) {
			this.searchArticles({
				category_id: categoryId,
				folder_id: null
			});
		});
		
		// Load articles when switching category to other language
		core.js.base.controllers.Subscriber.subscribe(this.TOPIC_GROUP, "/app/category/controllers/CategoryController/onSwitchToLanguage_content", this, function(language) {
			this.searchArticles({
				category_id: null,
				folder_id: null,
				language: language
			});
		});
		
		// Update total number of articles after activating an user
		core.js.base.controllers.Subscriber.subscribe(this.TOPIC_GROUP, "/app/content/article/activate/onSuccess", this, function(data) {
			if (this._articleSearchCriteria.status) {
				this._articleListView.removeArticleItemView(data.article_id);
				this._articleListView.increaseArticleCounter(-1);
			}
		});
		
		// Remove article item from the list view after deleting an article
		core.js.base.controllers.Subscriber.subscribe(this.TOPIC_GROUP, "/app/content/article/delete/onSuccess", this, function(data) {
			this._articleListView.removeArticleItemView(data.article_id);
			this._articleListView.increaseArticleCounter(-1);
		});
		
		core.js.base.controllers.Subscriber.subscribe(this.TOPIC_GROUP, "/app/content/article/empty/onSuccess", this, function() {
			if (this._articleSearchCriteria.status == "deleted") {
				// Empty the list
				this._articleListView.empty();
			}
		});
		
		// Drop the article to tree handler
		core.js.base.controllers.Subscriber.subscribe(this.TOPIC_GROUP, "/app/category/controllers/CategoryController/onDropExternalSource_content", this, function(data) {
			this.moveToCategory(data.source, data.category);
		});
		
		// Drop article to folder handler
		if (core.js.base.controllers.ActionProvider.get("content_folder_add").isAllowed) {
			core.js.base.controllers.Subscriber.subscribe(this.TOPIC_GROUP, "/app/category/controllers/FolderController/onDropItems_Content_Models_Article", this, function(data) {
				this.addToFolder(data.folderItemView.getFolder(), data.nodes);
			});
		}
		
		// Select folder handler
		core.js.base.controllers.Subscriber.subscribe(this.TOPIC_GROUP, "/app/category/controllers/FolderController/onClickFolder_Content_Models_Article", this, function(folderItemView) {
			this.searchArticles({
				category_id: null,
				folder_id: folderItemView.getFolder().folder_id
			});
		});
		
		return this;	// content.js.controllers.ArticleController
	},
	
	setArticleContextMenu: function(/*content.js.views.ArticleContextMenu*/ articleContextMenu) {
		// summary:
		//		Sets the article's context menu
		this._articleContextMenu = articleContextMenu;
		this._articleMediator.setArticleContextMenu(articleContextMenu);
		
		// Activate handler
		dojo.connect(articleContextMenu, "onActivateArticle", this, "activateArticle");
		
		// Delete handler
		dojo.connect(articleContextMenu, "onDeleteArticle", this, "deleteArticle");
		core.js.base.controllers.Subscriber.subscribe(this.TOPIC_GROUP, "/app/content/article/delete/onCancel", this, function() {
			this._helper.closeDialog();
		});
		core.js.base.controllers.Subscriber.subscribe(this.TOPIC_GROUP, "/app/content/article/delete/onComplete", this, function(data) {
			this._helper.closeDialog();
			dojo.publish("/app/global/notification", [{
				message: this._i18n.article["delete"][(data.result == "APP_RESULT_OK") ? "success" : "error"],
				type: (data.result == "APP_RESULT_OK") ? "message" : "error"
			}]);
			
			if (data.result == "APP_RESULT_OK") {
				dojo.publish("/app/content/article/delete/onSuccess", [ data ]);
			}
		});
		
		// Edit handler
		dojo.connect(articleContextMenu, "onEditArticle", this, "editArticle");
		core.js.base.controllers.Subscriber.subscribe(this.TOPIC_GROUP, "/app/content/article/edit/onCancel", this, function() {
			this._helper.removePane();
		});
		core.js.base.controllers.Subscriber.subscribe(this.TOPIC_GROUP, "/app/content/article/edit/onStart", this, function() {
			this._helper.showStandby();
		});
		core.js.base.controllers.Subscriber.subscribe(this.TOPIC_GROUP, "/app/content/article/edit/onComplete", this, function(data) {
			this._helper.hideStandby();
			dojo.publish("/app/global/notification", [{
				message: this._i18n.article.edit[(data.result == "APP_RESULT_OK") ? "success" : "error"],
				type: (data.result == "APP_RESULT_OK") ? "message" : "error"
			}]);
			
			if (data.result == "APP_RESULT_OK") {
				this.searchArticles();
			}
		});
		
		// Remove from folder handler
		dojo.connect(articleContextMenu, "onRemoveFromFolder", this, "removeFromFolder");
		
		// View revisions
		dojo.connect(articleContextMenu, "onViewRevisions", this, "viewRevisions");
		core.js.base.controllers.Subscriber.subscribe(this.TOPIC_GROUP, "/app/content/revision/list/onClose", this, function() {
			this._helper.removePane();
		});
		// Refresh the list of article after restoring a revision
		core.js.base.controllers.Subscriber.subscribe(this.TOPIC_GROUP, "/app/content/revision/restore/onComplete", this, function(data) {
			if (data.result == "APP_RESULT_OK") {
				this.searchArticles();
			}
		});
		
		// Translate article handler
		dojo.connect(articleContextMenu, "onTranslateArticle", this, "translateArticle");
		
		return this;	// content.js.controllers.ArticleController
	},
	
	activateArticle: function(/*content.js.views.ArticleItemView*/ articleItemView) {
		// summary:
		//		Activates/deactivates given article item
		var articleId = articleItemView.getArticle().article_id;
		var status = articleItemView.getArticle().status;
		var _this  = this;
		dojo.xhrPost({
			url: core.js.base.controllers.ActionProvider.get("content_article_activate").url,
			content: {
				article_id: articleId
			},
			handleAs: "json",
			load: function(data) {
				var message = (data.result == "APP_RESULT_OK") 
							  ? (status == "activated" ? "deactivateSuccess" : "activateSuccess") 
							  : (status == "activated" ? "deactivateError" : "activateError");
				dojo.publish("/app/global/notification", [{
					message: _this._i18n.article.activate[message],
					type: (data.result == "APP_RESULT_OK") ? "message" : "error"
				}]);
				
				if (data.result == "APP_RESULT_OK") {
					var newStatus = (status == "activated") ? "not_activated" : "activated";
					articleItemView.getArticle().status = newStatus;
					dojo.publish("/app/content/article/activate/onSuccess", [{ article_id: articleId, oldStatus: status, newStatus: newStatus }]);
				}
			}
		});
	},
	
	addArticle: function() {
		// summary:
		//		Adds new article
		var url = core.js.base.controllers.ActionProvider.get("content_article_add").url;
		this._helper.showPane(url);
	},
	
	deleteArticle: function(/*content.js.views.ArticleItemView*/ articleItemView) {
		// summary:
		//		Deletes given article item
		var params = {
			article_id: articleItemView.getArticle().article_id
		};
		var url = core.js.base.controllers.ActionProvider.get("content_article_delete").url + "?" + dojo.objectToQuery(params);
		this._helper.showDialog(url, {
			title: this._i18n.article["delete"].title,
			style: "width: 250px",
			refreshOnShow: true
		});
	},
	
	editArticle: function(/*content.js.views.ArticleItemView*/ articleItemView) {
		// summary:
		//		Edits given article item
		var params = {
			article_id: articleItemView.getArticle().article_id
		};
		var url = core.js.base.controllers.ActionProvider.get("content_article_edit").url + "?" + dojo.objectToQuery(params);
		this._helper.showPane(url);
	},
	
	emptyTrash: function() {
		// summary:
		//		Empties the trash
		var url = core.js.base.controllers.ActionProvider.get("content_article_empty").url;
		this._helper.showDialog(url, {
			title: this._i18n.article.empty.title,
			style: "width: 250px",
			refreshOnShow: true
		});
	},
	
	moveToCategory: function(/*Object*/ articleItem, /*Object*/ categoryItem) {
		// summary:
		//		Called when dropping article item to the category tree
		var _this = this;
		this._helper.showStandby();
		dojo.xhrPost({
			url: core.js.base.controllers.ActionProvider.get("content_article_move").url,
			content: {
				article_id: articleItem.article_id,
				category_id: categoryItem.category_id
			},
			handleAs: "json",
			load: function(data) {
				_this._helper.hideStandby();
				
				var message = (data.result == "APP_RESULT_OK") ? "success" : "error";
				message = dojox.string.sprintf(_this._i18n.article.move[message], articleItem.title, categoryItem.name);
				dojo.publish("/app/global/notification", [{
					message: message,
					type: (data.result == "APP_RESULT_OK") ? "message" : "error"
				}]);
				
				if (data.result == "APP_RESULT_OK") {
					if (_this._articleSearchCriteria.category_id == null) {
						// Reload the list of articles
						_this.searchArticles();
					} else if (data.category_id != _this._articleSearchCriteria.category_id) {
						// Remove article item from the list
						_this._articleListView.removeArticleItemView(data.article_id);
						_this._articleListView.increaseArticleCounter(-1);
					}
				}
			}
		});
	},
	
	initArticleSearchCriteria: function(/*Object*/ criteria) {
		// summary:
		//		Inits the controls with given criteria
		dojo.mixin(this._articleSearchCriteria, criteria);
		this._articleToolbar.initSearchCriteria(this._articleSearchCriteria);
		this._articleMediator.initArticleSearchCriteria(this._articleSearchCriteria);
	},
	
	saveArticleOrder: function() {
		// summary:
		//		Saves the order of articles in the selected category
		var articleItemViews = this._articleListView.getArticleItemViews(),
			data = [],
			startIndex = this._articleSearchCriteria.per_page * (this._articleSearchCriteria.page - 1) + 1;  
		for (var i = 0; i < articleItemViews.length; i++) {
			data.push({
				article_id: articleItemViews[i].getArticle().article_id,
				category_id: this._articleSearchCriteria.category_id,
				index: startIndex + i
			});
		}
		
		this._helper.showStandby();
		var _this = this;
		dojo.xhrPost({
			url: core.js.base.controllers.ActionProvider.get("content_article_order").url,
			content: {
				data: dojo.toJson(data)
			},
			handleAs: "json",
			load: function(data) {
				_this._helper.hideStandby();
				dojo.publish("/app/global/notification", [{
					message: _this._i18n.article.order[(data.result == "APP_RESULT_OK") ? "success" : "error"],
					type: (data.result == "APP_RESULT_OK") ? "message" : "error"
				}]);
			}
		});
	},
	
	searchArticles: function(/*Object*/ criteria) {
		// summary:
		//		Searches for articles
		var _this = this;
		this._helper.closeDialog();
		
		dojo.mixin(this._articleSearchCriteria, criteria);
		var q   = core.js.base.Encoder.encode(this._articleSearchCriteria);
		var url = core.js.base.controllers.ActionProvider.get("content_article_list").url;
		dojo.hash("u=" + url + "/?q=" + q);
		
		this._helper.showStandby();
		dojo.xhrPost({
			url: url,
			content: {
				q: q,
				format: "html"
			},
			load: function(data) {
				_this._articleListView.setContent(data);
				_this._helper.hideStandby();
			}
		});
		
		// Update the counter
		this._statusListView.countArticles(this._articleSearchCriteria.language);
	},
	
	translateArticle: function(/*content.js.views.ArticleItemView*/ articleItemView, /*String*/ language) {
		// summary:
		//		Translates given article item to other language
		var params = {
			source_id: articleItemView.getArticle().article_id,
			language: language
		};
		var url = core.js.base.controllers.ActionProvider.get("content_article_add").url + "?" + dojo.objectToQuery(params);
		this._helper.showPane(url);
	},
	
	updateCover: function(/*content.js.views.ArticleItemView*/ articleItemView, /*Object*/ thumbnails) {
		// summary:
		//		Updates article's cover
		var articleId = articleItemView.getArticle().article_id;
		
		var _this = this;
		this._helper.showStandby();
		dojo.xhrPost({
			url: core.js.base.controllers.ActionProvider.get("content_article_cover").url,
			content: {
				article_id: articleId,
				thumbnails: dojo.toJson(thumbnails)
			},
			handleAs: "json",
			load: function(data) {
				_this._helper.hideStandby();
				dojo.publish("/app/global/notification", [{
					message: _this._i18n.article.cover[(data.result == "APP_RESULT_OK") ? "success" : "error"],
					type: (data.result == "APP_RESULT_OK") ? "message" : "error"
				}]);
				
				if (data.result == "APP_RESULT_OK") {
					articleItemView.updateCoverThumbnails(thumbnails);
				}
			}
		});
	},
	
	viewRevisions: function(/*content.js.views.ArticleItemView*/ articleItemView) {
		// summary:
		//		Lists revisions of given article
		var params = {
			article_id: articleItemView.getArticle().article_id
		};
		var url = core.js.base.controllers.ActionProvider.get("content_revision_list").url + "?" + dojo.objectToQuery(params);
		this._helper.showPane(url);
	},
	
	////////// STATUS FILTER //////////
	
	// _statusListView: content.js.views.ArticleStatusListView
	_statusListView: null,
	
	setStatusListView: function(/*content.js.views.ArticleStatusListView*/ statusListView) {
		// summary:
		//		Sets the status list view
		this._statusListView = statusListView;
		this._articleMediator.setStatusListView(statusListView);
		
		// Filter articles by status
		dojo.connect(statusListView, "onStatusSelected", this, function(status) {
			this.searchArticles({
				status: status
			});
		});
		
		// Update number of articles after updating article's status
		core.js.base.controllers.Subscriber.subscribe(this.TOPIC_GROUP, "/app/content/article/activate/onSuccess", this, function(data) {
			statusListView.increaseArticleCounter(data.oldStatus, -1);
			statusListView.increaseArticleCounter(data.newStatus, 1);
		});
		
		// after deleting an article
		core.js.base.controllers.Subscriber.subscribe(this.TOPIC_GROUP, "/app/content/article/delete/onSuccess", this, function(data) {
			if (data.status) {
				statusListView.increaseArticleCounter(data.status, -1);
				if (data.status != "deleted") {
					statusListView.increaseArticleCounter("deleted", 1);
				}
			}
		});
		
		dojo.connect(statusListView, "onUpdateStatus", this, "updateStatus");
		dojo.connect(statusListView, "onDeleteArticles", this, "deleteArticles");
		
		core.js.base.controllers.Subscriber.subscribe(this.TOPIC_GROUP, "/app/content/article/empty/onSuccess", this, function() {
			statusListView.setTrashEmpty();
		});
		
		// after switching to other language
		core.js.base.controllers.Subscriber.subscribe(this.TOPIC_GROUP, "/app/category/controllers/CategoryController/onSwitchToLanguage_content", this, function(language) {
			statusListView.countArticles(language);
		});
		
		return this;	// content.js.controllers.ArticleController
	},
	
	deleteArticles: function(/*DomNode[]*/ articleNodes) {
		// summary:
		//		Called when moving article items to the trash
		this._helper.showStandby();
		var url = core.js.base.controllers.ActionProvider.get("content_article_delete").url;
		while (articleNodes.length > 0) {
			var article	= core.js.base.Encoder.decode(dojo.attr(articleNodes[0], "data-app-entity-props"));
			articleNodes.splice(0, 1);
			if (article.status != "deleted") {
				dojo.xhrPost({
					url: url,
					content: {
						article_id: article.article_id,
						format: "json"
					},
					handleAs: "json",
					load: function(data) {
						dojo.publish("/app/content/article/delete/onComplete", [ data ]);
					}
				});
			}
		}
		
		if (articleNodes.length == 0) {
			this._helper.hideStandby();
		}
	},
	
	updateStatus: function(/*String*/ status, /*DomNode[]*/ articleNodes) {
		// summary:
		//		Called when moving article items to a status item
		if (this._articleSearchCriteria.status == status) {
			return;
		}
		
		this._helper.showStandby();
		while (articleNodes.length > 0) {
			var article	= core.js.base.Encoder.decode(dojo.attr(articleNodes[0], "data-app-entity-props"));
			var articleItemView = this._articleListView.getArticleItemView(article.article_id);
			if (articleItemView && article.status) {
				this.activateArticle(articleItemView);
				articleNodes.splice(0, 1);
			}
		}
		
		if (articleNodes.length == 0) {
			this._helper.hideStandby();
		}
	},
	
	////////// MANAGE ARTICLES IN FOLDERS //////////
	
	addToFolder: function(/*Object*/ folder, /*DomNode[]*/ articleNodes) {
		// summary:
		//		Called after dropping articles to given folder
		if (!folder || folder.entity_class != "Content_Models_Article") {
			return;
		}
		var folderId = folder.folder_id;
		var url = core.js.base.controllers.ActionProvider.get("content_folder_add").url;
		
		this._helper.showStandby();
		while (articleNodes.length > 0) {
			var article	= core.js.base.Encoder.decode(dojo.attr(articleNodes[0], "data-app-entity-props"));
			articleNodes.splice(0, 1);
			dojo.xhrPost({
				url: url,
				content: {
					article_id: article.article_id,
					folder_id: folderId
				},
				handleAs: "json",
				load: function(data) {
					dojo.publish("/app/content/folder/add/onComplete", [ data ]);
				}
			});
		}
		
		if (articleNodes.length == 0) {
			this._helper.hideStandby();
		}
	},
	
	removeFromFolder: function(/*content.js.views.ArticleItemView*/ articleItemView) {
		// summary:
		//		Removes given article item from the selected folder
		if (!this._articleSearchCriteria.folder_id) {
			return;
		}
		this._helper.showStandby();
		var _this = this;
		dojo.xhrPost({
			url: core.js.base.controllers.ActionProvider.get("content_folder_remove").url,
			content: {
				article_id: articleItemView.getArticle().article_id,
				folder_id: this._articleSearchCriteria.folder_id
			},
			handleAs: "json",
			load: function(data) {
				_this._helper.hideStandby();
				dojo.publish("/app/global/notification", [{
					message: _this._i18n.folder.remove[(data.result == "APP_RESULT_OK") ? "success" : "error"],
					type: (data.result == "APP_RESULT_OK") ? "message" : "error"
				}]);
				
				if (data.result == "APP_RESULT_OK") {
					_this.searchArticles();
				}
			}
		});
	}
});
