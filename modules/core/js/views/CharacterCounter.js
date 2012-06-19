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
 * @version		2012-02-18
 */

dojo.provide("core.js.views.CharacterCounter");

dojo.require("dojox.string.sprintf");

dojo.require("core.js.base.I18N");

dojo.declare("core.js.views.CharacterCounter", null, {
	// _container: DomNode
	_container: null,
	
	// _i18n: Object
	_i18n: null,
	
	constructor: function(/*dijit.form.TextBox*/ textbox) {
		core.js.base.I18N.requireLocalization("core/languages");
		this._i18n = core.js.base.I18N.getLocalization("core/languages");
		
		// Create a container to show the number of characters
		this._container = dojo.create("span", {
			style: "padding: 0 5px; vertical-align: top"
		}, textbox.domNode, "after");
		
		this._updateNumCharacters(textbox.get("value").length);
		
		dojo.connect(textbox, "onChange", this, function(value) {
			this._updateNumCharacters(value.length);
		});
		dojo.connect(textbox, "onKeyDown", this, function() {
			var n = textbox.get("value").length;
			this._updateNumCharacters(n);
		});
		dojo.connect(textbox, "onKeyUp", this, function() {
			var n = textbox.get("value").length;
			this._updateNumCharacters(n);
		});
	},
	
	_updateNumCharacters: function(numCharacters) {
		// summary:
		//		Shows the total number of characters
		dojo.attr(this._container, "innerHTML", (numCharacters == 0) ? "" : dojox.string.sprintf(this._i18n.global._share.characters, numCharacters));
	}
});
