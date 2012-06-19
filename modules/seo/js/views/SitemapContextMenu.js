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
 * @version		2011-10-18
 */

dojo.provide("seo.js.views.SitemapContextMenu");

dojo.require("dijit.Menu");
dojo.require("dijit.MenuItem");

dojo.require("core.js.base.controllers.ActionProvider");
dojo.require("core.js.base.I18N");

dojo.declare("seo.js.views.SitemapContextMenu", null, {
	// _contextMenu: dijit.Menu
	_contextMenu: null,
	
	// _i18n: Object
	_i18n: null,
	
	constructor: function() {
		core.js.base.I18N.requireLocalization("seo/languages");
		this._i18n = core.js.base.I18N.getLocalization("seo/languages");
	},

	show: function(/*seo.js.views.SitemapItemView*/ sitemapItemView) {
		// summary:
		//		Shows the context menu
		var _this = this;
		
		// Create menu
		this._contextMenu = new dijit.Menu({
			targetNodeIds: [ dojo.attr(sitemapItemView.getDomNode(), "id") ]
		});
		
		// "Edit" item
		this._contextMenu.addChild(new dijit.MenuItem({
			label: this._i18n.global._share.editAction,
			disabled: !core.js.base.controllers.ActionProvider.get("seo_sitemap_build").isAllowed,
			onClick: function() {
				_this.onEditItem(sitemapItemView);
			}
		}));
		
		// "Delete" item
		this._contextMenu.addChild(new dijit.MenuItem({
			label: this._i18n.global._share.deleteAction,
			iconClass: "appIcon appDeleteIcon",
			disabled: !core.js.base.controllers.ActionProvider.get("seo_sitemap_build").isAllowed,
			onClick: function() {
				_this.onDeleteItem(sitemapItemView);
			}
		}));
		
		this._contextMenu.startup();
	},
	
	////////// CALLBACKS //////////
	
	onDeleteItem: function(/*seo.js.views.SitemapItemView*/ sitemapItemView) {
		// summary:
		//		Deletes given sitemap item
		// tags:
		//		callback
	},
	
	onEditItem: function(/*seo.js.views.SitemapItemView*/ sitemapItemView) {
		// summary:
		//		Edits given sitemap item
		// tags:
		//		callback
	}
});
