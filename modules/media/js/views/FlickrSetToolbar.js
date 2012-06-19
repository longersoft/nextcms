/**
 * NextCMS
 * 
 * @author		Nguyen Huu Phuoc <thenextcms@gmail.com>
 * @copyright	Copyright (c) 2011 - 2012, Nguyen Huu Phuoc
 * @license		http://nextcms.org/license.txt	(GNU GPL version 2 or later)
 * @link		http://nextcms.org
 * @category	modules
 * @package		media
 * @subpackage	js
 * @since		1.0
 * @version		2011-11-05
 */

dojo.provide("media.js.views.FlickrSetToolbar");

dojo.require("dijit.form.Button");
dojo.require("dijit.Toolbar");

dojo.require("core.js.base.controllers.ActionProvider");
dojo.require("core.js.base.I18N");

dojo.declare("media.js.views.FlickrSetToolbar", null, {
	// _toolbar: dijit.Toolbar
	_toolbar: null,
	
	// _i18n: Object
	_i18n: null,
	
	constructor: function(/*String*/ id) {
		this._toolbar = new dijit.Toolbar({}, id);
		
		core.js.base.I18N.requireLocalization("media/languages");
		this._i18n = core.js.base.I18N.getLocalization("media/languages");
		
		this._createToolbar();
	},
	
	_createToolbar: function() {
		// summary:
		//		Creates the toolbar
		// Add toolbar items
		var _this = this;
		
		// Refresh button
		this._toolbar.addChild(new dijit.form.Button({
			label: this._i18n.global._share.refreshAction,
			showLabel: false,
			iconClass: "appIcon appRefreshIcon",
			disabled: !core.js.base.controllers.ActionProvider.get("media_flickr_set").isAllowed,
			onClick: function(e) {
				_this.onRefresh();
			}
		}));
	},
	
	////////// CALLBACKS //////////
	
	onRefresh: function() {
		// summary:
		//		Reloads the list of Flickr sets
		// tags:
		//		callback
	}
});
