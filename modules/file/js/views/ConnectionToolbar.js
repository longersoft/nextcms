/**
 * NextCMS
 * 
 * @author		Nguyen Huu Phuoc <thenextcms@gmail.com>
 * @copyright	Copyright (c) 2011 - 2012, Nguyen Huu Phuoc
 * @license		http://nextcms.org/license.txt	(GNU GPL version 2 or later)
 * @link		http://nextcms.org
 * @category	modules
 * @package		file
 * @subpackage	js
 * @since		1.0
 * @version		2011-10-18
 */

dojo.provide("file.js.views.ConnectionToolbar");

dojo.require("dijit.form.Button");
dojo.require("dijit.Toolbar");

dojo.require("core.js.base.controllers.ActionProvider");
dojo.require("core.js.base.I18N");

dojo.declare("file.js.views.ConnectionToolbar", null, {
	// _toolbar: dijit.Toolbar
	_toolbar: null,
	
	// _i18n: Object
	_i18n: null,	
	
	constructor: function(/*String*/ id) {
		this._toolbar = new dijit.Toolbar({}, id);
		
		core.js.base.I18N.requireLocalization("file/languages");
		this._i18n = core.js.base.I18N.getLocalization("file/languages");
		
		var _this = this;
		
		// Add button
		this._toolbar.addChild(new dijit.form.Button({
			label: this._i18n.connection.add.newConnectionButton,
			showLabel: false,
			iconClass: "appIcon appAddIcon",
			disabled: !core.js.base.controllers.ActionProvider.get("file_connection_add").isAllowed,
			onClick: function(e) {
				_this.onAddConnection();
			}
		}));
	},
	
	////////// CALLBACKS //////////
	
	onAddConnection: function() {
		// summary:
		//		This method is called when the adding connection button is clicked
		// tags:
		//		callback
	}
});
