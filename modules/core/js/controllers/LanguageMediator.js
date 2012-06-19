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
 * @version		2011-12-11
 */

dojo.provide("core.js.controllers.LanguageMediator");

dojo.require("core.js.base.I18N");

dojo.declare("core.js.controllers.LanguageMediator", null, {
	// _i18n: Object
	_i18n: null,
	
	// _grid: core.js.views.LanguageGrid
	_grid: null,
	
	// _toolbar: core.js.views.LanguageToolbar
	_toolbar: null,
	
	// _gridParentNode: DomNode
	_gridParentNode: null,
	
	// _messageNode: DomNode
	//		The node shows help message
	_messageNode: null,
	
	constructor: function() {
		core.js.base.I18N.requireLocalization("core/languages");
		this._i18n = core.js.base.I18N.getLocalization("core/languages");
	},
	
	setLanguageToolbar: function(/*core.js.views.LanguageToolbar*/ toolbar) {
		// summary:
		//		Sets the translator toolbar
		this._toolbar = toolbar;
		
		// Load the language file handler
		dojo.connect(toolbar, "onLoadLanguage", this, function(file) {
			if (this._messageNode) {
				// Hide the help message
				dojo.style(this._messageNode, "display", "none");
			}
		});
	},
	
	setLanguageGrid: function(/*core.js.views.LanguageGrid*/ grid) {
		// summary:
		//		Sets the translator grid
		this._grid			 = grid;
		this._gridParentNode = dojo.byId(grid.getId()).parentNode;
		
		dojo.connect(this._grid, "onRowContextMenu", this, function(item) {
			// Don't allow to edit and delete the root node
			var isRoot = item.root && item.root[0];
			this._grid.allowToDelete(!isRoot)
					  .allowToEdit(!isRoot);
		});
		
		this._messageNode = dojo.create("div", {
			className: "appCenter",
			innerHTML: "<div>" + this._i18n.language.list.help + "</div>"
		}, this._gridParentNode);
		dojo.connect(this._grid, "onClose", this, function() {
			// Show the help message
			dojo.style(this._messageNode, "display", "block");
		});
	}
});
