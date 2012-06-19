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
 * @version		2011-12-10
 */

dojo.provide("core.js.views.LanguageToolbar");

dojo.require("dijit.form.Button");
dojo.require("dijit.form.Select");
dojo.require("dijit.Toolbar");

dojo.require("core.js.base.controllers.ActionProvider");
dojo.require("core.js.base.I18N");

dojo.declare("core.js.views.LanguageToolbar", null, {
	// _id: String
	_id: null,

	// _i18n: Object
	_i18n: null,
	
	// _languageFiles: Object
	_languageFiles: null,
	
	// _moduleSelect: dijit.form.Select
	_moduleSelect: null,
	
	// _extensionSelect: dijit.form.Select
	_extensionSelect: null,
	
	// _languageSelect: dijit.form.Select
	_languageSelect: null,
	
	constructor: function(/*String*/ id) {
		this._id = id;
		
		core.js.base.I18N.requireLocalization("core/languages");
		this._i18n = core.js.base.I18N.getLocalization("core/languages");
		
		this._createToolbar();
	},
	
	_createToolbar: function() {
		// summary:
		//		Creates the toolbar
		var _this = this;
		var toolbar = new dijit.Toolbar({}, this._id);
		
		// Module selector
		this._moduleSelect = new dijit.form.Select({
			style: "height: 20px; margin-right: 5px",
			onChange: function(value) {
				if (_this._languageFiles == null) {
					return;
				}
				_this._resetSelectControl(_this._extensionSelect, _this._i18n.language.list.selectExtension);
				_this._resetSelectControl(_this._languageSelect, _this._i18n.language.list.selectLanguage);
				
				if (_this._languageFiles[value]) {
					for (var type in _this._languageFiles[value]) {
						// type can be "module", "hooks", "plugins", "tasks", "widgets"
						if (_this._languageFiles[value][type]) {
							switch (type) {
								case "module":
									for (var i in _this._languageFiles[value][type]) {
										_this._languageSelect.addOption({
											label: _this._languageFiles[value][type][i],
											value: value + "/languages/" + _this._languageFiles[value][type][i]
										});
									}
									break;
								case "hooks":
								case "plugins":
								case "tasks":
								case "widgets":
									_this._extensionSelect.addOption({
										type: "separator"
									});
									_this._extensionSelect.addOption({
										label: _this._i18n.language.list[type],
										value: "",
										disabled: true
									});
									for (var extensionName in _this._languageFiles[value][type]) {
										_this._extensionSelect.addOption({
											label: extensionName,
											value: dojo.toJson({
												module: value,
												type: type,
												name: extensionName
											})
										});
									}
									break;
							}
						}
					}
				}
			}
		});
		
		// Extension selector
		this._extensionSelect = new dijit.form.Select({
			style: "height: 20px; margin-right: 5px",
			onChange: function(value) {
				if (_this._languageFiles == null || !value) {
					return;
				}
				value = dojo.fromJson(value);
				_this._resetSelectControl(_this._languageSelect, _this._i18n.language.list.selectLanguage);
				var languages = _this._languageFiles[value.module][value.type][value.name];
				if (languages) {
					for (var i in languages) {
						_this._languageSelect.addOption({
							label: languages[i],
							value: value.module + "/" + value.type + "/" + value.name + "/" + languages[i]
						});
					}
				}
			}
		});
		
		// Language selector
		this._languageSelect = new dijit.form.Select({
			style: "height: 20px; margin-right: 5px",
			onChange: function(value) {
				if (value) {
					_this.onLoadLanguage(value);
				}
			}
		});
		
		this._resetSelects();
		toolbar.addChild(this._moduleSelect);
		toolbar.addChild(this._extensionSelect);
		toolbar.addChild(this._languageSelect);
	},
	
	setLanguageFiles: function(/*Object*/ languageFiles) {
		// summary:
		//		Sets the language files
		this._languageFiles = languageFiles;
		this._resetSelects();
		
		for (var module in languageFiles) {
			this._moduleSelect.addOption({
				label: module,
				value: module
			});
		}
	},
	
	_resetSelectControl: function(/*dijit.form.Select*/ selectControl, /*String*/ initOptionLabel) {
		// summary:
		//		Resets a select control
		selectControl.removeOption(selectControl.getOptions());
		selectControl.addOption({
			label: initOptionLabel,
			value: "",
			disabled: true
		});
	},
	
	_resetSelects: function() {
		// summary:
		//		Resets all select controls
		this._resetSelectControl(this._moduleSelect, this._i18n.language.list.selectModule);
		this._resetSelectControl(this._extensionSelect, this._i18n.language.list.selectExtension);
		this._resetSelectControl(this._languageSelect, this._i18n.language.list.selectLanguage);
	},
	
	////////// CALLBACKS //////////
	
	onLoadLanguage: function(/*String*/ file) {
		// summary:
		//		Loads the selected language file
		// file:
		//		The path of language file
		// tags:
		//		callback
	}
});
