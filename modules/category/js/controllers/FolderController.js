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
 * @version		2012-05-16
 */

dojo.provide("category.js.controllers.FolderController");

dojo.require("dijit.form.TextBox");
dojo.require("dijit.InlineEditBox");

dojo.require("core.js.base.controllers.ActionProvider");
dojo.require("core.js.base.controllers.Subscriber");
dojo.require("core.js.base.Encoder");
dojo.require("core.js.base.I18N");
dojo.require("core.js.base.views.Helper");

dojo.declare("category.js.controllers.FolderController", null, {
	// _id: String
	_id: null,
	
	// _i18n: Object
	_i18n: null,
	
	// _helper: core.js.base.views.Helper
	_helper: null,
	
	// _folderToolbar: category.js.views.FolderToolbar
	_folderToolbar: null,
	
	// _folderListView: category.js.views.FolderListView
	_folderListView: null,
	
	// _folderContextMenu: category.js.views.FolderContextMenu
	_folderContextMenu: null,
	
	// _criteria: Object
	_criteria: {
		entity_class: null,
		language: null
	},
	
	// TOPIC_GROUP: [const] String
	TOPIC_GROUP: "/category/js/controllers/FolderController",
	
	constructor: function(/*String*/ id) {
		core.js.base.controllers.Subscriber.unsubscribe(this.TOPIC_GROUP);
		
		this._id = id;
		core.js.base.I18N.requireLocalization("category/languages");
		this._i18n = core.js.base.I18N.getLocalization("category/languages");
	},
	
	setEntityClass: function(/*String*/ clazz) {
		// summary:
		//		Sets the entity class
		this._criteria.entity_class = clazz;
		return this;	// category.js.controllers.FolderController
	},
	
	setLanguage: function(/*String*/ language) {
		// summary:
		//		Sets the language
		// language:
		//		The language in format of languagecode_COUNTRYCODE
		this._criteria.language = language;
		return this;	// category.js.controllers.FolderController
	},
	
	setHelperContainer: function(/*String*/ containerId) {
		// summary:
		//		Sets the Id of helper container
		this._helper = new core.js.base.views.Helper(containerId);
		this._helper.setLanguageData(this._i18n);
		return this;	// category.js.controllers.FolderController
	},
	
	setFolderToolbar: function(/*category.js.views.FolderToolbar*/ toolbar) {
		// summary:
		//		Sets the folder toolbar
		this._folderToolbar = toolbar;
		
		// Set the language
		this._folderToolbar.setLanguage(this._criteria.language);
		
		// Add new folder handler
		dojo.connect(toolbar, "onAddFolder", this, "addFolder");
		core.js.base.controllers.Subscriber.subscribe(this.TOPIC_GROUP, "/app/category/folder/add/onCancel", this, function() {
			this._helper.closeDialog();
		});
		core.js.base.controllers.Subscriber.subscribe(this.TOPIC_GROUP, "/app/category/folder/add/onComplete", this, function(data) {
			this._helper.closeDialog();
			dojo.publish("/app/global/notification", [{
				message: this._i18n.folder.add[data.result == "APP_RESULT_OK" ? "success" : "error"],
				type: (data.result == "APP_RESULT_OK") ? "message" : "error"
			}]);
			if (data.result == "APP_RESULT_OK") {
				this.search();
			}
		});
		
		// Refresh handler
		dojo.connect(toolbar, "onRefresh", this, "search");
		
		// Switch to other language handler
		dojo.connect(toolbar, "onSwitchToLanguage", this, function(language) {
			this.search({
				language: language
			});
			
			// Extension point
			dojo.publish("/app/category/controllers/FolderController/onSwitchToLanguage_" + this._criteria.entity_class, [ language ]);
		});
		
		return this;	// category.js.controllers.FolderController
	},
	
	setFolderListView: function(/*category.js.views.FolderListView*/ listView) {
		// summary:
		//		Sets the folder list view
		this._folderListView = listView;
		
		dojo.connect(listView, "onDropExternal", this, function(folderItemView, nodes) {
			// Extension point
			dojo.publish("/app/category/controllers/FolderController/onDropItems_" + this._criteria.entity_class, [{
				folderItemView: folderItemView,
				nodes: nodes
			}]);
		});
		
		dojo.connect(listView, "onClickFolder", this, function(folderItemView) {
			// Extension point
			dojo.publish("/app/category/controllers/FolderController/onClickFolder_" + this._criteria.entity_class, [ folderItemView ]);
		});
		
		dojo.connect(listView, "onMouseDown", this, function(folderItemView) {
			if (this._folderContextMenu) {
				this._folderContextMenu.show(folderItemView);
			}
		});
		
		return this;	// category.js.controllers.FolderController
	},
	
	setFolderContextMenu: function(/*category.js.views.FolderContextMenu*/ menu) {
		// summary:
		//		Sets the folder context menu
		this._folderContextMenu = menu;
		
		// Delete folder handler
		dojo.connect(menu, "onDeleteFolder", this, "deleteFolder");
		core.js.base.controllers.Subscriber.subscribe(this.TOPIC_GROUP, "/app/category/folder/delete/onComplete", this, function(data) {
			this._helper.closeDialog();
			dojo.publish("/app/global/notification", [{
				message: this._i18n.folder["delete"][(data.result == "APP_RESULT_OK") ? "success" : "error"],
				type: (data.result == "APP_RESULT_OK") ? "message" : "error"
			}]);
			if (data.result == "APP_RESULT_OK") {
				this.search();
			}
		});
		core.js.base.controllers.Subscriber.subscribe(this.TOPIC_GROUP, "/app/category/folder/delete/onCancel", this, function() {
			this._helper.closeDialog();
		});
		
		// Rename folder handler
		dojo.connect(menu, "onRenameFolder", this, "renameFolder");
		
		return this;	// category.js.controllers.FolderController
	},
	
	addFolder: function() {
		// summary:
		//		Adds new folder
		var url = core.js.base.controllers.ActionProvider.get("category_folder_add").url + "?" + dojo.objectToQuery(this._criteria);
		this._helper.showDialog(url, {
			title: this._i18n.folder.add.title,
			style: "width: 250px",
			refreshOnShow: true
		});
	},
	
	deleteFolder: function(/*category.js.views.FolderItemView*/ folderItemView) {
		// summary:
		//		Deletes given folder
		var params = {
			folder_id: folderItemView.getFolder().folder_id
		};
		var url = core.js.base.controllers.ActionProvider.get("category_folder_delete").url + "?" + dojo.objectToQuery(params);
		this._helper.showDialog(url, {
			title: this._i18n.folder["delete"].title,
			style: "width: 250px",
			refreshOnShow: true
		});
	},
	
	renameFolder: function(/*category.js.views.FolderItemView*/ folderItemView) {
		// summary:
		//		Renames given folder
		var _this	 = this;
		var folderId = folderItemView.getFolder().folder_id;
		if (!folderItemView.nameEditBox) {
			folderItemView.nameEditBox = new dijit.InlineEditBox({
				editor: "dijit.form.TextBox", 
				autoSave: true, 
				disabled: false,
				editorParams: {
					required: true
				},
				onChange: function(newName) {
					this.set("disabled", true);
					if (newName != "") {
						dojo.xhrPost({
							url: core.js.base.controllers.ActionProvider.get("category_folder_rename").url,
							content: {
								folder_id: folderId,
								name: newName
							},
							handleAs: "json",
							load: function(data) {
								dojo.publish("/app/global/notification", [{
									message: _this._i18n.folder.rename[data.result == "APP_RESULT_OK" ? "success" : "error"],
									type: (data.result == "APP_RESULT_OK") ? "message" : "error"
								}]);
								if (data.result == "APP_RESULT_OK") {
									folderItemView.getFolder().name = data.name;
								}
							}
						});
					}
				},
				onCancel: function() {
					this.set("disabled", true);
				}
			}, folderItemView.getFolderNameNode());
		}
		folderItemView.nameEditBox.set("disabled", false);
		folderItemView.nameEditBox.startup();
		folderItemView.nameEditBox.edit();
	},
	
	search: function(/*Object*/ criteria) {
		// summary:
		//		Searches for folders
		dojo.mixin(this._criteria, criteria);
		
		var _this = this;
		dojo.xhrPost({
			url: core.js.base.controllers.ActionProvider.get("category_folder_list").url,
			content: dojo.mixin({
				format: "html"
			}, this._criteria),
			load: function(data) {
				_this._folderListView.setContent(data);
			}
		});
	}
});
