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
 * @version		2012-04-17
 */

dojo.provide("core.js.views.TaskGrid");

dojo.require("dijit.Menu");
dojo.require("dijit.MenuItem");
dojo.require("dijit.MenuSeparator");
dojo.require("dojo.data.ItemFileReadStore");
dojo.require("dojox.grid.EnhancedGrid");
dojo.require("dojox.grid.enhanced.plugins.Menu");
dojo.require("dojox.widget.PlaceholderMenuItem");

dojo.require("core.js.base.controllers.ActionProvider");
dojo.require("core.js.base.I18N");

dojo.declare("core.js.views.TaskGrid", null, {
	// _id: String
	_id: null,
	
	// _i18n: Object
	_i18n: null,
	
	// _taskGrid: dojox.grid.EnhancedGrid
	_taskGrid: null,
	
	_cellMenu: null,
	
	// _installMenuItem: dijit.MenuItem
	_installMenuItem: null,
	
	// _configMenuItem: dijit.MenuItem
	_configMenuItem: null,
	
	// _scheduleMenuItem: dijit.MenuItem
	_scheduleMenuItem: null,
	
	// _runMenuItem: dijit.MenuItem
	_runMenuItem: null,
	
	constructor: function(/*String*/ id) {
		this._id = id;
		
		core.js.base.I18N.requireLocalization("core/languages");
		this._i18n = core.js.base.I18N.getLocalization("core/languages");
		
		this._createGrid();
	},
	
	_createGrid: function() {
		// summary:
		//		Creates the grid
		var _this = this;
		
		// Columns
		var layout = [{
			field: "name",
			width: "200px",
			name: this._i18n.task._share.name
		}, {
			field: "title",
			width: "300px",
			name: this._i18n.task._share.title
		}, {
			field: "description",
			width: "400px",
			name: this._i18n.task._share.description
		}, {
			field: "last_run",
			width: "150px",
			name: this._i18n.task._share.lastRun
		}, {
			field: "next_run",
			width: "150px",
			name: this._i18n.task._share.nextRun
		}];
		
		// Header menu
		var headerMenu = new dijit.Menu();
		headerMenu.addChild(new dijit.MenuItem({
			label: this._i18n.task.list.showColumns,
			disabled: true
		}));
		headerMenu.addChild(new dijit.MenuSeparator());
		headerMenu.addChild(new dojox.widget.PlaceholderMenuItem({
			label: "GridColumns"
		}));
		headerMenu.startup();
		
		// Cell context menu
		this._cellMenu = new dijit.Menu();
		
		// "Install" menu item
		this._installMenuItem = new dijit.MenuItem({
			label: this._i18n.global._share.installAction,
			disabled: !core.js.base.controllers.ActionProvider.get("core_task_install").isAllowed,
			onClick: function(e) {
				var rowIndex = _this._taskGrid.selection.selectedIndex;
				var item = _this._taskGrid.getItem(rowIndex);
				if (item) {
					_this.onInstallTask(item);
				}
			}
		});
		this._cellMenu.addChild(this._installMenuItem);
		
		// "Config" menu item
		this._configMenuItem = new dijit.MenuItem({
			label: this._i18n.global._share.configureAction,
			iconClass: "appIcon appConfigIcon",
			disabled: !core.js.base.controllers.ActionProvider.get("core_task_config").isAllowed,
			onClick: function(e) {
				var rowIndex = _this._taskGrid.selection.selectedIndex;
				var item = _this._taskGrid.getItem(rowIndex);
				if (item) {
					_this.onConfigTask(item);
				}
			}
		});
		this._cellMenu.addChild(this._configMenuItem);
		
		// "Schedule" menu item
		this._scheduleMenuItem = new dijit.MenuItem({
			label: this._i18n.global._share.scheduleAction,
			iconClass: "appIcon coreTaskScheduleIcon",
			disabled: !core.js.base.controllers.ActionProvider.get("core_task_schedule").isAllowed,
			onClick: function(e) {
				var rowIndex = _this._taskGrid.selection.selectedIndex;
				var item = _this._taskGrid.getItem(rowIndex);
				if (item) {
					_this.onScheduleTask(item);
				}
			}
		});
		this._cellMenu.addChild(this._scheduleMenuItem);
		
		// "Run" menu item
		this._runMenuItem = new dijit.MenuItem({
			label: this._i18n.global._share.runAction,
			iconClass: "appIcon appRunIcon",
			disabled: !core.js.base.controllers.ActionProvider.get("core_task_run").isAllowed,
			onClick: function(e) {
				var rowIndex = _this._taskGrid.selection.selectedIndex;
				var item = _this._taskGrid.getItem(rowIndex);
				if (item) {
					_this.onRunTask(item);
				}
			}
		});
		this._cellMenu.addChild(this._runMenuItem);
		
		// Create grid
		this._taskGrid = new dojox.grid.EnhancedGrid({
			clientSort: false,
			rowSelector: "20px",
			style: "height: 100%; width: 100%; visibility: hidden",
			structure: layout,
			plugins: {
				menus: {
					cellMenu: this._cellMenu
				}
			},
			headerMenu: headerMenu,
			loadingMessage: "<span class='dojoxGridLoading'>" + this._i18n.global._share.loadingAction  + "</span>",
			errorMessage: "<span class='dojoxGridError'>" + this._i18n.task.list.error + "</span>",
			noDataMessage: "<span class='dojoxGridNoData'>" + this._i18n.task.list.notFound + "</span>"
		}, dojo.create('div'));
		dojo.byId(this._id).appendChild(this._taskGrid.domNode);
		
		dojo.connect(this._taskGrid, "onRowContextMenu", function(e) {
			var item = this.getItem(e.rowIndex);
			if (item) {
				_this.onRowContextMenu(item);
			}
		});
	},
	
	showTasks: function(/*Object*/ tasks) {
		// summary:
		//		Shows the list of cron tasks
		var store = new dojo.data.ItemFileReadStore({
			data: tasks
		});
		dojo.style(this._taskGrid.domNode, {
			visibility: "visible"
		});
		this._taskGrid.setStore(store);
	},
	
	getContextMenu: function() {
		// summary:
		//		Gets the context menu of grid
		return this._cellMenu;	// dijit.Menu
	},
	
	////////// UPDATE STATE OF CONTROLS //////////	
	
	allowToConfig: function(/*Boolean*/ isAllowed) {
		// summary:
		//		Allows/disallows to configure a task
		isAllowed = isAllowed && core.js.base.controllers.ActionProvider.get("core_task_config").isAllowed;
		this._configMenuItem.set("disabled", !isAllowed);
		return this;	// core.js.views.TaskGrid
	},
	
	allowToInstall: function(/*Boolean*/ isAllowed) {
		// summary:
		//		Allows/disallows to install a task
		var _this = this;
		isAllowed = isAllowed && core.js.base.controllers.ActionProvider.get("core_task_install").isAllowed;
		this._installMenuItem.set("label", this._i18n.global._share.installAction);
		this._installMenuItem.set("disabled", !isAllowed);
		this._installMenuItem.onClick = function(e) {
			var rowIndex = _this._taskGrid.selection.selectedIndex;
			var item = _this._taskGrid.getItem(rowIndex);
			if (item) {
				_this.onInstallTask(item);
			}
		};
		return this;	// core.js.views.TaskGrid
	},
	
	allowToRun: function(/*Boolean*/ isAllowed) {
		// summary:
		//		Allows/disallows to run a task
		isAllowed = isAllowed && core.js.base.controllers.ActionProvider.get("core_task_run").isAllowed;
		this._runMenuItem.set("disabled", !isAllowed);
		return this;	// core.js.views.TaskGrid
	},
	
	allowToSchedule: function(/*Boolean*/ isAllowed) {
		// summary:
		//		Allows/disallows to schedule a task
		isAllowed = isAllowed && core.js.base.controllers.ActionProvider.get("core_task_schedule").isAllowed;
		this._scheduleMenuItem.set("disabled", !isAllowed);
		return this;	// core.js.views.TaskGrid
	},
	
	allowToUninstall: function(/*Boolean*/ isAllowed) {
		// summary:
		//		Allows/disallows to uninstall a task
		var _this = this;
		isAllowed = isAllowed && core.js.base.controllers.ActionProvider.get("core_task_uninstall").isAllowed;
		this._installMenuItem.set("label", this._i18n.global._share.uninstallAction);
		this._installMenuItem.set("disabled", !isAllowed);
		this._installMenuItem.onClick = function(e) {
			var rowIndex = _this._taskGrid.selection.selectedIndex;
			var item = _this._taskGrid.getItem(rowIndex);
			if (item) {
				_this.onUninstallTask(item);
			}
		};
		return this;	// core.js.views.TaskGrid
	},
	
	////////// CALLBACKS //////////
	
	onConfigTask: function(/*dojo.data.Item*/ item) {
		// summary:
		//		Configures given task item
		// tags:
		//		callback
	},
	
	onInstallTask: function(/*dojo.data.Item*/ item) {
		// summary:
		//		Installs given task item
		// tags:
		//		callback
	},
	
	onRowContextMenu: function(/*dojo.data.Item*/ item) {
		// summary:
		//		Called when right-click on the task item
		// tags:
		//		callback
	},
	
	onRunTask: function(/*dojo.data.Item*/ item) {
		// summary:
		//		Runs given task item
		// tags:
		//		callback
	},
	
	onScheduleTask: function(/*dojo.data.Item*/ item) {
		// summary:
		//		Schedules given task item
		// tags:
		//		callback
	},
	
	onUninstallTask: function(/*dojo.data.Item*/ item) {
		// summary:
		//		Uninstalls given task item
		// tags:
		//		callback
	}
});
