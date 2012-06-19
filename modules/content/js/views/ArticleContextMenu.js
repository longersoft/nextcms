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

dojo.provide("content.js.views.ArticleContextMenu");

dojo.require("dijit.Menu");
dojo.require("dijit.MenuItem");
dojo.require("dijit.MenuSeparator");
dojo.require("dijit.PopupMenuItem");

dojo.require("core.js.base.Config");
dojo.require("core.js.base.controllers.ActionProvider");
dojo.require("core.js.base.I18N");

dojo.declare("content.js.views.ArticleContextMenu", null, {
	// _contextMenu: dijit.Menu
	_contextMenu: null,
	
	// _i18n: Object
	_i18n: null,
	
	// _activateMenuItem: dijit.MenuItem
	_activateMenuItem: null,

	// _editMenuItem: dijit.MenuItem
	_editMenuItem: null,
	
	// _deleteMenuItem: dijit.MenuItem
	_deleteMenuItem: null,
	
	// _revisionMenuItem: dijit.MenuItem
	_revisionMenuItem: null,
	
	// _removeFromFolderMenuItem: dijit.MenuItem
	_removeFromFolderMenuItem: null,
	
	constructor: function() {
		core.js.base.I18N.requireLocalization("content/languages");
		this._i18n = core.js.base.I18N.getLocalization("content/languages");
	},
	
	show: function(/*content.js.views.ArticleItemView*/ articleItemView) {
		var _this = this;
		
		// Get article data
		var article = articleItemView.getArticle();
		
		// Create menu
		this._contextMenu = new dijit.Menu({
			targetNodeIds: [ dojo.attr(articleItemView.getDomNode(), "id") ]
		});
		
		// "Activate" menu item
		this._activateMenuItem = new dijit.MenuItem({
			label: (article.status == "activated") ? this._i18n.global._share.deactivateAction : this._i18n.global._share.activateAction,
			iconClass: "appIcon " + (article.status == "activated" ? "appDeactivateIcon" : "appActivateIcon"),
			disabled: !core.js.base.controllers.ActionProvider.get("content_article_activate").isAllowed,
			onClick: function() {
				_this.onActivateArticle(articleItemView);
			}
		});
		this._contextMenu.addChild(this._activateMenuItem);
		
		// "Edit" menu item
		this._editMenuItem = new dijit.MenuItem({
			label: this._i18n.global._share.editAction,
			disabled: !core.js.base.controllers.ActionProvider.get("content_article_edit").isAllowed,
			onClick: function() {
				_this.onEditArticle(articleItemView);
			}
		});
		this._contextMenu.addChild(this._editMenuItem);
		
		// "Delete" menu item
		this._deleteMenuItem = new dijit.MenuItem({
			label: (article.status == "deleted") ? this._i18n.global._share.deleteForeverAction : this._i18n.global._share.deleteAction,
			iconClass: "appIcon appDeleteIcon",
			disabled: !core.js.base.controllers.ActionProvider.get("content_article_delete").isAllowed,
			onClick: function() {
				_this.onDeleteArticle(articleItemView);
			}
		});
		this._contextMenu.addChild(this._deleteMenuItem);
		
		// "Remove from folder" menu item
		this._removeFromFolderMenuItem = new dijit.MenuItem({
			label: this._i18n.global._share.removeFromFolderAction,
			disabled: !core.js.base.controllers.ActionProvider.get("content_folder_remove").isAllowed,
			onClick: function() {
				_this.onRemoveFromFolder(articleItemView);
			}
		});
		this._contextMenu.addChild(this._removeFromFolderMenuItem);
		
		this._contextMenu.addChild(new dijit.MenuSeparator());
		
		// "Revision" menu item
		this._revisionMenuItem = new dijit.MenuItem({
			label: this._i18n.global._share.viewRevisionsAction,
			disabled: !core.js.base.controllers.ActionProvider.get("content_revision_list").isAllowed,
			onClick: function() {
				_this.onViewRevisions(articleItemView);
			}
		});
		this._contextMenu.addChild(this._revisionMenuItem);
		
		// "Localize" menu item
		var languages = core.js.base.Config.get("core", "localization_languages");
		if (languages) {
			var localizePopupMenu = new dijit.Menu();
			for (var locale in languages) {
				localizePopupMenu.addChild(new dijit.MenuItem({
					__locale: locale,
					label: languages[locale],
					iconClass: "appIcon appFlag_" + locale,
					onClick: function(e) {
						var translations = articleItemView.getArticle().translations;
						if (translations[this.__locale]) {
							_this.onEditArticle(articleItemView);
						} else {
							_this.onTranslateArticle(articleItemView, this.__locale);
						}
					}
				}));
			}
			
			this._contextMenu.addChild(new dijit.PopupMenuItem({
				label: this._i18n.global._share.localizeAction,
				popup: localizePopupMenu
			}));
		}
		
		this._contextMenu.startup();
	},
	
	////////// ENABLE/DISABLE CONTROLS //////////
	
	allowToRemoveFromFolder: function(/*Boolean*/ isAllowed) {
		// summary:
		//		Allows/disallows to remove articles from a given folder
		this._removeFromFolderMenuItem.set("disabled", !isAllowed || !core.js.base.controllers.ActionProvider.get("content_folder_remove").isAllowed);
		return this;	// content.js.views.ArticleContextMenu
	},
	
	////////// CALLBACKS //////////
	
	onActivateArticle: function(/*content.js.views.ArticleItemView*/ articleItemView) {
		// summary:
		//		Activates or deactivates given article item
		// tags:
		//		callback
	},
	
	onDeleteArticle: function(/*content.js.views.ArticleItemView*/ articleItemView) {
		// summary:
		//		Deletes given article item
		// tags:
		//		callback
	},
	
	onEditArticle: function(/*content.js.views.ArticleItemView*/ articleItemView) {
		// summary:
		//		Edits given article item
		// tags:
		//		callback
	},
	
	onRemoveFromFolder: function(/*content.js.views.ArticleItemView*/ articleItemView) {
		// summary:
		//		Removes given article item from the selected folder
		// tags:
		//		callback
	},
	
	onTranslateArticle: function(/*content.js.views.ArticleItemView*/ articleItemView, /*String*/ language) {
		// summary:
		//		Translates given article item to other language
		// tags:
		//		callback
	},
	
	onViewRevisions: function(/*content.js.views.ArticleItemView*/ articleItemView) {
		// summary:
		//		Shows revisions of given article item
		// tags:
		//		callback
	}
});
