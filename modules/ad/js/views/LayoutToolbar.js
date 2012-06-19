/**
 * NextCMS
 * 
 * @author		Nguyen Huu Phuoc <thenextcms@gmail.com>
 * @copyright	Copyright (c) 2011 - 2012, Nguyen Huu Phuoc
 * @license		http://nextcms.org/license.txt	(GNU GPL version 2 or later)
 * @link		http://nextcms.org
 * @category	modules
 * @package		ad
 * @subpackage	js
 * @since		1.0
 * @version		2011-12-30
 */

dojo.provide("ad.js.views.LayoutToolbar");

dojo.require("dijit.form.Button");
dojo.require("dijit.form.Select");
dojo.require("dijit.Toolbar");
dojo.require("dijit.ToolbarSeparator");

dojo.require("core.js.base.controllers.ActionProvider");
dojo.require("core.js.base.I18N");

dojo.declare("ad.js.views.LayoutToolbar", null, {
	// _id: String
	_id: null,

	// _i18n: Object
	_i18n: null,
	
	// _saveButton: dijit.form.Button
	_saveButton: null,
	
	// _templateSelect: dijit.form.Select
	_templateSelect: null,
	
	// _pageSelect: dijit.form.Select
	_pageSelect: null,
	
	// _templates: Object
	// 		Defines the array of templates in the following format:
	// 		{
	//			templateName: {
	//				pageId: {
	//					page_id: "Page's Id",
	//					template: "Page's template",
	//					name: "Name of page",
	//					layout: "Layout of page (in JSON)",
	//					banners: Array of banners on the page
	//				},
	//				otherPageId: {
	//					...
	//				}
	//			},
	//			otherTemplateName: {
	//				...
	//			}
	//		}
	_templates: null,
	
	constructor: function(/*String*/ id) {
		this._id = id;
		
		core.js.base.I18N.requireLocalization("ad/languages");
		this._i18n = core.js.base.I18N.getLocalization("ad/languages");
		
		this._createToolbar();
	},
	
	_createToolbar: function() {
		// summary:
		//		Creates the toolbar
		var _this = this;
		var toolbar = new dijit.Toolbar({}, this._id);
		
		// "Add" button
		this._saveButton = new dijit.form.Button({
			label: this._i18n.global._share.saveAction,
			showLabel: false,
			iconClass: "appIcon appSaveIcon",
			disabled: !core.js.base.controllers.ActionProvider.get("ad_banner_place").isAllowed,
			onClick: function(e) {
				_this.onSaveLayout();
			}
		});
		toolbar.addChild(this._saveButton);
		
		toolbar.addChild(new dijit.ToolbarSeparator());
		
		// Template and page select controls
		this._templateSelect = new dijit.form.Select({ 
			options: [{
				label: this._i18n.banner.place.selectTemplate,
				value: "",
				disabled: true
			}, {
				value: "",
				label: "",
				selected: false,
				disabled: false
			}],
			style: "height: 20px; margin-right: 5px",
			disabled: !core.js.base.controllers.ActionProvider.get("ad_banner_place").isAllowed,
			onChange: function(value) {
				if (_this._templates) {
					_this._pageSelect.removeOption(_this._pageSelect.getOptions());
					_this._pageSelect.addOption([{
						label: _this._i18n.banner.place.selectPage,
						value: "",
						disabled: true
					}, {
						value: "",
						label: "",
						selected: false,
						disabled: false
					}]);
					
					var pages = _this._templates[value];
					for (var pageId in pages) {
						_this._pageSelect.addOption({
							label: pages[pageId + ""].name,
							value: pageId,
							// Don't allow to choose the page which has not been set the layout
							disabled: pages[pageId + ""].layout ? false : true
						});
					}
				}
			}
		});
		toolbar.addChild(this._templateSelect);
		
		this._pageSelect = new dijit.form.Select({ 
			options: [{
				label: this._i18n.banner.place.selectPage,
				value: ""
			}, {
				value: "",
				label: "",
				selected: false,
				disabled: false
			}],
			style: "height: 20px",
			disabled: !core.js.base.controllers.ActionProvider.get("ad_banner_place").isAllowed,
			onChange: function(value) {
				var template = _this._templateSelect.get("value");
				if (_this._templates && template && _this._templates[template]) {
					var page = _this._templates[template][value + ""];
					_this.onLoadPage(page);
				}
			}
		});
		toolbar.addChild(this._pageSelect);
	},
	
	setTemplates: function(/*Object*/ templates) {
		// summary:
		//		Sets the templates and pages
		this._templateSelect.removeOption(this._templateSelect.getOptions());
		this._templateSelect.addOption([{
			label: this._i18n.banner.place.selectTemplate,
			value: "",
			disabled: true
		}, {
			value: "",
			label: "",
			selected: false,
			disabled: false
		}]);
		
		this._templates = templates;
		for (var template in templates) {
			this._templateSelect.addOption({
				label: template,
				value: template
			});
		}
	},
	
	getTemplates: function() {
		// summary:
		//		Gets the templates
		return this._templates;		// Object
	},
	
	////////// CONTROL STATE OF CONTROLS //////////
	
	allowToSaveLayout: function(/*Boolean*/ isAllowed) {
		// summary:
		//		Allows/disallows to save the layout
		isAllowed = isAllowed && core.js.base.controllers.ActionProvider.get("ad_banner_place").isAllowed;
		this._saveButton.set("disabled", !isAllowed);
		return this;	// ad.js.views.LayoutToolbar
	},
	
	////////// CALLBACKS //////////
	
	onLoadPage: function(/*Object*/ page) {
		// summary:
		//		Loads the layout and banners of given page
		// page:
		//		The page data, including the following properties:
		//		- template: The template's name
		//		- page_id: The page's Id
		//		- layout: Layout of page
		//		- banners: Array of banners on the page
		// tags:
		//		callback
	},
	
	onSaveLayout: function() {
		// summary:
		//		Saves the layout
		// tags:
		//		callback
	}
});
