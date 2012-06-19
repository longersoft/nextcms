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

dojo.provide("tag.js.views.TagContextMenu");

dojo.require("dijit.Menu");
dojo.require("dijit.MenuItem");

dojo.require("core.js.base.controllers.ActionProvider");
dojo.require("core.js.base.I18N");

dojo.declare("tag.js.views.TagContextMenu", null, {
	// _contextMenu: dijit.Menu
	_contextMenu: null,
	
	// _i18n: Object
	_i18n: null,
	
	// _editMenuItem: dijit.MenuItem
	_editMenuItem: null,
	
	// _deleteMenuItem: dijit.MenuItem
	_deleteMenuItem: null,
	
	constructor: function() {
		core.js.base.I18N.requireLocalization("tag/languages");
		this._i18n = core.js.base.I18N.getLocalization("tag/languages");
	},
	
	show: function(/*tag.js.views.TagItemView*/ tagItemView) {
		// summary:
		//		Shows a context menu for each selected tag item
		var _this = this;
		
		// Get tag data
		var tag = tagItemView.getTag();
		
		// Create menu
		this._contextMenu = new dijit.Menu({
			targetNodeIds: [ dojo.attr(tagItemView.getDomNode(), "id") ]
		});
		
		// "Edit" menu item
		this._editMenuItem = new dijit.MenuItem({
			label: this._i18n.global._share.editAction,
			disabled: !core.js.base.controllers.ActionProvider.get("tag_tag_edit").isAllowed,
			onClick: function() {
				_this.onEditTag(tagItemView);
			}
		});
		this._contextMenu.addChild(this._editMenuItem);
		
		// "Delete" menu item
		this._deleteMenuItem = new dijit.MenuItem({
			label: this._i18n.global._share.deleteAction,
			iconClass: "appIcon appDeleteIcon",
			disabled: !core.js.base.controllers.ActionProvider.get("tag_tag_delete").isAllowed,
			onClick: function() {
				_this.onDeleteTag(tagItemView);
			}
		});
		this._contextMenu.addChild(this._deleteMenuItem);
	},
	
	////////// CALLBACKS //////////
	
	onDeleteTag: function(/*tag.js.views.TagItemView*/ tagItemView) {
		// summary:
		//		Deletes given tag item
		// tags:
		//		callback
	},
	
	onEditTag: function(/*tag.js.views.TagItemView*/ tagItemView) {
		// summary:
		//		Edits given tag item
		// tags:
		//		callback
	}
});
