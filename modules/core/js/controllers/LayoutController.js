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
 * @version		2012-05-16
 */

dojo.provide("core.js.controllers.LayoutController");

dojo.require("core.js.base.controllers.ActionProvider");
dojo.require("core.js.base.controllers.Subscriber");
dojo.require("core.js.base.I18N");
dojo.require("core.js.base.views.Helper");
dojo.require("core.js.controllers.LayoutMediator");
dojo.require("core.js.LayoutConstant");
dojo.require("core.js.views.LayoutContainer");
dojo.require("core.js.views.LayoutContextMenu");

dojo.declare("core.js.controllers.LayoutController", null, {
	// _id: String
	_id: null,
	
	// _i18n: Object
	_i18n: null,
	
	// _helper: core.js.base.views.Helper
	_helper: null,
	
	// _mode: String
	//		Can be "show" or "config"
	_mode: "show",
	
	// _layoutContextMenu: core.js.views.LayoutContextMenu
	_layoutContextMenu: null,
	
	// _layoutContainer: core.js.views.LayoutContainer
	_layoutContainer: null,
	
	// _layoutToolbar: core.js.views.LayoutToolbar
	_layoutToolbar: null,
	
	// _layoutTreeView: core.js.views.LayoutTreeView
	_layoutTreeView: null,
	
	// _mediator: core.js.controllers.LayoutMediator
	_mediator: new core.js.controllers.LayoutMediator(),
	
	// TOPIC_GROUP: [const] String
	TOPIC_GROUP: "/core/js/controllers/LayoutController",
	
	constructor: function(/*String*/ id) {
		core.js.base.controllers.Subscriber.unsubscribe(this.TOPIC_GROUP);
		this._id = id;
		
		core.js.base.I18N.requireLocalization("core/languages");
		this._i18n = core.js.base.I18N.getLocalization("core/languages");
		
		// Create helper instance
		this._helper = new core.js.base.views.Helper(id);
		this._helper.setLanguageData(this._i18n);
		
		this._layoutContextMenu = new core.js.views.LayoutContextMenu(id, {
			minGridColumns: 1,
			maxGridColumns: 10
		});
		this._layoutContainer = new core.js.views.LayoutContainer(id);
		
		this._mediator.setLayoutContextMenu(this._layoutContextMenu);
		this._mediator.setLayoutContainer(this._layoutContainer);
		
		this._initContextMenu();
	},
	
	getLayoutContainer: function() {
		// summary:
		//		Gets the instance of layout container
		return this._layoutContainer;	// core.js.views.LayoutContainer
	},
	
	setLayoutToolbar: function(/*core.js.views.LayoutToolbar*/ layoutToolbar) {
		// summary:
		//		Sets the layout toolbar
		this._layoutToolbar = layoutToolbar;
		this._mediator.setLayoutToolbar(layoutToolbar);
		
		dojo.connect(layoutToolbar, "onCancel", this, function() {
			this.switchToMode("preview");
		});
		dojo.connect(layoutToolbar, "onEditLayout", this, function() {
			this.switchToMode("edit");
		});
		dojo.connect(layoutToolbar, "onSaveLayout", this, function() {
			this.onSaveLayout(this.getLayoutData());
		});
		return this;	// core.js.controllers.LayoutController
	},
	
	_initContextMenu: function() {
		// summary:
		//		Handles the context menu's events
		dojo.connect(this._layoutContextMenu, "onAddBorderContainer", this, "addBorderContainer");
		dojo.connect(this._layoutContextMenu, "onAddGridContainer", this, "addGridContainer");
		dojo.connect(this._layoutContextMenu, "onDeleteBorderContainer", this, "deleteBorderContainer");
		dojo.connect(this._layoutContextMenu, "onDeleteGridContainer", this, "deleteGridContainer");
		dojo.connect(this._layoutContextMenu, "onSetGridColumns", this, "setGridColumns");
	},
	
	addBorderContainer: function(/*dijit.layout.BorderContainer*/ container, /*String*/ region) {
		// summary:
		//		Adds border container to selected border container
		// container:
		//		The selected container
		// region:
		//		The position the new container will be placed.
		//		Can be "top", "bottom", "left", "right" or "center"
		return this._layoutContainer.addBorderContainer(container, region);
	},
	
	addGridContainer: function(/*dijit.layout.BorderContainer*/ container) {
		// summary:
		//		Adds a grid container to the selected border container
		// container:
		//		The selected border container
		return this._layoutContainer.addGridContainer(container);	// dojox.layout.GridContainer
	},
	
	deleteBorderContainer: function(/*dijit.layout.BorderContainer*/ container) {
		// summary:
		//		Deletes given border container
		// container:
		//		The border container
		this._layoutContainer.deleteBorderContainer(container);
	},
	
	deleteGridContainer: function(/*dojox.layout.GridContainer*/ container) {
		// summary:
		//		Deletes given grid container
		// container:
		//		The grid container
		this._layoutContainer.deleteGridContainer(container);
	},
	
	setGridColumns: function(/*dojox.layout.GridContainer*/ container, /*Integer*/ numColumns) {
		// summary:
		//		Sets the number of columns to given grid container
		// container:
		//		The grid container
		// numColumns:
		//		The number of columns
		this._layoutContainer.setGridColumns(container, numColumns);
	},
	
	addPrefixToFields: function(/*String*/ prefix) {
		// summary:
		//		When putting the widgets inside the content editor, before submiting form 
		//		we need to add prefix to all widget fields to make them different 
		//		with the form's fields.
		dojo.query("." + core.js.LayoutConstant.WIDGET_INPUT_CLASS, this._id).forEach(function(node) {
			var name = dojo.attr(node, "name");
			if (name) {
				dojo.attr(node, "name", prefix + name);
			}
		});
	},
	
	setLayoutData: function(/*Object*/ data) {
		// summary:
		//		Sets the layout data
		this._layoutContainer.setLayoutData(data);
		return this;	// core.js.controllers.LayoutController
	},
	
	switchToMode: function(/*String*/ mode) {
		// summary:
		//		Switches to view or edit mode
		// mode:
		//		Can be "view" (all the buttons will be disabled), "preview", "edit"
		switch (mode) {
			case "edit":
				this._layoutContainer.switchToEditMode();
				break;
			case "preview":
			case "view":
				this._layoutContainer.switchToViewMode();
				break;
			default:
				break;
		}
		this._mediator.switchToMode(mode);
		
		return this;	// core.js.controllers.LayoutController
	},
	
	getHtmlData: function() {
		// summary:
		//		Gets the HTML data returned by the layout container
		return this._layoutContainer.getHtmlData();		// String
	},
	
	getLayoutData: function() {
		// summary:
		//		Gets the layout data
		return this._layoutContainer.getLayoutData();	// Object
	},
	
	setLayoutTreeView: function(/*core.js.views.LayoutTreeView*/ layoutTreeView) {
		// summary:
		//		Sets the layout tree
		this._layoutTreeView = layoutTreeView;
		
		this._mediator.setLayoutTreeView(layoutTreeView);
		
		// Set filter handler
		dojo.connect(layoutTreeView, "onSetFilters", this, "setFilters");
		core.js.base.controllers.Subscriber.subscribe(this.TOPIC_GROUP, "/app/core/page/filter/onCancel", this, function() {
			this._helper.closeDialog();
		});
		core.js.base.controllers.Subscriber.subscribe(this.TOPIC_GROUP, "/app/core/page/filter/onSave", this, function(data) {
			this._helper.closeDialog();
			if (data.filters || data.classes) {
				var filters = dojo.isArray(data.filters) ? data.filters : [data.filters];
				if (data.classes) {
					filters = filters.concat(data.classes.split(","));
				}
				this._layoutTreeView.setFilters(data.container_id, filters);
			} else {
				this._layoutTreeView.setFilters(data.container_id, []);
			}
		});
		
		// Set properties handler
		dojo.connect(layoutTreeView, "onSetProperties", this, "setProperties");
		core.js.base.controllers.Subscriber.subscribe(this.TOPIC_GROUP, "/app/core/page/property/onCancel", this, function() {
			this._helper.closeDialog();
		});
		core.js.base.controllers.Subscriber.subscribe(this.TOPIC_GROUP, "/app/core/page/property/onSave", this, function(data) {
			this._helper.closeDialog();
			this._layoutTreeView.setProperties(data.container_id, data);
		});
		
		return this;	// core.js.controllers.LayoutController
	},
	
	setFilters: function(/*dojo.data.Item*/ item) {
		// summary:
		//		Sets filters to given portlet
		var params = {
			container_id: item.container_id[0],
			filters: item.filters ? item.filters[0] : null
		};
		var url = core.js.base.controllers.ActionProvider.get("core_page_filter").url + "?" + dojo.objectToQuery(params);
		this._helper.showDialog(url, {
			title: this._i18n.page.filter.title,
			style: "width: 300px",
			refreshOnShow: true
		});
	},
	
	setProperties: function(/*dojo.data.Item*/ item) {
		// summary:
		//		Sets properties to given item
		var params = {
			container_id: item.container_id[0],
			cls: item.cls ? item.cls[0] : "dijit.layout.BorderContainer",
			properties: item.properties ? item.properties[0] : null
		};
		var url = core.js.base.controllers.ActionProvider.get("core_page_property").url + "?" + dojo.objectToQuery(params);
		this._helper.showDialog(url, {
			title: this._i18n.page.property.title,
			style: "width: 450px",
			refreshOnShow: true
		});
	},
	
	////////// CALLBACKS //////////
	
	onSaveLayout: function(/*Object*/ layoutData) {
		// summary:
		//		Saves the layout. Called when user click on the Save button on the toolbar
		// tags:
		//		callback
	}
});
