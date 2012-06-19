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
 * @version		2012-03-22
 */

dojo.provide("ad.js.Banner");

dojo.require("core.js.Constant");

dojo.declare("ad.js.Banner", null, {
	// _data: Object
	_data: null,
	
	// _zone: ad.js.Zone
	//		The zone that the banner is placed inside
	_zone: null,
	
	constructor: function(/*Object*/ data, /*ad.js.Zone*/ zone) {
		this._data = data;
		this._zone = zone;
	},
	
	match: function() {
		// summary:
		//		Checks if the banner is placed on the current page
		// FIXME:
		return true;	// Bool
	},
	
	render: function() {
		// summary:
		//		Render the banner
		if (!this._data) {
			return;
		}
		var container = this._zone.getId();
		switch (this._data.format) {
			// Render an Image banner
			case "image":
				var a = dojo.create("a", {
					title: this._data.title || "",
					target: this._data.target || "_blank",
					href: this._data.target_url || ""
				}, container);
				var image = dojo.create("img", {
					src: core.js.Constant.normalizeUrl(this._data.url)
				}, a);
				break;
				
			// Render a Flash banner, requires SWFObject library
			case "flash":
				var id = container + "_" + Math.random();
				var div = dojo.create("div", {
					id: id
				}, container);
				swfobject.embedSWF(this._data.url, id,
								   this._zone.getWidth(), this._zone.getHeight(),
								   "9.0.0", "", {}, { allowscriptaccess: "always" }, {});
				break;
				
			// Render a HTML banner
			case "html":
				dojo.byId(container).innerHTML += this._data.code;
				break;
			
			// Render a Javascript banner
			case "javascript":
				var s = dojo.create("script", {
					type: "text/javascript"
				}, container);
				if (this._data.code) {
					s.text = this._data.code;
				} else if (this._data.url) {
					dojo.attr(s, "src", this._data.url);
				}
				break;
		}
	}
});
