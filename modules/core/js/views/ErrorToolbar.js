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
 * @version		2011-10-18
 */

dojo.provide("core.js.views.ErrorToolbar");

dojo.require("dijit.form.Button");
dojo.require("dijit.form.DateTextBox");
dojo.require("dijit.form.DropDownButton");
dojo.require("dijit.Menu");
dojo.require("dijit.MenuItem");
dojo.require("dijit.MenuSeparator");
dojo.require("dijit.Toolbar");
dojo.require("dijit.ToolbarSeparator");
dojo.require("dojo.date.stamp");

dojo.require("core.js.base.controllers.ActionProvider");
dojo.require("core.js.base.I18N");

dojo.declare("core.js.views.ErrorToolbar", null, {
	// _id: String
	_id: null,

	// _i18n: Object
	_i18n: null,
	
	// _moduleDropDownButton: dijit.form.DropDownButton
	_moduleDropDownButton: null,
	
	// _fromDateTextBox: dijit.form.DateTextBox
	_fromDateTextBox: null,
	
	// _toDateTextBox: dijit.form.DateTextBox
	_toDateTextBox: null,
	
	// _modules: Array
	_modules: [],
	
	constructor: function(/*String*/ id) {
		this._id = id;
		
		core.js.base.I18N.requireLocalization("core/languages");
		this._i18n = core.js.base.I18N.getLocalization("core/languages");
	},
	
	setModules: function(/*Array*/ modules) {
		// summary:
		//		Sets array of modules
		this._modules = modules;
		return this;	// core.js.views.ErrorToolbar
	},
	
	show: function() {
		// summary:
		//		Shows the toolbar
		var _this = this;
		var toolbar = new dijit.Toolbar({}, this._id);
		
		// "Refresh" button
		this._refreshButton = new dijit.form.Button({
			label: this._i18n.global._share.refreshAction,
			showLabel: false,
			iconClass: "appIcon appRefreshIcon",
			disabled: !core.js.base.controllers.ActionProvider.get("core_error_list").isAllowed,
			onClick: function(e) {
				_this.onRefresh();
			}
		});
		toolbar.addChild(this._refreshButton);
		
		if (this._modules.length > 0) {
			// Add a module filter
			var modulesMenu = new dijit.Menu();
			modulesMenu.addChild(new dijit.MenuItem({
				label: this._i18n.error.list.allModules,
				onClick: function(e) {
					_this._moduleDropDownButton.set("label", this.label);
					_this.onSelectModule(null);
				}
			}));
			modulesMenu.addChild(new dijit.MenuSeparator());
			
			for (var i in this._modules) {
				modulesMenu.addChild(new dijit.MenuItem({
					__module: this._modules[i].name,
					label: this._modules[i].title,
					onClick: function(e) {
						_this._moduleDropDownButton.set("label", this.label);
						_this.onSelectModule(this.__module);
					}
				}));
			}
			toolbar.addChild(new dijit.ToolbarSeparator());
			this._moduleDropDownButton = new dijit.form.DropDownButton({
				label: this._i18n.error.list.allModules,
				showLabel: true,
				dropDown: modulesMenu
			});
			toolbar.addChild(this._moduleDropDownButton);
		}
		
		toolbar.addChild(new dijit.ToolbarSeparator());
		// Search for errors in a range of dates
		this._fromDateTextBox = new dijit.form.DateTextBox({
			style: "margin: 0 5px; width: 100px",
			placeHolder: this._i18n.error.list.fromDate
		});
		toolbar.addChild(this._fromDateTextBox);
		
		this._toDateTextBox = new dijit.form.DateTextBox({
			style: "width: 100px",
			placeHolder: this._i18n.error.list.toDate
		});
		toolbar.addChild(this._toDateTextBox);
		
		toolbar.addChild(new dijit.form.Button({
			label: this._i18n.global._share.searchAction,
			showLabel: false,
			iconClass: "appIcon appSearchIcon",
			disabled: !core.js.base.controllers.ActionProvider.get("core_error_list").isAllowed,
			onClick: function(e) {
				var fromDate = _this._fromDateTextBox.get("value");
				var toDate   = _this._toDateTextBox.get("value");
				
				if (fromDate) {
					// DOJO LESSON: Format the date provided by a dijit.form.DateTextBox widget
					fromDate = dojo.date.stamp.toISOString(fromDate, {selector: "date"});
				}
				if (toDate) {
					toDate = dojo.date.stamp.toISOString(toDate, {selector: "date"});
				}
				_this.onSearchErrors({
					from_date: fromDate,
					to_date: toDate
				});
			}
		}));
	},
	
	initSearchCriteria: function(/*Object*/ criteria) {
		// summary:
		//		Init controls with given criteria
		if (criteria.from_date) {
			this._fromDateTextBox.set("value", criteria.from_date);
		}
		if (criteria.to_date) {
			this._toDateTextBox.set("value", criteria.to_date);
		}
	},
	
	////////// CALLBACKS //////////
	
	onRefresh: function() {
		// summary:
		//		Reloads the list of errors
		// tags:
		//		callback
	},
	
	onSearchErrors: function(/*Object*/ criteria) {
		// summary:
		//		Searches for errors by given criteria
		// criteria:
		//		Consists of the following members:
		//		- from_date
		//		- to_date
		// tags:
		//		callback
	},
	
	onSelectModule: function(/*String?*/ module) {
		// summary:
		//		Loads the list of errors in given module
		// tags:
		//		callback
	}
});
