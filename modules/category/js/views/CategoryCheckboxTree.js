/**
 * NextCMS
 * 
 * @author		Nguyen Huu Phuoc <thenextcms@gmail.com>
 * @copyright	Copyright (c) 2011 - 2012, Nguyen Huu Phuoc
 * @license		http://nextcms.org/license.txt	(GNU GPL version 2 or later)
 * @link		http://nextcms.org
 * @category	modules
 * @package		category
 * @subpackage	js
 * @since		1.0
 * @version		2011-10-18
 */

dojo.provide("category.js.views.CategoryCheckboxTree");

dojo.require("dijit.form.CheckBox");
dojo.require("dijit.Tree");
dojo.require("dijit.tree.ForestStoreModel");
dojo.require("dojo.data.ItemFileReadStore");

dojo.require("core.js.base.controllers.ActionProvider");

dojo.declare("category.js.views.CategoryCheckboxTree", null, {
	// _id: String
	_id: null,
	
	// _module: String
	_module: null,
	
	// _language: String
	_language: null,
	
	// _params: Array
	_params: {
		name: "categories[]",
		selected: [],
		disabled: false
	},
	
	constructor: function(/*String*/ id, /*String*/ module, /*String*/ language, /*Object*/ params) {
		// params:
		//		Contains the following members:
		//		- name [String]: Name of checkboxes
		//		- selected [String]: Ids of selected categories, separated by a comma
		//		- disabled [Boolean]
		this._id	   = id;
		this._module   = module;
		this._language = language;
		
		params.selected = params.selected ? params.selected.split(",") : [];
		dojo.mixin(this._params, params);
		
		this._createTree();
	},
	
	_createTree: function() {
		// summary:
		//		Creates the tree
		var url = core.js.base.controllers.ActionProvider.get("category_category_list").url;
		var params = { 
			format: "json", 
			mod: this._module,
			language: this._language
		};
		
		var _this = this;
		var div	  = dojo.create("div", {
			id: this._id
		}, this._parentNode);
		var store = new dojo.data.ItemFileReadStore({ 
			url: url + "?" + dojo.objectToQuery(params)
		});
		var model = new dijit.tree.ForestStoreModel({
			store: store
		});
		var _this = this;
		var tree = new dijit.Tree({
			model: model,
			showRoot: false,
			getIconClass: function(/*dojo.data.Item*/ item, /*Boolean*/ opened) {
				return '';
			},
			// DOJO LESSON: Override _createTreeNode() to add a checkbox to each node
			_createTreeNode: function(/*Object*/ args) {
				var node = new dijit._TreeNode(args);
				if (args.item.root == true) {
					return node;
				}
				
				var categoryId   = args.item.category_id[0] + "";
				var checkboxNode = dojo.create("input", {}, node.labelNode, "before");
				var checked		 = dojo.indexOf(_this._params.selected, categoryId) != -1;
				var checkbox	 = new dijit.form.CheckBox({
					name: _this._params.name,
					value: categoryId,
					checked: checked,
					disabled: _this._params.disabled,
					onClick: function() {
						this.checked ? dojo.attr(this.focusNode, "checked", "checked") : dojo.removeAttr(this.focusNode, "checked");
						this.checked ? dojo.attr(this.focusNode, "data-app-checked", "checked") : dojo.removeAttr(this.focusNode, "data-app-checked");
					}
				}, checkboxNode);
				if (checked) {
					dojo.attr(checkbox.focusNode, "data-app-checked", "checked");
				}
				
				return node;
			}
		}, this._id);
	}
});
