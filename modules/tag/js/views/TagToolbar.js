/**
 * NextCMS
 * 
 * @author		Nguyen Huu Phuoc <thenextcms@gmail.com>
 * @copyright	Copyright (c) 2011 - 2012, Nguyen Huu Phuoc
 * @license		http://nextcms.org/license.txt	(GNU GPL version 2 or later)
 * @link		http://nextcms.org
 * @category	modules
 * @package		tag
 * @subpackage	js
 * @since		1.0
 * @version		2012-01-12
 */

dojo.provide("tag.js.views.TagToolbar");

dojo.require("dijit.form.Button");
dojo.require("dijit.form.DropDownButton");
dojo.require("dijit.form.Select");
dojo.require("dijit.form.TextBox");
dojo.require("dijit.Menu");
dojo.require("dijit.MenuItem");
dojo.require("dijit.MenuSeparator");
dojo.require("dijit.Toolbar");
dojo.require("dijit.ToolbarSeparator");

dojo.require("core.js.base.Config");
dojo.require("core.js.base.controllers.ActionProvider");
dojo.require("core.js.base.I18N");

dojo.declare("tag.js.views.TagToolbar", null, {
	// _id: String
	_id: null,

	// _i18n: Object
	_i18n: null,
	
	// _addButton: dijit.form.Button
	_addButton: null,
	
	// _refreshButton: dijit.form.Button
	_refreshButton: null,
	
	// _searchTextBox: dijit.form.TextBox
	_searchTextBox: null,
	
	// _languageDropDownButton: dijit.form.DropDownButton
	_languageDropDownButton: null,
	
	// _searchButton: dijit.form.Button
	_searchButton: null,
	
	// _perPageSelect: dijit.form.Select
	_perPageSelect: null,
	
	// _numTagsPerPage: Array
	_numTagsPerPage: [ 100, 120, 150, 200, 400, 800 ],
	
	constructor: function(/*String*/ id) {
		this._id = id;
		
		core.js.base.I18N.requireLocalization("tag/languages");
		this._i18n = core.js.base.I18N.getLocalization("tag/languages");
		
		this._createToolbar();
	},
	
	_createToolbar: function() {
		// summary:
		//		Creates the toolbar
		var _this = this;
		var toolbar = new dijit.Toolbar({}, this._id);
		
		// "Add" button
		this._addButton = new dijit.form.Button({
			label: this._i18n.global._share.addAction,
			showLabel: false,
			iconClass: "appIcon appAddIcon",
			disabled: !core.js.base.controllers.ActionProvider.get("tag_tag_add").isAllowed,
			onClick: function(e) {
				_this.onAddTag();
			}
		});
		toolbar.addChild(this._addButton);
		
		// "Refresh" button
		this._refreshButton = new dijit.form.Button({
			label: this._i18n.global._share.refreshAction,
			showLabel: false,
			iconClass: "appIcon appRefreshIcon",
			disabled: !core.js.base.controllers.ActionProvider.get("tag_tag_list").isAllowed,
			onClick: function(e) {
				_this.onRefresh();
			}
		});
		toolbar.addChild(this._refreshButton);
		
		toolbar.addChild(new dijit.ToolbarSeparator());
		
		// Search control
		this._searchTextBox = new dijit.form.TextBox({
			style: "width: 150px",
			placeHolder: this._i18n.tag.list.searchHelp
		});
		toolbar.addChild(this._searchTextBox);
		
		// Language filter
		var languages = core.js.base.Config.get("core", "localization_languages");
		if (languages) {
			var languagesMenu = new dijit.Menu();
			languagesMenu.addChild(new dijit.MenuItem({
				label: this._i18n.global._share.allLanguages,
				onClick: function(e) {
					_this._languageDropDownButton.set("label", this.label);
					_this._languageDropDownButton.set("iconClass", null);
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
		
		this._searchButton = new dijit.form.Button({
			label: this._i18n.global._share.searchAction,
			showLabel: false,
			iconClass: "appIcon appSearchIcon",
			disabled: !core.js.base.controllers.ActionProvider.get("tag_tag_list").isAllowed,
			onClick: function(e) {
				var keyword = _this._searchTextBox.get("value");
				_this.onSearchTags(keyword);
			}
		});
		toolbar.addChild(this._searchButton);
		
		var options = [];
		dojo.forEach(this._numTagsPerPage, function(value, index) {
			options.push({
				label: value,
				value: value + ""
			});
		});
		this._perPageSelect = new dijit.form.Select({ 
			options: options, 
			style: "height: 20px",
			disabled: !core.js.base.controllers.ActionProvider.get("tag_tag_list").isAllowed,
			onChange: function(value) {
				_this.onUpdatePageSize(parseInt(value));
			}
		});
		dojo.addClass(this._perPageSelect.domNode, "appRight");
		toolbar.addChild(this._perPageSelect);
	},
	
	initSearchCriteria: function(/*Object*/ criteria) {
		// summary:
		//		Inits the controls with given criteria
		if (criteria.language && this._languageDropDownButton) {
			var languages = core.js.base.Config.get("core", "localization_languages");
			this._languageDropDownButton.set("label", languages[criteria.language]);
			this._languageDropDownButton.set("iconClass", "appIcon appFlag_" + criteria.language);
		}
		this._searchTextBox.set("value", criteria.keyword);
		this._perPageSelect.set("value", criteria.per_page + "");
	},
	
	////////// CALLBACKS //////////
	
	onAddTag: function() {
		// summary:
		//		Adds new tag
		// tags:
		//		callback
	},
	
	onRefresh: function() {
		// summary:
		//		Reloads the list of tags
		// tags:
		//		callback
	},
	
	onSearchTags: function(/*String*/ keyword) {
		// summary:
		//		Searches for tags by given keyword
		// tags:
		//		callback
	},
	
	onSwitchToLanguage: function(/*String?*/ language) {
		// summary:
		//		Loads the list of tags by given language
		// tags:
		//		callback
	},
	
	onUpdatePageSize: function(/*Integer*/ perPage) {
		// summary:
		//		This method is called when the page size select changes its value
		// perPage:
		//		The number of tags per page
		// tags:
		//		callback
	}
});
