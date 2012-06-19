/**
 * NextCMS
 * 
 * @author		Nguyen Huu Phuoc <thenextcms@gmail.com>
 * @copyright	Copyright (c) 2011 - 2012, Nguyen Huu Phuoc
 * @license		http://nextcms.org/license.txt	(GNU GPL version 2 or later)
 * @link		http://nextcms.org
 * @category	modules
 * @package		core
 * @subpackage	js
 * @since		1.0
 * @version		2011-10-28
 */

dojo.provide("core.js.base.views.Helper");

dojo.require("dojox.layout.ContentPane");
dojo.require("dojox.widget.DialogSimple");
dojo.require("dojox.widget.Standby");

dojo.require("core.js.base.I18N");

dojo.declare("core.js.base.views.Helper", null, {
	// _id: String
	_id: null,
	
	// _i18n: Object
	_i18n: null,
	
	////////// HELPER CONTROLS //////////
	
	// _dialog: dijit.Dialog
	//		Dialog for showing additional form
	_dialog: null,
	
	// _pane: dojox.layout.ContentPane
	//		The pane for showing additional UI
	_pane: null,
	
	// _standby: dojox.widget.Standby
	_standby: null,
	
	constructor: function(/*String*/ id) {
		this._id = id;
	},
	
	setLanguageData: function(/*Object*/ languageData) {
		this._i18n = languageData;
		return this;	// core.js.base.views.Helper
	},
	
	setModule: function(/*String*/ module) {
		core.js.base.I18N.requireLocalization(module + "/languages");
		this._i18n = core.js.base.I18N.getLocalization(module + "/languages");
		
		return this;	// core.js.base.views.Helper
	},
	
	////////// MANAGE UI CONTROLS //////////
	
	showDialog: function(/*String*/ url, /*Object*/ settings) {
		// summary:
		//		Shows the dialog when creating request of given URL
		settings = dojo.mixin({
			loadingMessage: "<div style='text-align: center'><span class='dijitContentPaneLoading'>" + this._i18n.global._share.loadingAction + "</span></div>"
		}, settings);
		this._dialog = new dojox.widget.DialogSimple(settings);
		this._dialog.set("href", url);
		this._dialog.show();
		
		return this._dialog;	// dojox.widget.DialogSimple
	},
	
	closeDialog: function() {
		// summary:
		//		Closes the dialog that is created by the showDialog() method
		if (this._dialog) {
			// this._dialog.hide();
			dijit.byId(this._id).removeChild(this._dialog);
			this._dialog.destroyRecursive();
			this._dialog = null;
		}
	},
	
	showPane: function(/*String*/ url, /*Object*/ settings) {
		// summary:
		//		Shows additional pane when creating request of given URL
		var defaultSettings = {
			region: "right",
			splitter: true,
			gutters: false,
			minSize: 400,
			style: "height: 100%; width: 40%",
			loadingMessage: "<div class='appCenter'><div><span class='dijitContentPaneLoading'>" + this._i18n.global._share.loadingAction + "</span></div></div>"
		};
		settings = dojo.mixin(defaultSettings, settings);
		if (this._pane == null) {
			this._pane = new dojox.layout.ContentPane(settings);
			dijit.byId(this._id).addChild(this._pane);
		}
		this._pane.set("href", url);
		dojo.connect(this._pane, "onDownloadEnd", this, function() {
			dojo.publish("/app/global/onLoadComplete", [ this._pane.get("href") ]);
		});
		
		return this._pane;	// dojox.layout.ContentPane
	},
	
	removePane: function() {
		// summary:
		//		Removes additional pane which is created by showPane() method
		if (this._pane != null) {
			dijit.byId(this._id).removeChild(this._pane);
			this._pane.destroyRecursive();
			this._pane = null;
		}
	},
	
	showStandby: function() {
		// summary:
		//		Shows the Standby widget
		if (this._standby == null) {
			// Init the Standby widget
			this._standby = new dojox.widget.Standby({
				target: this._id,
				imageText: this._i18n.global._share.loadingAction
			});
			document.body.appendChild(this._standby.domNode);
			this._standby.startup();
		}
		this._standby.show();
	},
	
	hideStandby: function() {
		// summary:
		//		Hides the Standby widget
		this._standby.hide();
	}
});
