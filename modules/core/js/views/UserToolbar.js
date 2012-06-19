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

dojo.provide("core.js.views.UserToolbar");

dojo.require("dijit.form.Button");
dojo.require("dijit.form.Select");
dojo.require("dijit.form.TextBox");
dojo.require("dijit.Toolbar");
dojo.require("dijit.ToolbarSeparator");

dojo.require("core.js.base.controllers.ActionProvider");
dojo.require("core.js.base.I18N");

dojo.declare("core.js.views.UserToolbar", null, {
	// _toolbar: dijit.Toolbar
	_toolbar: null,
	
	// _searchTextBox: dijit.form.TextBox
	_searchTextBox: null,
	
	// _refreshButton: dijit.form.Button
	_refreshButton: null,
	
	// _perPageSelect: dijit.form.Select
	_perPageSelect: null,
	
	// _numFormsPerPage: Array
	_numFormsPerPage: [ 20, 40, 60, 80, 100 ],	
	
	// _i18n: Object
	_i18n: null,
	
	constructor: function(/*String*/ id) {
		this._toolbar = new dijit.Toolbar({}, id);
		
		core.js.base.I18N.requireLocalization("core/languages");
		this._i18n = core.js.base.I18N.getLocalization("core/languages");
		
		var _this = this;
		
		// "Add" button
		this._toolbar.addChild(new dijit.form.Button({
			label: this._i18n.user.add.newUserButton,
			showLabel: false,
			iconClass: "appIcon coreAddUserIcon",
			disabled: !core.js.base.controllers.ActionProvider.get("core_user_add").isAllowed,
			onClick: function(e) {
				_this.onAddUser();
			}
		}));
		
		// "Refresh" button
		this._refreshButton = new dijit.form.Button({
			label: this._i18n.global._share.refreshAction,
			showLabel: false,
			iconClass: "appIcon appRefreshIcon",
			disabled: !core.js.base.controllers.ActionProvider.get("core_user_list").isAllowed,
			onClick: function(e) {
				_this.onRefresh();
			}
		});
		this._toolbar.addChild(this._refreshButton);
		
		this._toolbar.addChild(new dijit.ToolbarSeparator());
		
		// Search controls
		this._searchTextBox = new dijit.form.TextBox({
			style: "width: 200px",
			placeHolder: this._i18n.user.list.searchUserHelp
		});
		this._toolbar.addChild(this._searchTextBox);
		this._toolbar.addChild(new dijit.form.Button({
			label: this._i18n.global._share.searchAction,
			showLabel: false,
			iconClass: "appIcon appSearchIcon",
			disabled: !core.js.base.controllers.ActionProvider.get("core_user_list").isAllowed,
			onClick: function(e) {
				var keyword = _this._searchTextBox.get("value");
				_this.onSearchUser(keyword);
			}
		}));
		
		var options = [];
		dojo.forEach(this._numFormsPerPage, function(value, index) {
			options.push({
				label: value,
				value: value + ""
			});
		});
		this._perPageSelect = new dijit.form.Select({ 
			options: options, 
			style: "height: 20px",
			disabled: !core.js.base.controllers.ActionProvider.get("core_user_list").isAllowed,
			onChange: function(value) {
				_this.onUpdatePageSize(parseInt(value));
			}
		});
		dojo.addClass(this._perPageSelect.domNode, "appRight");
		this._toolbar.addChild(this._perPageSelect);
	},
	
	initSearchCriteria: function(/*Object*/ criteria) {
		// summary:
		//		Inits the controls based on given criteria
		this._searchTextBox.set("value", criteria.keyword || "");
		this._perPageSelect.set("value", (criteria.per_page || 20) + "");
	},
	
	////////// CALLBACKS //////////
	
	onAddUser: function() {
		// summary:
		//		This method is called when the adding user button is clicked
		// tags:
		//		callback
	},
	
	onRefresh: function() {
		// summary:
		//		Reloads the list of users by current criteria
		// tags:
		//		callback
	},
	
	onSearchUser: function(/*String*/ keyword) {
		// summary:
		//		This method is called when searching is performed
		// keyword:
		//		The search keyword
		// tags:
		//		callback
	},
	
	onUpdatePageSize: function(/*Integer*/ perPage) {
		// summary:
		//		This method is called when the page size select changes its value
		// perPage:
		//		The number of users per page
		// tags:
		//		callback
	}
});
