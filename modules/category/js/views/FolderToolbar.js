/**
 * NextCMS
 * 
 * @author		Nguyen Huu Phuoc <thenextcms@gmail.com>
 * @copyright	Copyright (c) 2011 - 2012, Nguyen Huu Phuoc
 * @license		http://nextcms.org/license.txt	(GNU GPL version 2 or later)
 * @link		http://nextcms.org
 * @category	modules
 * @package		category
 * @subpackage	js
 * @since		1.0
 * @version		2012-03-27
 */

dojo.provide("category.js.views.FolderToolbar");

dojo.require("dijit.form.Button");
dojo.require("dijit.form.DropDownButton");
dojo.require("dijit.Menu");
dojo.require("dijit.MenuItem");
dojo.require("dijit.Toolbar");
dojo.require("dijit.ToolbarSeparator");

dojo.require("core.js.base.Config");
dojo.require("core.js.base.controllers.ActionProvider");
dojo.require("core.js.base.I18N");

dojo.declare("category.js.views.FolderToolbar", null, {
	// _id: String
	_id: null,
	
	// _i18n: Object
	_i18n: null,
	
	constructor: function(/*String*/ id) {
		this._id = id;
		
		core.js.base.I18N.requireLocalization("category/languages");
		this._i18n = core.js.base.I18N.getLocalization("category/languages");
		
		this._createToolbar();
	},
	
	_createToolbar: function() {
		// summary:
		//		Creates toolbar
		var _this = this;
		var toolbar = new dijit.Toolbar({}, this._id);
		toolbar.addChild(new dijit.form.Button({
			label: this._i18n.global._share.addAction,
			showLabel: false,
			iconClass: "appIcon appAddIcon",
			disabled: !core.js.base.controllers.ActionProvider.get("category_folder_add").isAllowed,
			onClick: function(e) {
				_this.onAddFolder();
			}
		}));
		
		// Refresh button
		toolbar.addChild(new dijit.form.Button({
			label: this._i18n.global._share.refreshAction,
			showLabel: false,
			iconClass: "appIcon appRefreshIcon",
			disabled: !core.js.base.controllers.ActionProvider.get("category_folder_list").isAllowed,
			onClick: function(e) {
				_this.onRefresh();
			}
		}));
		
		// Language selector
		var languages = core.js.base.Config.get("core", "localization_languages");
		if (languages) {
			var languagesMenu = new dijit.Menu();
			for (var locale in languages) {
				languagesMenu.addChild(new dijit.MenuItem({
					__locale: locale,
					label: languages[locale],
					iconClass: "appIcon appFlag_" + locale,
					onClick: function(e) {
						_this._languageDropDownButton.set("label", this.label);
						_this._languageDropDownButton.set("iconClass", this.iconClass);
						_this.onSwitchToLanguage(this.__locale);
					}
				}));
			}
			toolbar.addChild(new dijit.ToolbarSeparator());
			this._languageDropDownButton = new dijit.form.DropDownButton({
				label: this._i18n.global._share.language,
				showLabel: true,
				dropDown: languagesMenu
			});
			toolbar.addChild(this._languageDropDownButton);
		}
	},
	
	setLanguage: function(/*String*/ language) {
		// summary:
		//		Set the language
		if (language) {
			var languages = core.js.base.Config.get("core", "localization_languages");
			this._languageDropDownButton.set("label", languages[language]);
			this._languageDropDownButton.set("iconClass", "appIcon appFlag_" + language);
		}
	},
	
	////////// CALLBACKS //////////
	
	onAddFolder: function() {
		// summary:
		//		Adds new folder
		// tags:
		//		callback
	},
	
	onRefresh: function() {
		// summary:
		//		Reloads the list of folders
		// tags:
		//		callback
	},
	
	onSwitchToLanguage: function(/*String?*/ language) {
		// summary:
		//		Loads the list of folders when switching to other language
		// tags:
		//		callback
	}
});
