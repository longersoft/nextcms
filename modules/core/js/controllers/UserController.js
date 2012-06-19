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
 * @version		2012-05-25
 */

dojo.provide("core.js.controllers.UserController");

dojo.require("dijit.form.TextBox");
dojo.require("dijit.InlineEditBox");
dojo.require("dojox.string.sprintf");

dojo.require("core.js.base.controllers.ActionProvider");
dojo.require("core.js.base.controllers.Subscriber");
dojo.require("core.js.base.Encoder");
dojo.require("core.js.base.I18N");
dojo.require("core.js.base.views.Helper");
dojo.require("core.js.controllers.UserMediator");

dojo.declare("core.js.controllers.UserController", null, {
	// _id: String
	_id: null,
	
	// _i18n: Object
	_i18n: null,
	
	// _helper: core.js.base.views.Helper
	_helper: null,
	
	// _userMediator: core.js.controllers.UserMediator
	_userMediator: new core.js.controllers.UserMediator(),
	
	// TOPIC_GROUP: [const] String
	TOPIC_GROUP: "/core/js/controllers/UserController",
	
	constructor: function(/*String*/ id) {
		this._id = id;
		
		core.js.base.I18N.requireLocalization("core/languages");
		this._i18n = core.js.base.I18N.getLocalization("core/languages");
		
		// Create helper instance
		this._helper = new core.js.base.views.Helper(id);
		this._helper.setLanguageData(this._i18n);
		
		core.js.base.controllers.Subscriber.unsubscribe(this.TOPIC_GROUP);
	},
	
	////////// MANAGE ROLES //////////
	
	// _roleToolbar: core.js.views.RoleToolbar
	_roleToolbar: null,
	
	// _roleListView: core.js.views.RoleListView
	_roleListView: null,
	
	// _roleContextMenu: core.js.views.RoleContextMenu
	//		The context menu for each role item
	_roleContextMenu: null,
	
	// _roleSearchCriteria: Object
	_roleSearchCriteria: {
		page: 1,
		name: null,
		active_role_id: null
	},
	
	setRoleToolbar: function(/*core.js.views.RoleToolbar*/ roleToolbar) {
		// summary:
		//		Sets the role toolbar
		this._roleToolbar = roleToolbar;
		
		// Add role handler
		dojo.connect(roleToolbar, "onAddRole", this, "addRole");
		core.js.base.controllers.Subscriber.subscribe(this.TOPIC_GROUP, "/app/core/role/add/onCancel", this, function() {
			this._helper.closeDialog();
		});
		core.js.base.controllers.Subscriber.subscribe(this.TOPIC_GROUP, "/app/core/role/add/onComplete", this, function(data) {
			dojo.publish("/app/global/notification", [{
				message: this._i18n.role.add[(data.result == "APP_RESULT_OK") ? "success" : "error"],
				type: (data.result == "APP_RESULT_OK") ? "message" : "error"
			}]);
			
			if (data.result == "APP_RESULT_OK") {
				this.searchRoles();
			}
		});
		
		// Refresh handler
		dojo.connect(roleToolbar, "onRefresh", this, "searchRoles");
		
		// Search handler
		dojo.connect(roleToolbar, "onSearchRole", this, function(name) {
			this.searchRoles({
				name: name
			});
		});
		
		return this;	// core.js.controllers.UserController
	},
	
	setRoleListView: function(/*core.js.views.RoleListView*/ roleListView) {
		// summary:
		//		Sets the roles list view
		this._roleListView = roleListView;
		
		// Show context menu
		dojo.connect(roleListView, "onMouseDown", this, function(roleItemView) {
			if (this._roleContextMenu) {
				this._roleContextMenu.show(roleItemView);
			}
		});
		
		// Click role item handler
		dojo.connect(roleListView, "onClickRole", this, function(roleItemView) {
			// Add CSS class to identify the selected item in the role list view
			dojo.query(".coreRoleItemSelected", this._roleListView.getDomNode()).removeClass("coreRoleItemSelected");
			dojo.query(roleItemView.getDomNode()).addClass("coreRoleItemSelected");
			
			var roleId = roleItemView.getRole().role_id;
			this.searchUsers({
				role_id: roleId,
				page: 1,
				keyword: null
			})
		});
		
		// Paging handler
		core.js.base.controllers.Subscriber.subscribe(this.TOPIC_GROUP, "/app/core/role/list/onGotoPage", this, function(page) {
			this.searchRoles({
				page: page
			});
		});
		
		// View all handler
		core.js.base.controllers.Subscriber.subscribe(this.TOPIC_GROUP, "/app/core/role/list/onViewAll", this, function(node) {
			dojo.query(".coreRoleItemSelected", roleListView.getDomNode()).removeClass("coreRoleItemSelected");
			dojo.addClass(node, "coreRoleItemSelected");
			this.searchUsers({
				role_id: null
			});
		});
		
		// Update users counter after creating new user
		core.js.base.controllers.Subscriber.subscribe(this.TOPIC_GROUP, "/app/core/user/add/onSuccess", this, function(data) {
			roleListView.increaseUserCounter(data.role_id, 1);
		});
		// after changing user's role
		core.js.base.controllers.Subscriber.subscribe(this.TOPIC_GROUP, "/app/core/user/edit/onSuccess", this, function(data) {
			if (data.new_role_id != data.old_role_id) {
				roleListView.increaseUserCounter(data.old_role_id, -1);
				roleListView.increaseUserCounter(data.new_role_id, 1);
			}
		});
		// after removing user
		core.js.base.controllers.Subscriber.subscribe(this.TOPIC_GROUP, "/app/core/user/delete/onSuccess", this, function(data) {
			roleListView.increaseUserCounter(data.role_id, -1);
		});
		// after moving user
		core.js.base.controllers.Subscriber.subscribe(this.TOPIC_GROUP, "/app/core/user/move/onSuccess", this, function(data) {
			if (data.new_role_id != data.old_role_id) {
				roleListView.increaseUserCounter(data.old_role_id, -1);
				roleListView.increaseUserCounter(data.new_role_id, 1);
			}
		});
		
		// Dnd handler
		dojo.connect(roleListView, "onDropUsers", this, "moveUsers");
		
		return this;	// core.js.controllers.UserController
	},
	
	setRoleContextMenu: function(/*core.js.views.RoleContextMenu*/ roleContextMenu) {
		// summary:
		//		Sets the role's context menu
		this._roleContextMenu = roleContextMenu;
		this._userMediator.setRoleContextMenu(roleContextMenu);
		
		// Delete handler
		dojo.connect(roleContextMenu, "onDeleteRole", this, "deleteRole");
		core.js.base.controllers.Subscriber.subscribe(this.TOPIC_GROUP, "/app/core/role/delete/onCancel", this, function() {
			this._helper.closeDialog();
		});
		core.js.base.controllers.Subscriber.subscribe(this.TOPIC_GROUP, "/app/core/role/delete/onComplete", this, function(data) {
			this._helper.closeDialog();
			dojo.publish("/app/global/notification", [{
				message: this._i18n.role["delete"][(data.result == "APP_RESULT_OK") ? "success" : "error"],
				type: (data.result == "APP_RESULT_OK") ? "message" : "error"
			}]);
			
			this.searchRoles();
		});
		
		// Rename handler
		dojo.connect(roleContextMenu, "onRenameRole", this, "renameRole");
		
		// Lock handler
		dojo.connect(roleContextMenu, "onLockRole", this, "lockRole");
		
		// Set permissions handler
		dojo.connect(roleContextMenu, "onSetRolePermissions", this, "setRolePermissions");
		core.js.base.controllers.Subscriber.subscribe(this.TOPIC_GROUP, "/app/core/rule/role/onCancel", this, function() {
			this._helper.removePane();
		});
		core.js.base.controllers.Subscriber.subscribe(this.TOPIC_GROUP, "/app/core/rule/role/onComplete", this, function(data) {
			this._helper.removePane();
			dojo.publish("/app/global/notification", [{
				message: this._i18n.rule.role[(data.result == "APP_RESULT_OK") ? "success" : "error"],
				type: (data.result == "APP_RESULT_OK") ? "message" : "error"
			}]);
		});
		
		return this;	// core.js.controllers.UserController
	},
	
	addRole: function() {
		// summary:
		//		Shows a dialog for adding new role
		var url = core.js.base.controllers.ActionProvider.get("core_role_add").url;
		this._helper.showDialog(url, {
			title: this._i18n.role.add.title,
			style: "width: 250px",
			refreshOnShow: true
		});
	},
	
	deleteRole: function(/*core.js.views.RoleItemView*/ roleItemView) {
		// summary:
		//		Deletes a role
		// roleItemView:
		//		The selected role item
		var params = {
			role_id: roleItemView.getRole().role_id
		};
		var url = core.js.base.controllers.ActionProvider.get("core_role_delete").url + "?" + dojo.objectToQuery(params);
		this._helper.showDialog(url, {
			title: this._i18n.role["delete"].title,
			style: "width: 250px",
			refreshOnShow: true
		});
	},
	
	lockRole: function(/*core.js.views.RoleItemView*/ roleItemView) {
		// summary:
		//		Locks role
		// roleItemView:
		//		The selected role item
		var roleId = roleItemView.getRole().role_id;
		var locked = roleItemView.getRole().locked;
		var _this  = this;
		dojo.xhrPost({
			url: core.js.base.controllers.ActionProvider.get("core_role_lock").url,
			content: {
				role_id: roleId
			},
			handleAs: "json",
			load: function(data) {
				var message = (data.result == "APP_RESULT_OK") ? (locked ? "unlockSuccess" : "lockSuccess") : (locked ? "unlockError" : "lockError");
				dojo.publish("/app/global/notification", [{
					message: _this._i18n.role.lock[message],
					type: (data.result == "APP_RESULT_OK") ? "message" : "error"
				}]);
				
				if (data.result == "APP_RESULT_OK") {
					roleItemView.getRole().locked = !locked;
				}
			}
		});
	},
	
	renameRole: function(/*core.js.views.RoleItemView*/ roleItemView) {
		// summary:
		//		Renames a role
		// roleItemView:
		//		The selected role item
		var _this  = this;
		var roleId = roleItemView.getRole().role_id;

		// Create InlineEditBox element
		if (!roleItemView.nameEditBox) {
			roleItemView.nameEditBox = new dijit.InlineEditBox({
				editor: "dijit.form.TextBox", 
				autoSave: true, 
				disabled: false, 
				editorParams: {
					required: true
				},
				onChange: function(newName) {
					this.set("disabled", true);
	
					// I can get the roleNode as follow:
					//		var roleNode = editBox.displayNode.parentNode;
					if (newName != "") {
						dojo.xhrPost({
							url: core.js.base.controllers.ActionProvider.get("core_role_rename").url,
							content: {
								role_id: roleId,
								name: newName
							},
							handleAs: "json",
							load: function(data) {
								dojo.publish("/app/global/notification", [{
									message: _this._i18n.role.rename[(data.result == "APP_RESULT_OK") ? "success" : "error"],
									type: (data.result == "APP_RESULT_OK") ? "message" : "error"
								}]);
							}
						});
					}
				}, 
				onCancel: function() {
					this.set("disabled", true);
				}
			}, roleItemView.getRoleNameNode());
		}
		roleItemView.nameEditBox.set("disabled", false);
		roleItemView.nameEditBox.startup();
		roleItemView.nameEditBox.edit();
	},
	
	searchRoles: function(/*Object*/ criteria) {
		// summary:
		//		Searches for roles
		this._helper.closeDialog();
		
		var _this = this;
		dojo.mixin(this._roleSearchCriteria, criteria);
		var q = core.js.base.Encoder.encode(this._roleSearchCriteria);
		
		this._helper.showStandby();
		dojo.xhrPost({
			url: core.js.base.controllers.ActionProvider.get("core_role_list").url,
			content: {
				q: q
			},
			load: function(data) {
				_this._helper.hideStandby();
				_this._roleListView.setContent(data);
			}
		});
	},
	
	setRolePermissions: function(/*core.js.views.RoleItemView*/ roleItemView) {
		// summary:
		//		Manages role's permissions
		// roleItemView:
		//		The selected role item
		var params = {
			role_id: roleItemView.getRole().role_id
		};
		var url = core.js.base.controllers.ActionProvider.get("core_rule_role").url + "?" + dojo.objectToQuery(params);
		this._helper.showPane(url);
	},

	////////// MANAGE USERS //////////

	// _userToolbar: core.js.views.UserToolbar
	_userToolbar: null,
	
	// _userListView: core.js.views.UserListView
	_userListView: null,
	
	// _userContextMenu: core.js.views.UserContextMenu
	//		The context menu for each selected user
	_userContextMenu: null,
	
	// _searchCriteria: Object
	//	The search conditions including the following keys:
	//		- role_id: If of role that user belong to
	//		- keyword
	//		- page: Current index of page
	//		- per_page: The number of users per page
	//		- status
	_userSearchCriteria: {
		role_id: null,
		keyword: null,
		page: 1,
		per_page: 20,
		status: null
	},
	
	setUserListView: function(/*core.js.views.UserListView*/ userListView) {
		// summary:
		//		Sets the users list view
		this._userListView = userListView;
		
		// Show the context menu when right-clicking on each user item
		dojo.connect(userListView, "onMouseDown", this, function(userItemView) {
			if (this._userContextMenu) {
				this._userContextMenu.show(userItemView);
			}
		});
		
		// Update avatar handler
		dojo.connect(userListView, "onDropAvatar", this, "updateAvatar");
		
		core.js.base.controllers.Subscriber.subscribe(this.TOPIC_GROUP, "/app/core/user/list/onGotoPage", this, function(page) {
			this.searchUsers({
				page: page
			});
		});
		
		// Refresh list of users after creating new user
		core.js.base.controllers.Subscriber.subscribe(this.TOPIC_GROUP, "/app/core/user/add/onSuccess", this, function(data) {
			this.searchUsers();
		});
		core.js.base.controllers.Subscriber.subscribe(this.TOPIC_GROUP, "/app/core/user/delete/onSuccess", this, function(data) {
			// FIXME: Remove the user item without reloading
			this.searchUsers();
		});
		
		return this;	// core.js.controllers.UserController
	},
	
	setUserToolbar: function(/*core.js.views.UserToolbar*/ userToolbar) {
		// summary:
		//		Sets the user toolbar
		this._userToolbar = userToolbar;
		
		// Add user handler
		dojo.connect(userToolbar, "onAddUser", this, "addUser");
		core.js.base.controllers.Subscriber.subscribe(this.TOPIC_GROUP, "/app/core/user/add/onCancel", this, function() {
			this._helper.removePane();
		});
		core.js.base.controllers.Subscriber.subscribe(this.TOPIC_GROUP, "/app/core/user/add/onComplete", this, function(data) {
			this._helper.removePane();
			dojo.publish("/app/global/notification", [{
				message: this._i18n.user.add[(data.result == "APP_RESULT_OK") ? "success" : "error"],
				type: (data.result == "APP_RESULT_OK") ? "message" : "error"
			}]);
			
			if (data.result == "APP_RESULT_OK") {
				dojo.publish("/app/core/user/add/onSuccess", [ data ]);
			}
		});
		
		// Refresh handler
		dojo.connect(userToolbar, "onRefresh", this, "searchUsers");
		
		// Update page size handler
		dojo.connect(userToolbar, "onUpdatePageSize", this, function(perPage) {
			this.searchUsers({
				page: 1,
				per_page: perPage
			});
		});
		
		// Search handler
		dojo.connect(userToolbar, "onSearchUser", this, function(keyword) {
			this.searchUsers({
				page: 1,
				keyword: keyword
			});
		});
		
		return this;	// core.js.controllers.UserController
	},
	
	setUserContextMenu: function(/*core.js.views.UserContextMenu*/ userContextMenu) {
		// summary:
		//		Sets the user's context menu
		
		// Listen on context menu events
		// DOJO LESSON: Because Dojo does not separate DOM and object's events.
		// Using the dojo.connect, all the events of the view can be handled in a flexible way. 
		// The views do NOT need to care how their events can be handled and who are handlers.
		// Compare this with the old approaching:
		// - Attach a controller instance in view object:
		//		dojo.declare("core.js.views.UserContextMenu", null, {
		//			_controller: null,
		//			constructor: function(/*core.js.controllers.UserController*/ controller) {
		//				this._controller = controller;
		//			),
		//			...
		// - And use the controller instance to handle the events:
		//			...
		//			show: function(/*core.js.views.UserItemView*/ userItemView) {
		//				var _this = this;
		//				this._contextMenu.addChild(new dijit.MenuItem({
		//					onClick: function() {
		//						_this._controller.activateUser(userItemView);
		//					}
		//				});
		//			}
		//		});
		this._userContextMenu = userContextMenu;
		
		// It is possible to control menu items after showing the menu item
		this._userMediator.setUserContextMenu(userContextMenu);
		
		// Activate handler
		dojo.connect(userContextMenu, "onActivateUser", this, "activateUser");
		
		// Edit handler
		dojo.connect(userContextMenu, "onEditUser", this, "editUser");
		core.js.base.controllers.Subscriber.subscribe(this.TOPIC_GROUP, "/app/core/user/edit/onCancel", this, function() {
			this._helper.removePane();
		});
		core.js.base.controllers.Subscriber.subscribe(this.TOPIC_GROUP, "/app/core/user/edit/onComplete", this, function(data) {
			this._helper.removePane();
			dojo.publish("/app/global/notification", [{
				message: this._i18n.user.edit[(data.result == "APP_RESULT_OK") ? "success" : "error"],
				type: (data.result == "APP_RESULT_OK") ? "message" : "error"
			}]);
			
			if (data.result == "APP_RESULT_OK") {
				dojo.publish("/app/core/user/edit/onSuccess", [ data ]);
				this.searchUsers();
			}
		});
		
		// Delete handler
		dojo.connect(userContextMenu, "onDeleteUser", this, "deleteUser");
		core.js.base.controllers.Subscriber.subscribe(this.TOPIC_GROUP, "/app/core/user/delete/onCancel", this, function() {
			this._helper.closeDialog();
		});
		core.js.base.controllers.Subscriber.subscribe(this.TOPIC_GROUP, "/app/core/user/delete/onComplete", this, function(data) {
			this._helper.closeDialog();
			dojo.publish("/app/global/notification", [{
				message: this._i18n.user["delete"][(data.result == "APP_RESULT_OK") ? "success" : "error"],
				type: (data.result == "APP_RESULT_OK") ? "message" : "error"
			}]);
			
			if (data.result == "APP_RESULT_OK") {
				dojo.publish("/app/core/user/delete/onSuccess", [ data ]);
			}
		});
		
		// Set permissions handler
		dojo.connect(userContextMenu, "onSetUserPermissions", this, "setUsersPermissions");
		core.js.base.controllers.Subscriber.subscribe(this.TOPIC_GROUP, "/app/core/rule/user/onCancel", this, function() {
			this._helper.removePane();
		});
		core.js.base.controllers.Subscriber.subscribe(this.TOPIC_GROUP, "/app/core/rule/user/onComplete", this, function(data) {
			this._helper.removePane();
			dojo.publish("/app/global/notification", [{
				message: this._i18n.rule.user[(data.result == "APP_RESULT_OK") ? "success" : "error"],
				type: (data.result == "APP_RESULT_OK") ? "message" : "error"
			}]);
		});
		
		return this;	// core.js.controllers.UserController
	},
	
	initSearchCriteria: function(/*Object*/ criteria) {
		// summary:
		//		Inits the controls based on given criteria
		dojo.mixin(this._userSearchCriteria, criteria);
		this._userToolbar.initSearchCriteria(this._userSearchCriteria);
	},
	
	activateUser: function(/*core.js.views.UserItemView*/ userItemView) {
		// summary:
		//		Activates or deactivates user
		// userItemView:
		//		The selected user item
		var userId = userItemView.getUser().user_id;
		var status = userItemView.getUser().status;
		
		var _this  = this;
		dojo.xhrPost({
			url: core.js.base.controllers.ActionProvider.get("core_user_activate").url,
			content: {
				user_id: userId
			},
			handleAs: "json",
			load: function(data) {
				var message = (data.result == "APP_RESULT_OK") 
							  ? (status == "activated" ? "deactivateSuccess" : "activateSuccess")
							  : (status == "activated" ? "deactivateError" : "activateError");
				dojo.publish("/app/global/notification", [{
					message: _this._i18n.user.activate[message],
					type: (data.result == "APP_RESULT_OK") ? "message" : "error"
				}]);
				
				if (data.result == "APP_RESULT_OK") {
					var newStatus = (status == "activated") ? "not_activated" : "activated";
					userItemView.getUser().status = newStatus;
					
					if (_this._userSearchCriteria.status) {
						_this._userListView.removeUserItemView(userItemView);
					}
					
					dojo.publish("/app/core/user/activate/onSuccess", [{ oldStatus: status, newStatus: newStatus }]);
				}
			}
		});
	},
	
	addUser: function() {
		// summary:
		//		Adds new user
		var url = core.js.base.controllers.ActionProvider.get("core_user_add").url;
		this._helper.showPane(url);
	},
	
	deleteUser: function(/*core.js.views.UserItemView*/ userItemView) {
		// summary:
		//		Deletes given user
		// userItemView:
		//		The selected user item
		var params = {
			user_id: userItemView.getUser().user_id
		};
		var url = core.js.base.controllers.ActionProvider.get("core_user_delete").url + "?" + dojo.objectToQuery(params);
		this._helper.showDialog(url, {
			title: this._i18n.user["delete"].title,
			style: "width: 250px",
			refreshOnShow: true
		});
	},
	
	editUser: function(/*core.js.views.UserItemView*/ userItemView) {
		// summary:
		//		Edits user's information
		// userItemView:
		//		The selected user item
		var params = {
			user_id: userItemView.getUser().user_id
		};
		var url = core.js.base.controllers.ActionProvider.get("core_user_edit").url + "?" + dojo.objectToQuery(params);
		this._helper.showPane(url);
	},
	
	searchUsers: function(/*Object*/ criteria) {
		// summary:
		//		Searches for users
		var _this = this;
		
		// DOJO LESSON: How I love dojo.mixin
		dojo.mixin(this._userSearchCriteria, criteria);
		
		var q   = core.js.base.Encoder.encode(this._userSearchCriteria);
		var url = core.js.base.controllers.ActionProvider.get("core_user_list").url;
		dojo.hash("u=" + url + "/?q=" + q);
		
		this._helper.showStandby();
		dojo.xhrPost({
			url: url,
			content: {
				q: q,
				format: "html"
			},
			load: function(data) {
				_this._helper.hideStandby();
				_this._userListView.setContent(data);
			}
		});
	},
	
	setUsersPermissions: function(/*core.js.views.UserItemView*/ userItemView) {
		// summary:
		//		Manages user's permissions
		// userItemView:
		//		The selected user item
		var params = {
			user_id: userItemView.getUser().user_id
		};
		var url = core.js.base.controllers.ActionProvider.get("core_rule_user").url + "?" + dojo.objectToQuery(params);
		this._helper.showPane(url);
	},
	
	updateAvatar: function(/*core.js.views.UserItemView*/ userItemView, /*String*/ avatarUrl) {
		// summary:
		//		Updates user's avatar
		var _this = this;
		dojo.xhrPost({
			url: core.js.base.controllers.ActionProvider.get("core_user_avatar").url,
			content: {
				user_id: userItemView.getUser().user_id,
				url: avatarUrl
			},
			handleAs: "json",
			load: function(data) {
				dojo.publish("/app/global/notification", [{
					message: _this._i18n.user.avatar[(data.result == "APP_RESULT_OK") ? "success" : "error"],
					type: (data.result == "APP_RESULT_OK") ? "message" : "error"
				}]);
				if (data.result == "APP_RESULT_OK") {
					userItemView.updateAvatar(avatarUrl);
				}
			}
		});
	},
	
	////////// STATUS FILTER //////////
	
	// _statusListView: core.js.views.StatusListView
	_statusListView: null,
	
	setStatusListView: function(/*core.js.views.StatusListView*/ statusListView) {
		// summary:
		//		Sets the status list view
		this._statusListView = statusListView;
		
		// Filter users by status
		dojo.connect(statusListView, "onStatusSelected", this, function(status) {
			this.searchUsers({
				status: status
			});
		});
		
		// Update users counter after creating new user
		core.js.base.controllers.Subscriber.subscribe(this.TOPIC_GROUP, "/app/core/user/add/onSuccess", this, function(data) {
			statusListView.increaseUserCounter(data.status, 1);
		});
		// after updating user's status
		core.js.base.controllers.Subscriber.subscribe(this.TOPIC_GROUP, "/app/core/user/activate/onSuccess", this, function(data) {
			statusListView.increaseUserCounter(data.oldStatus, -1);
			statusListView.increaseUserCounter(data.newStatus, 1);
		});
		// after removing user
		core.js.base.controllers.Subscriber.subscribe(this.TOPIC_GROUP, "/app/core/user/delete/onSuccess", this, function(data) {
			statusListView.increaseUserCounter(data.status, -1);
		});
		
		dojo.connect(statusListView, "onUpdateStatus", this, "updateStatus");
		
		return this;	// core.js.controllers.UserController
	},
	
	updateStatus: function(/*String*/ status, /*DomNode[]*/ userNodes) {
		// summary:
		//		Called when moving user items to a status item
		if (this._userSearchCriteria.status == status) {
			return;
		}
		
		this._helper.showStandby();
		while (userNodes.length > 0) {
			var user = core.js.base.Encoder.decode(dojo.attr(userNodes[0], "data-app-entity-props"));
			var userItemView = this._userListView.getUserItemView(user.user_id);
			if (userItemView && user.status) {
				if (userItemView.getUser().status != status) {
					this.activateUser(userItemView);
				}
			}
			userNodes.splice(0, 1);
		}
		
		if (userNodes.length == 0) {
			this._helper.hideStandby();
		}
	},
	
	////////// HANDLE TOPICS //////////
	
	moveUsers: function(/*core.js.views.RoleItemView*/ roleItemView, /*DomNode[]*/ userNodes) {
		// summary:
		//		Use when moving an user to another group
		var role		= roleItemView.getRole();
		
		// Get name of target role
		// FIXME: Move this method to core.js.views.RoleItemView class to get the role description
		var newRoleName = dojo.query(".coreRoleLabel", roleItemView.getDomNode())[0].innerHTML;
		
		var _this = this;
		var url = core.js.base.controllers.ActionProvider.get("core_user_move").url;
		
		this._helper.showStandby();
		while (userNodes.length > 0) {
			var userNode = userNodes[0];
			var user	 = core.js.base.Encoder.decode(dojo.attr(userNode, "data-app-entity-props"));
			
			if (user.role_id != role.role_id) {
				dojo.xhrPost({
					url: url,
					content: {
						user_id: user.user_id,
						role_id: role.role_id
					},
					handleAs: "json",
					load: function(data) {
						if (data.result == "APP_RESULT_OK") {
							dojo.publish("/app/global/notification", [{
								message: dojox.string.sprintf(_this._i18n.user.move.success, user.user_name, newRoleName),
								type: "message"
							}]);
							
							// Remove user item from the list
							if (_this._userSearchCriteria.role_id && _this._userSearchCriteria.role_id != data.new_role_id) {
								_this._userListView.removeUserItemView(data.user_id);
							}
							
							dojo.publish("/app/core/user/move/onSuccess", [ data ]);
						}
					}
				});
			}
			
			userNodes.splice(0, 1);
		}
		
		if (userNodes.length == 0) {
			this._helper.hideStandby();
		}
	}
});
