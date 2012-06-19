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

dojo.provide("ad.js.Zone");

dojo.require("ad.js.Banner");

dojo.declare("ad.js.Zone", null, {
	// _id: String
	//		Container of zone
	_id: null,
	
	// _width: String
	//		Width of zone
	_width: null,
	
	// _height: String
	//		Height of zone
	_height: null,
	
	// _banners: Array
	//		Array of banners
	_banners: Array(),
	
	constructor: function(/*String*/ id, /*String*/ width, /*String*/ height) {
		this._id	  = id;
		this._banners = Array();
		this._width   = width;
		this._height  = height;
	},
	
	getId: function() {
		// summary:
		//		Gets the zone container
		return this._id;	// String
	},
	
	getWidth: function() {
		// summary:
		//		Gets the width of zone
		return this._width;		// String
	},
	
	getHeight: function() {
		// summary:
		//		Gets the height of zone
		return this._height;	// String
	},
	
	addBanner: function(/*Object*/ data) {
		// summary:
		//		Add a banner to zone
		var banner = new ad.js.Banner(data, this);
		if (banner.match()) {
			this._banners.push(banner);
		}
		return this;	// ad.js.Zone
	},
	
	addBanners: function(/*Object[]*/ banners) {
		// summary:
		//		Add multiple banners to zone
		for (var i in banners) {
			this.addBanner(banners[i]);
		}
		return this;	// ad.js.Zone
	},
	
	render: function() {
		// summary:
		//		Render all banners in zone
		for (var i in this._banners) {
			this._banners[i].render();
		}
	}
});
