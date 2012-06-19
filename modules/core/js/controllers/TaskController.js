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

dojo.provide("core.js.controllers.TaskController");

dojo.require("dijit.Menu");
dojo.require("dijit.MenuItem");
dojo.require("dijit.MenuSeparator");
dojo.require("dojox.string.sprintf");

dojo.require("core.js.base.controllers.ActionProvider");
dojo.require("core.js.base.controllers.Subscriber");
dojo.require("core.js.base.Encoder");
dojo.require("core.js.base.I18N");
dojo.require("core.js.base.views.Helper");
dojo.require("core.js.Constant");
dojo.require("core.js.controllers.TaskMediator");

dojo.declare("core.js.controllers.TaskController", null, {
	// _id: String
	_id: null,
	
	// _i18n: Object
	_i18n: null,
	
	// _helper: core.js.base.views.Helper
	_helper: null,
	
	// _taskToolbar: core.js.views.TaskToolbar
	_taskToolbar: null,
	
	// _taskGrid: core.js.views.TaskGrid
	_taskGrid: null,
	
	// _defaultCriteria: Object
	_defaultCriteria: {
		module: null
	},
	
	// _mediator: core.js.controllers.TaskMediator
	_mediator: new core.js.controllers.TaskMediator(),
	
	// _actionItems: Array
	//		Array of menu items that perform additional actions for a given task
	_actionItems: [],
	
	// TOPIC_GROUP: [const] String
	TOPIC_GROUP: "/core/js/controllers/TaskController",
	
	constructor: function(/*String*/ id) {
		this._id = id;
		
		core.js.base.I18N.requireLocalization("core/languages");
		this._i18n = core.js.base.I18N.getLocalization("core/languages");
		
		// Create helper instance
		this._helper = new core.js.base.views.Helper(id);
		this._helper.setLanguageData(this._i18n);
		
		core.js.base.controllers.Subscriber.unsubscribe(this.TOPIC_GROUP);
	},
	
	setTaskToolbar: function(/*core.js.views.TaskToolbar*/ toolbar) {
		// summary:
		//		Sets the task toolbar
		this._taskToolbar = toolbar;
		
		// Refresh handler
		dojo.connect(toolbar, "onRefresh", this, "searchTasks");
		
		// Module filter handler
		dojo.connect(toolbar, "onSelectModule", this, function(module) {
			this.searchTasks({
				module: module
			});
		});
		
		return this;	// core.js.controllers.TaskController
	},
	
	setTaskGrid: function(/*core.js.views.TaskGrid*/ grid) {
		// summary:
		//		Sets the task grid
		this._taskGrid = grid;
		this._mediator.setTaskGrid(grid);
		
		// Install task handler
		dojo.connect(grid, "onInstallTask", this, "installTask");
		
		// Uninstall task handler
		dojo.connect(grid, "onUninstallTask", this, "uninstallTask");
		
		// Configure task handler
		dojo.connect(grid, "onConfigTask", this, "configTask");
		core.js.base.controllers.Subscriber.subscribe(this.TOPIC_GROUP, "/app/core/task/config/onCancel", this, function() {
			this._helper.removePane();
		});
		core.js.base.controllers.Subscriber.subscribe(this.TOPIC_GROUP, "/app/core/task/config/onStart", this, function() {
			this._helper.showStandby();
		});
		core.js.base.controllers.Subscriber.subscribe(this.TOPIC_GROUP, "/app/core/task/config/onComplete", this, function(data) {
			this._helper.hideStandby();
			dojo.publish("/app/global/notification", [{
				message: dojox.string.sprintf(this._i18n.task.config[(data.result == "APP_RESULT_OK") ? "success" : "error"], data.name),
				type: (data.result == "APP_RESULT_OK") ? "message" : "error"
			}]);
		});
		
		// Run task handler
		dojo.connect(grid, "onRunTask", this, "runTask");
		
		// Schedule task handler
		dojo.connect(grid, "onScheduleTask", this, "scheduleTask");
		core.js.base.controllers.Subscriber.subscribe(this.TOPIC_GROUP, "/app/core/task/schedule/onCancel", this, function() {
			this._helper.removePane();
		});
		core.js.base.controllers.Subscriber.subscribe(this.TOPIC_GROUP, "/app/core/task/schedule/onStart", this, function() {
			this._helper.showStandby();
		});
		core.js.base.controllers.Subscriber.subscribe(this.TOPIC_GROUP, "/app/core/task/schedule/onComplete", this, function(data) {
			this._helper.hideStandby();
			dojo.publish("/app/global/notification", [{
				message: this._i18n.task.schedule[(data.result == "APP_RESULT_OK") ? "success" : "error"],
				type: (data.result == "APP_RESULT_OK") ? "message" : "error"
			}]);
		});
		
		core.js.base.controllers.Subscriber.subscribe(this.TOPIC_GROUP, "/app/core/task/config/onCancelAction", this, function() {
			this._helper.removePane();
		});
		dojo.connect(grid, "onRowContextMenu", this, function(item) {
			var menu = grid.getContextMenu();
			for (var i in this._actionItems) {
				menu.removeChild(this._actionItems[i]);
			}
			this._actionItems = [];
			
			if (item.actions[0]) {
				var _this = this, actions = dojo.fromJson(item.actions[0]), languagePath, label, translationKeys;
				
				// Add a separator menu item
				var separator = new dijit.MenuSeparator();
				this._actionItems.push(separator);
				menu.addChild(separator);
				
				for (var action in actions) {
					dojo.registerModulePath(item.module[0], core.js.Constant.ROOT_URL + "/modules/" + item.module[0]);
					languagePath = [ item.module[0], "tasks", item.name[0] ].join("/");
					core.js.base.I18N.requireLocalization(languagePath);
					label = core.js.base.I18N.getLocalization(languagePath);
					translationKeys = actions[action].translationKey.split(".");
					for (var i in translationKeys) {
						label = label[translationKeys[i] + ""];
					}
					
					var menuItem = new dijit.MenuItem({
						label: label,
						disabled: !actions[action].allowed,
						onClick: function(e) {
							_this.performAction(item, action);
						}
					});
					this._actionItems.push(menuItem);
					menu.addChild(menuItem);
				}
			}
		});
		
		return this;	// core.js.controllers.TaskController
	},
	
	configTask: function(/*dojo.data.Item*/ item) {
		// summary:
		//		Configures the given task
		var params = {
			mod: item.module[0],
			name: item.name[0]
		};
		var url = core.js.base.controllers.ActionProvider.get("core_task_config").url + "?" + dojo.objectToQuery(params);
		this._helper.showPane(url, {
			style: "width: 50%"
		});
	},
	
	initSearchCriteria: function(/*Object*/ criteria) {
		// summary:
		//		Inits with given criteria
		dojo.mixin(this._defaultCriteria, criteria);
		return this;	// core.js.controllers.TaskController
	},
	
	installTask: function(/*dojo.data.Item*/ item) {
		// summary:
		//		Installs given task item
		var _this = this;
		dojo.xhrPost({
			url: core.js.base.controllers.ActionProvider.get("core_task_install").url,
			content: {
				mod: item.module[0],
				name: item.name[0]
			},
			handleAs: "json",
			load: function(data) {
				dojo.publish("/app/global/notification", [{
					message: dojox.string.sprintf(_this._i18n.task.install[(data.result == "APP_RESULT_OK") ? "success" : "error"], data.name),
					type: (data.result == "APP_RESULT_OK") ? "message" : "error"
				}]);
				if (data.result == "APP_RESULT_OK") {
					_this.searchTasks();
				}
			}
		});
	},
	
	runTask: function(/*dojo.data.Item*/ item) {
		// summary:
		//		Runs given task
		var _this = this;
		this._helper.showStandby();
		dojo.xhrPost({
			url: core.js.base.controllers.ActionProvider.get("core_task_run").url,
			content: {
				mod: item.module[0],
				name: item.name[0]
			},
			handleAs: "json",
			load: function(data) {
				_this._helper.hideStandby();
				dojo.publish("/app/global/notification", [{
					message: dojox.string.sprintf(_this._i18n.task.run[(data.result == "APP_RESULT_OK") ? "success" : "error"], data.name),
					type: (data.result == "APP_RESULT_OK") ? "message" : "error"
				}]);
				if (data.result == "APP_RESULT_OK") {
					_this.searchTasks();
				}
			}
		});
	},
	
	scheduleTask: function(/*dojo.data.Item*/ item) {
		// summary:
		//		Schedules given task
		var params = {
			mod: item.module[0],
			name: item.name[0]
		};
		var url = core.js.base.controllers.ActionProvider.get("core_task_schedule").url + "?" + dojo.objectToQuery(params);
		this._helper.showPane(url, {
			style: "width: 50%"
		});
	},
	
	searchTasks: function(/*Object*/ criteria) {
		// summary:
		//		Searches for tasks
		dojo.mixin(this._defaultCriteria, criteria);
		
		var q   = core.js.base.Encoder.encode(this._defaultCriteria);
		var url = core.js.base.controllers.ActionProvider.get("core_task_list").url;
		dojo.hash("u=" + url + "/?q=" + q);
		
		var _this = this;
		this._helper.showStandby();
		dojo.xhrPost({
			url: url,
			content: {
				q: q,
				format: "json"
			},
			handleAs: "json",
			load: function(data) {
				_this._helper.hideStandby();
				_this._taskGrid.showTasks(data.data);
			}
		});
		
		return this;	// core.js.controllers.TaskController
	},
	
	uninstallTask: function(/*dojo.data.Item*/ item) {
		// summary:
		//		Uninstalls given task item
		var _this = this;
		dojo.xhrPost({
			url: core.js.base.controllers.ActionProvider.get("core_task_uninstall").url,
			content: {
				mod: item.module[0],
				name: item.name[0]
			},
			handleAs: "json",
			load: function(data) {
				dojo.publish("/app/global/notification", [{
					message: dojox.string.sprintf(_this._i18n.task.uninstall[(data.result == "APP_RESULT_OK") ? "success" : "error"], data.name),
					type: (data.result == "APP_RESULT_OK") ? "message" : "error"
				}]);
				if (data.result == "APP_RESULT_OK") {
					_this.searchTasks();
				}
			}
		});
	},
	
	performAction: function(/*dojo.data.Item*/ item, /*String*/ action) {
		// summary:
		//		Performs an action to a given task
		var params = {
			_type: "task",
			_mod: item.module[0],
			_name: item.name[0],
			_method: action
		};
		var url = core.js.base.controllers.ActionProvider.get("core_extension_render").url + "?" + dojo.objectToQuery(params);
		this._helper.showPane(url, {
			style: "width: 50%"
		});
	}
});
