/**
 * NextCMS
 * 
 * @author		Nguyen Huu Phuoc <thenextcms@gmail.com>
 * @copyright	Copyright (c) 2011 - 2012, Nguyen Huu Phuoc
 * @license		http://nextcms.org/license.txt	(GNU GPL version 2 or later)
 * @link		http://nextcms.org
 * @category	modules
 * @package		message
 * @subpackage	js
 * @since		1.0
 * @version		2012-02-28
 */

dojo.provide("message.js.views.MessageToolbar");

dojo.require("dijit.form.Button");
dojo.require("dijit.form.Select");
dojo.require("dijit.form.TextBox");
dojo.require("dijit.Toolbar");
dojo.require("dijit.ToolbarSeparator");

dojo.require("core.js.base.controllers.ActionProvider");
dojo.require("core.js.base.I18N");

dojo.declare("message.js.views.MessageToolbar", null, {
	// _id: String
	_id: null,
	
	// _toolbar: dijit.Toolbar
	_toolbar: null,
	
	// _i18n: Object
	_i18n: null,
	
	// _emptyTrashButton: dijit.form.Button
	_emptyTrashButton: null,
	
	// _searchTextBox: dijit.form.TextBox
	_searchTextBox: null,
	
	// _searchButton: dijit.form.Button
	_searchButton: null,
	
	// _perPageSelect: dijit.form.Select
	_perPageSelect: null,
	
	// _numMessagesPerPage: Array
	_numMessagesPerPage: [ 20, 40, 60, 80, 100 ],
	
	// _numDeletedMessages: Integer
	//		Number of messages in the trash
	_numDeletedMessages: 0,
	
	constructor: function(/*String*/ id) {
		this._id = id;
		
		core.js.base.I18N.requireLocalization("message/languages");
		this._i18n = core.js.base.I18N.getLocalization("message/languages");
		
		this._createToolbar();
	},
	
	_createToolbar: function() {
		// summary:
		//		Creates the toolbar
		this._toolbar = new dijit.Toolbar({}, this._id);
		var _this   = this;
		
		// "Send" button
		this._toolbar.addChild(new dijit.form.Button({
			label: this._i18n.global._share.sendAction,
			showLabel: false,
			iconClass: "appIcon messageSendIcon",
			disabled: !core.js.base.controllers.ActionProvider.get("message_message_send").isAllowed,
			onClick: function(e) {
				_this.onSendMessage();
			}
		}));
		
		// "Refresh" button
		this._toolbar.addChild(new dijit.form.Button({
			label: this._i18n.global._share.refreshAction,
			showLabel: false,
			iconClass: "appIcon appRefreshIcon",
			disabled: !core.js.base.controllers.ActionProvider.get("message_message_list").isAllowed,
			onClick: function(e) {
				_this.onRefresh();
			}
		}));
		
		this._toolbar.addChild(new dijit.ToolbarSeparator());
		
		// "Empty trash" button
		this._emptyTrashButton = new dijit.form.Button({
			label: this._i18n.global._share.emptyTrashAction,
			showLabel: false,
			iconClass: "appIcon appTrashEmptyIcon",
			disabled: !core.js.base.controllers.ActionProvider.get("message_message_empty").isAllowed,
			onClick: function(e) {
				_this.onEmptyTrash();
			}
		});
		this._toolbar.addChild(this._emptyTrashButton);
		
		this._toolbar.addChild(new dijit.ToolbarSeparator());
		
		// Search control
		this._searchTextBox = new dijit.form.TextBox({
			style: "width: 150px",
			placeHolder: this._i18n.message.list.searchMessageHelp
		});
		this._searchButton = new dijit.form.Button({
			label: this._i18n.global._share.searchAction,
			showLabel: false,
			iconClass: "appIcon appSearchIcon",
			disabled: !core.js.base.controllers.ActionProvider.get("message_message_list").isAllowed,
			onClick: function(e) {
				var keyword = _this._searchTextBox.get("value");
				_this.onSearchMessages(keyword);
			}
		});
		this._toolbar.addChild(this._searchTextBox);
		this._toolbar.addChild(this._searchButton);
		
		// Select the number of articles per page
		var options = [];
		dojo.forEach(this._numMessagesPerPage, function(value, index) {
			options.push({
				label: value,
				value: value + ""
			});
		});
		this._perPageSelect = new dijit.form.Select({ 
			options: options, 
			style: "height: 20px",
			disabled: !core.js.base.controllers.ActionProvider.get("message_message_list").isAllowed,
			onChange: function(value) {
				_this.onUpdatePageSize(parseInt(value));
			}
		});
		dojo.addClass(this._perPageSelect.domNode, "appRight");
		this._toolbar.addChild(this._perPageSelect);
	},
	
	initSearchCriteria: function(/*Object*/ criteria) {
		// summary:
		//		Inits controls with given criteria
		if (criteria.per_page) {
			this._perPageSelect.set("value", criteria.per_page + "");
		}
		this.allowToEmptyTrash(criteria.deleted == "1");
	},
	
	increaseDeletedMessages: function(/*Integer*/ increasingNumber) {
		// summary:
		//		Increases (or decreases) the number of deleted messages
		this.setNumDeletedMessages(this._numDeletedMessages + increasingNumber);
	},
	
	setNumDeletedMessages: function(/*Integer*/ numDeletedMessages) {
		// summary:
		//		Sets the number of deleted messages
		this._numDeletedMessages = numDeletedMessages;
		this._emptyTrashButton.set("iconClass", "appIcon " + ((numDeletedMessages == 0) ? "appTrashEmptyIcon" : "appTrashFullIcon"));
	},
	
	setTrashEmpty: function() {
		// summary:
		//		Sets the trash as empty
		this.setNumDeletedMessages(0);
	},
	
	////////// CONTROL STATE OF CONTROLS //////////
	
	allowToEmptyTrash: function(/*Boolean*/ isAllowed) {
		// summary:
		//		Allows/disallows to empty the trash
		isAllowed = isAllowed && core.js.base.controllers.ActionProvider.get("message_message_empty").isAllowed;
		this._emptyTrashButton.set("disabled", !isAllowed);
		return this;	// message.js.views.MessageToolbar
	},
	
	////////// CALLBACKS //////////
	
	onEmptyTrash: function() {
		// summary:
		//		Empties the trash
		// tags:
		//		callback		
	},
	
	onRefresh: function() {
		// summary:
		//		Reloads the list of messages
		// tags:
		//		callback
	},
	
	onSearchMessages: function(/*String*/ keyword) {
		// summary:
		//		Searches for messages by given keyword
		// tags:
		//		callback
	},
	
	onSendMessage: function() {
		// summary:
		//		Sends new message
		// tags:
		//		callback
	},
	
	onUpdatePageSize: function(/*Integer*/ perPage) {
		// summary:
		//		Called when the page size select changes its value
		// perPage:
		//		The number of messages per page
		// tags:
		//		callback
	}
});
