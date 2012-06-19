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
 * @version		2012-04-05
 */

dojo.provide("core.js.controllers.LayoutMediator");

dojo.require("dijit.layout.BorderContainer");
dojo.require("dojox.layout.GridContainer");
dojo.require("dijit.layout.TabContainer");

dojo.require("core.js.views.LayoutPortlet");

dojo.declare("core.js.controllers.LayoutMediator", null, {
	// _layoutContextMenu: core.js.views.LayoutContextMenu
	_layoutContextMenu: null,
	
	// _layoutContainer: core.js.views.LayoutContainer
	_layoutContainer: null,
	
	// _layoutToolbar: core.js.views.LayoutToolbar
	_layoutToolbar: null,
	
	// _layoutTreeView: core.js.views.LayoutTreeView
	_layoutTreeView: null,
	
	setLayoutContextMenu: function(/*core.js.views.LayoutContextMenu*/ layoutContextMenu) {
		// summary:
		//		Sets the context menu of layout container
		this._layoutContextMenu = layoutContextMenu;
		
		dojo.connect(layoutContextMenu, "onContextMenu", this, "onLayoutContextMenu");
	},
	
	setLayoutContainer: function(/*core.js.views.LayoutContainer*/ layoutContainer) {
		// summary:
		//		Sets the layout container
		this._layoutContainer = layoutContainer;
	},
	
	setLayoutToolbar: function(/*core.js.views.LayoutToolbar*/ layoutToolbar) {
		// summary:
		//		Sets the layout toolbar
		this._layoutToolbar = layoutToolbar;
	},
	
	setLayoutTreeView: function(/*core.js.views.LayoutTreeView*/ layoutTreeView) {
		// summary:
		//		Sets the layout tree view
		this._layoutTreeView = layoutTreeView;
		dojo.connect(layoutTreeView, "onNodeContextMenu", this, "onLayoutTreeContextMenu");
	},
	
	onLayoutContextMenu: function(/*dijit.layout.BorderContainer|dojox.layout.GridContainer*/ widget) {
		// summary:
		//		Called after right-clicking a widget
		// widget:
		//		The selected Dijit widget, can be an instance of
		//		dijit.layout.TabContainer, dijit.layout.BorderContainer, dojox.layout.GridContainer
		//		or core.js.views.LayoutPortlet
		switch (true) {
			case (widget instanceof dijit.layout.BorderContainer):
				var canDelete = this._canDeleteBorderContainer(widget);
				
				this._layoutContextMenu.allowToInsertBorderContainer(true)
									   .allowToDeleteBorderContainer(canDelete)
									   .allowToInsertGridContainer(true)
									   .allowToDeleteGridContainer(false)
									   .allowToSetGridColumns(false);
				break;
			case (widget instanceof dojox.layout.GridContainer):
				this._layoutContextMenu.allowToInsertBorderContainer(false)
									   .allowToDeleteBorderContainer(false)
									   .allowToInsertGridContainer(false)
									   .allowToDeleteGridContainer(true)
									   .allowToSetGridColumns(true)
									   .allowToSetGridColumns(false, widget.nbZones);
				break;
			case (widget instanceof core.js.views.LayoutPortlet):
				this._layoutContextMenu.allowToInsertBorderContainer(false)
									   .allowToDeleteBorderContainer(false)
									   .allowToInsertGridContainer(false)
									   .allowToDeleteGridContainer(false)
									   .allowToSetGridColumns(false);
				break;
			default:
				break;
		}
	},
	
	switchToMode: function(/*String*/ mode) {
		// summary:
		//		Called after switching the layout controller to other mode
		// mode:
		//		The new mode, can be "edit", "preview" or "view"
		switch (mode) {
			case "edit":
				if (this._layoutToolbar) {
					this._layoutToolbar.allowToCancel(true)
									   .allowToEdit(false)
									   .allowToSave(true);
				}
				this._layoutContextMenu.attach();
				break;
			case "preview":
				if (this._layoutToolbar) {
					this._layoutToolbar.allowToCancel(false)
									   .allowToEdit(true)
									   .allowToSave(false);
				}
				this._layoutContextMenu.detach();
				break;
			case "view":
				if (this._layoutToolbar) {
					this._layoutToolbar.allowToCancel(false)
									   .allowToEdit(false)
									   .allowToSave(false);
				}
				this._layoutContextMenu.detach();
				break;
			default:
				break;
		}
	},
	
	onLayoutTreeContextMenu: function(/*dojo.data.Item*/ item) {
		// summary:
		//		Called after right-clicking a node on layout tree
		// item:
		//		Item associating with the selected tree node
		var cls = item.cls[0];
		switch (cls) {
			case "dijit.layout.TabContainer":
				this._layoutTreeView.allowToInsertBorderContainer(false)
									.allowToDeleteBorderContainer(false)
									.allowToLayoutBorderContainer(false)
									.allowToInsertGridContainer(true)
									.allowToDeleteGridContainer(false)
									.allowToSetGridColumns(false)
									.allowToActivateTabContainer(false)
									.allowToInsertTabContainer(false)
									.allowToDeleteTabContainer(true)
									.allowToInsertMainContentPane(false)
									.allowToDeleteMainContentPane(false)
									.allowToDeletePortlet(false)
									.allowToSetFilters(false);
				break;
			case "dijit.layout.BorderContainer":
				var container = dijit.byId(item.container_id[0]);
				var canDelete = this._canDeleteBorderContainer(container);
				
				this._layoutTreeView.allowToInsertBorderContainer(true)
									.allowToDeleteBorderContainer(canDelete)
									.allowToLayoutBorderContainer(true)
									.allowToLayoutBorderContainer(false, container.design)
									.allowToInsertGridContainer(true)
									.allowToDeleteGridContainer(false)
									.allowToSetGridColumns(false)
									.allowToActivateTabContainer(false)
									.allowToInsertTabContainer(container.getChildren().length == 0)
									.allowToDeleteTabContainer(false)
									.allowToInsertMainContentPane(container.getChildren().length == 0)
									.allowToDeleteMainContentPane(false)
									.allowToDeletePortlet(false)
									.allowToSetFilters(false);
				break;
			case "dojox.layout.GridContainer":
				var container = dijit.byId(item.container_id[0]);
				var parent	  = container.getParent();
				this._layoutTreeView.allowToInsertBorderContainer(false)
									.allowToDeleteBorderContainer(false)
									.allowToLayoutBorderContainer(false)
									.allowToInsertGridContainer(false)
									.allowToDeleteGridContainer(true)
									.allowToSetGridColumns(true)
									.allowToSetGridColumns(false, item.num_columns[0])
									.allowToActivateTabContainer(parent instanceof dijit.layout.TabContainer)
									.allowToInsertTabContainer(false)
									.allowToDeleteTabContainer(false)
									.allowToInsertMainContentPane(false)
									.allowToDeleteMainContentPane(false)
									.allowToDeletePortlet(false)
									.allowToSetFilters(false);
				break;
			case "gridContainerZone":
				var mainContentPane = this._layoutContainer
									? this._layoutContainer.getMainContentPane()
									: null;
				this._layoutTreeView.allowToInsertBorderContainer(false)
									.allowToDeleteBorderContainer(false)
									.allowToLayoutBorderContainer(false)
									.allowToInsertGridContainer(false)
									.allowToDeleteGridContainer(false)
									.allowToSetGridColumns(false)
									.allowToActivateTabContainer(false)
									.allowToInsertTabContainer(true)
									.allowToDeleteTabContainer(false)
									.allowToInsertMainContentPane(mainContentPane == null)
									.allowToDeleteMainContentPane(false)
									.allowToDeletePortlet(false)
									.allowToSetFilters(false);
				break;
			case "core.js.views.LayoutPortlet":
				this._layoutTreeView.allowToInsertBorderContainer(false)
									.allowToDeleteBorderContainer(false)
									.allowToLayoutBorderContainer(false)
									.allowToInsertGridContainer(false)
									.allowToDeleteGridContainer(false)
									.allowToSetGridColumns(false)
									.allowToActivateTabContainer(false)
									.allowToInsertTabContainer(false)
									.allowToDeleteTabContainer(false)
									.allowToInsertMainContentPane(false)
									.allowToDeleteMainContentPane(false)
									.allowToDeletePortlet(true)
									.allowToSetFilters(true);
				break;
			case "dijit.layout.ContentPane":
				this._layoutTreeView.allowToInsertBorderContainer(false)
									.allowToDeleteBorderContainer(false)
									.allowToLayoutBorderContainer(false)
									.allowToInsertGridContainer(false)
									.allowToDeleteGridContainer(false)
									.allowToSetGridColumns(false)
									.allowToActivateTabContainer(false)
									.allowToInsertTabContainer(false)
									.allowToDeleteTabContainer(false)
									.allowToInsertMainContentPane(false)
									.allowToDeleteMainContentPane(true)
									.allowToDeletePortlet(false)
									.allowToSetFilters(true);
			default:
				break;
		}
	},
	
	_canDeleteBorderContainer: function(/*dijit.layout.BorderContainer*/ container) {
		// summary:
		//		Checks if it is possible to delete border container
		// container:
		//		The border container
		// Don't allow to delete the border container:
		// - if it is the root container
		var canDelete = container.id != this._layoutContainer.getId();
		
		// - if it is the "center" container and there is other containers in other regions
		var parent = dijit.byNode(container.domNode.parentNode);
		if (container.region == "center" && parent && parent.getChildren().length > 1) {
			canDelete = false;
		}
		
		return canDelete;	// Boolean
	}
});
