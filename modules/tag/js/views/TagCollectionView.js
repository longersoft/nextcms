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
 * @version		2012-05-23
 */

dojo.provide("tag.js.views.TagCollectionView");

dojo.require("dojo.dnd.Source");

dojo.require("core.js.base.Encoder");

dojo.declare("tag.js.views.TagCollectionView", null, {
	// summary:
	//		This class shows the list of tags which can be dragged from the Tags Provider
	
	// _id: String
	_id: null,
	
	// _clearDiv: DomNode
	_clearDiv: null,
	
	// _inputName: String
	_inputName: null,
	
	// _tags: Array
	//		Map tag's Id with tag's properties
	_tags: {},
	
	constructor: function(/*String*/ id, /*String?*/ inputName) {
		// inputName:
		//		If this param is defined, the view will generate hidden inputs
		//		containing the tag's Ids
		this._id		= id;
		this._inputName = inputName;
		this._tags		= {};
		
		dojo.addClass(this._id, "tagTagCollectionItemsContainer");
		this._clearDiv = dojo.create("div", {
			style: "clear: both"
		}, this._id);
		
		this._init();
	},
	
	_init: function() {
		// summary:
		//		Allows to drag a tag item from the Tags Provider
		//		and drop to the view container
		var _this = this;
		
		new dojo.dnd.Target(this._id, {
			accept: ["tagTagItemDnd"],
			onDropExternal: function(source, nodes, copy) {
				dojo.forEach(nodes, function(node) {
					var tag = core.js.base.Encoder.decode(dojo.attr(node, "data-app-entity-props"));
					_this.addTag(tag);
				});
			}
		});
	},
	
	addTag: function(/*Object*/ tag) {
		// summary:
		//		Adds a tag to the collection
		// tag:
		//		Tag's properties
		var _this = this;
		if (!tag.tag_id || this._tags[tag.tag_id]) {
			return;
		}
		this._tags[tag.tag_id] = tag;
		
		// Add a DIV container to show the tag's title
		var div = dojo.create("div", {
			className: "tagTagCollectionItem"
		}, this._clearDiv, "before");
		var span = dojo.create("span", {
			innerHTML: tag.title
		}, div);
		
		// Remove the div if click on the tag's title
		dojo.connect(span, "onclick", function() {
			dojo.query(div).orphan();
			delete _this._tags[tag.tag_id];
		});
		
		// Add hidden input
		if (this._inputName) {
			dojo.create("input", {
				type: "hidden",
				name: this._inputName,
				value: tag.tag_id
			}, div);
		}
	}
});
