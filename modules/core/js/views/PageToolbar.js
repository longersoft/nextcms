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
 * @version		2012-02-05
 */

dojo.provide("core.js.views.PageToolbar");

dojo.require("dijit.form.Button");
dojo.require("dijit.form.DropDownButton");
dojo.require("dijit.Menu");
dojo.require("dijit.MenuItem");
dojo.require("dijit.MenuSeparator");
dojo.require("dijit.Toolbar");
dojo.require("dijit.ToolbarSeparator");

dojo.require("core.js.base.Config");
dojo.require("core.js.base.controllers.ActionProvider");
dojo.require("core.js.base.I18N");

dojo.declare("core.js.views.PageToolbar", null, {
	// _id: String
	_id: null,

	// _i18n: Object
	_i18n: null,
	
	// _templates: Array
	_templates: [],
	
	// _templateDropDownButton: dijit.form.DropDownButton
	_templateDropDownButton: null,
	
	// _languageDropDownButton: dijit.form.DropDownButton
	_languageDropDownButton: null,
	
	// _saveOrderButton: dijit.form.Button
	_saveOrderButton: null,
	
	// criteria: Object
	criteria: {
		template: null,
		language: null
	},
	
	constructor: function(/*String*/ id) {
		this._id = id;
		
		core.js.base.I18N.requireLocalization("core/languages");
		this._i18n = core.js.base.I18N.getLocalization("core/languages");
	},
	
	setTemplates: function(/*Array*/ templates) {
		// summary:
		//		Sets the array of available templates
		this._templates = templates;
		return this;	// core.js.views.PageToolbar
	},
	
	show: function() {
		// summary:
		//		Shows the toolbar
		var _this = this;
		var toolbar = new dijit.Toolbar({}, this._id);
		
		// Add button
		toolbar.addChild(new dijit.form.Button({
			label: this._i18n.global._share.addAction,
			showLabel: false,
			iconClass: "appIcon appAddIcon",
			disabled: !core.js.base.controllers.ActionProvider.get("core_page_add").isAllowed,
			onClick: function(e) {
				_this.onAddPage();
			}
		}));
		
		// Refresh button
		toolbar.addChild(new dijit.form.Button({
			label: this._i18n.global._share.refreshAction,
			showLabel: false,
			iconClass: "appIcon appRefreshIcon",
			disabled: !core.js.base.controllers.ActionProvider.get("core_page_list").isAllowed,
			onClick: function(e) {
				_this.onRefresh();
			}
		}));
		
		toolbar.addChild(new dijit.ToolbarSeparator());
		
		// Save order button
		this._saveOrderButton = new dijit.form.Button({
			label: this._i18n.global._share.saveOrderAction,
			showLabel: true,
			disabled: !core.js.base.controllers.ActionProvider.get("core_page_order").isAllowed,
			onClick: function(e) {
				_this.onSaveOrder();
			}
		});
		toolbar.addChild(this._saveOrderButton);
		
		// Template filter
		if (this._templates.length > 0) {
			var templatesMenu = new dijit.Menu();
			templatesMenu.addChild(new dijit.MenuItem({
				label: this._i18n.page.list.allTemplates,
				onClick: function(e) {
					_this._templateDropDownButton.set("label", this.label);
					_this.criteria.template = null;
					_this.onSelectTemplate(null);
				}
			}));
			templatesMenu.addChild(new dijit.MenuSeparator());
			
			for (var i in this._templates) {
				templatesMenu.addChild(new dijit.MenuItem({
					__template: this._templates[i],
					label: this._templates[i],
					onClick: function(e) {
						_this._templateDropDownButton.set("label", this.label);
						_this.criteria.template = this.__template;
						_this.onSelectTemplate(this.__template);
					}
				}));
			}
			toolbar.addChild(new dijit.ToolbarSeparator());
			this._templateDropDownButton = new dijit.form.DropDownButton({
				label: this._i18n.page.list.allTemplates,
				showLabel: true,
				dropDown: templatesMenu
			});
			toolbar.addChild(this._templateDropDownButton);
		}
		
		// Language filter
		var languages = core.js.base.Config.get("core", "localization_languages");
		if (languages) {
			var languagesMenu = new dijit.Menu();
			languagesMenu.addChild(new dijit.MenuItem({
				label: this._i18n.global._share.allLanguages,
				onClick: function(e) {
					_this._languageDropDownButton.set("label", this.label);
					_this._languageDropDownButton.set("iconClass", null);
					_this.criteria.language = null;
					_this.onSwitchToLanguage(null);
				}
			}));
			languagesMenu.addChild(new dijit.MenuSeparator());
			
			for (var locale in languages) {
				languagesMenu.addChild(new dijit.MenuItem({
					__locale: locale,
					label: languages[locale],
					iconClass: "appIcon appFlag_" + locale,
					onClick: function(e) {
						_this._languageDropDownButton.set("label", this.label);
						_this._languageDropDownButton.set("iconClass", this.iconClass);
						_this.criteria.language = this.__locale;
						_this.onSwitchToLanguage(this.__locale);
					}
				}));
			}
			this._languageDropDownButton = new dijit.form.DropDownButton({
				label: this._i18n.global._share.allLanguages,
				showLabel: true,
				dropDown: languagesMenu
			});
			toolbar.addChild(this._languageDropDownButton);
		}
	},
	
	initSearchCriteria: function(/*Object*/ criteria) {
		// summary:
		//		Inits controls with given criteria
		dojo.mixin(this.criteria, criteria);
		if (criteria.template && this._templateDropDownButton) {
			this._templateDropDownButton.set("label", criteria.template);
		}
		
		var languages = core.js.base.Config.get("core", "localization_languages");
		if (languages && criteria.language && this._languageDropDownButton) {
			this._languageDropDownButton.set("label", languages[criteria.language]);
			this._languageDropDownButton.set("iconClass", "appIcon appFlag_" + criteria.language);
		}
		
		this.allowToSaveOrder(this.criteria.template && this.criteria.language);
	},
	
	////////// CONTROL STATE OF CONTROLS //////////
	
	allowToSaveOrder: function(/*Boolean*/ isAllowed) {
		// summary:
		//		Allows/disallows to save order of pages
		isAllowed = isAllowed && core.js.base.controllers.ActionProvider.get("core_page_order").isAllowed;
		this._saveOrderButton.set("disabled", !isAllowed);
		return this;	// core.js.views.PageToolbar
	},
	
	////////// CALLBACKS //////////
	
	onAddPage: function() {
		// summary:
		//		Adds new page
		// tags:
		//		callback
	},
	
	onRefresh: function() {
		// summary:
		//		Reloads the list of pages
		// tags:
		//		callback
	},
	
	onSaveOrder: function() {
		// summary:
		//		Updates the orders of pages
		// tags:
		//		callback
	},
	
	onSelectTemplate: function(/*String?*/ template) {
		// summary:
		//		Loads the list of pages in given template
		// tags:
		//		callback
	},
	
	onSwitchToLanguage: function(/*String?*/ language) {
		// summary:
		//		Loads the list of pages by given language
		// tags:
		//		callback
	}
});
