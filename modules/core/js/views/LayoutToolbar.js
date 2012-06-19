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
 * @version		2012-02-28
 */

dojo.provide("core.js.views.LayoutToolbar");

dojo.require("dijit.form.Button");
dojo.require("dijit.Toolbar");
dojo.require("dijit.ToolbarSeparator");

dojo.require("core.js.base.I18N");

dojo.declare("core.js.views.LayoutToolbar", null, {
	// _toolbar: dijit.Toolbar
	_toolbar: null,
	
	// _i18n: Object
	_i18n: null,
	
	// _editButton: dijit.form.Button
	_editButton: null,
	
	// _saveButton: dijit.form.Button
	_saveButton: null,
	
	// _cancelButton: dijit.form.Button
	_cancelButton: null,
	
	constructor: function(/*String*/ id) {
		this._toolbar = new dijit.Toolbar({}, id);
		
		core.js.base.I18N.requireLocalization("core/languages");
		this._i18n = core.js.base.I18N.getLocalization("core/languages");
	
		// Add toolbar items
		var _this = this;
		
		// "Edit" button
		this._editButton = new dijit.form.Button({
			label: _this._i18n.page._share.editAction,
			showLabel: true,
			disabled: false,
			iconClass: "appIcon appEditLayoutIcon",
			onClick: function(e) {
				_this.onEditLayout();
			}
		});
		this._toolbar.addChild(this._editButton);
		
		// "Save" button
		this._saveButton = new dijit.form.Button({
			label: _this._i18n.global._share.saveAction,
			showLabel: true,
			disabled: true,
			iconClass: "appIcon appSaveIcon",
			onClick: function(e) {
				_this.onSaveLayout();
			}
		});
		this._toolbar.addChild(this._saveButton);
		
		// "Edit" button
		this._cancelButton = new dijit.form.Button({
			label: _this._i18n.global._share.cancelAction,
			showLabel: true,
			disabled: true,
			iconClass: "appIcon appCancelIcon",
			onClick: function(e) {
				_this.onCancel();
			}
		});
		this._toolbar.addChild(this._cancelButton);
	},
	
	////////// CONTROL STATE //////////
	
	allowToCancel: function(/*Boolean*/ isAllowed) {
		// summary:
		//		Allows/disallows to cancel the updating
		this._cancelButton.set("disabled", !isAllowed);
		return this;	// core.js.views.LayoutToolbar
	},
	
	allowToEdit: function(/*Boolean*/ isAllowed) {
		// summary:
		//		Allows/disallows to edit the layout
		this._editButton.set("disabled", !isAllowed);
		return this;	// core.js.views.LayoutToolbar
	},
	
	allowToSave: function(/*Boolean*/ isAllowed) {
		// summary:
		//		Allows/disallows to save the layout
		this._saveButton.set("disabled", !isAllowed);
		return this;	// core.js.views.LayoutToolbar
	},
	
	////////// CALLBACKS //////////
	
	onCancel: function() {
		// summary:
		//		Cancels the layout updating
		// tags:
		//		callback
	},
	
	onEditLayout: function() {
		// summary:
		//		Edits the layout
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
