/**
 * NextCMS
 * 
 * @author		Nguyen Huu Phuoc <thenextcms@gmail.com>
 * @copyright	Copyright (c) 2011 - 2012, Nguyen Huu Phuoc
 * @license		http://nextcms.org/license.txt	(GNU GPL version 2 or later)
 * @link		http://nextcms.org
 * @category	modules
 * @package		content
 * @subpackage	js
 * @since		1.0
 * @version		2012-02-14
 */

dojo.provide("content.js.controllers.RevisionController");

dojo.require("core.js.base.controllers.ActionProvider");
dojo.require("core.js.base.controllers.Subscriber");
dojo.require("core.js.base.I18N");
dojo.require("content.js.controllers.RevisionMediator");

dojo.declare("content.js.controllers.RevisionController", null, {
	// _id: String
	_id: null,
	
	// _articleId: String
	_articleId: null,
	
	// _i18n: Object
	_i18n: null,
	
	// _helper: core.js.base.views.Helper
	_helper: null,
	
	// _revisionToolbar: content.js.views.RevisionToolbar
	_revisionToolbar: null,
	
	// _revisionGrid: content.js.views.RevisionGrid
	_revisionGrid: null,
	
	// _defaultCriteria: Object
	_defaultCriteria: {
		article_id: null,
		keyword: null
	},
	
	// _revisionMediator: content.js.controllers.RevisionMediator
	_revisionMediator: new content.js.controllers.RevisionMediator(),
	
	// TOPIC_GROUP: [const] String
	TOPIC_GROUP: "/content/js/controllers/RevisionController",
	
	constructor: function(/*String*/ id, /*String*/ articleId) {
		core.js.base.controllers.Subscriber.unsubscribe(this.TOPIC_GROUP);
		
		this._id = id;
		this._defaultCriteria.article_id = articleId;
		
		core.js.base.I18N.requireLocalization("content/languages");
		this._i18n = core.js.base.I18N.getLocalization("content/languages");
		
		// Create helper instance
		this._helper = new core.js.base.views.Helper(id);
		this._helper.setLanguageData(this._i18n);
	},
	
	setRevisionToolbar: function(/*content.js.views.RevisionToolbar*/ toolbar) {
		// summary:
		//		Sets the revision toolbar
		this._revisionToolbar = toolbar;
		
		// Refresh handler
		dojo.connect(toolbar, "onRefresh", this, "searchRevisions");
		
		// Seach handler
		dojo.connect(toolbar, "onSearchRevisions", this, function(keyword) {
			this.searchRevisions({
				keyword: keyword
			});
		});
		
		// Close toolbar handler
		dojo.connect(toolbar, "onClose", this, function() {
			dojo.publish("/app/content/revision/list/onClose");
		});
		
		return this;	// content.js.controllers.RevisionController
	},
	
	setRevisionGrid: function(/*content.js.views.RevisionGrid*/ revisionGrid) {
		// summary:
		//		Sets the revision grid
		this._revisionGrid = revisionGrid;
		this._revisionMediator.setRevisionGrid(revisionGrid);
		
		// Restore handler
		dojo.connect(revisionGrid, "onRestoreRevision", this, "restoreRevision");
		
		core.js.base.controllers.Subscriber.subscribe(this.TOPIC_GROUP, "/app/content/revision/restore/onCancel", this, function() {
			this._helper.closeDialog();
		});
		
		core.js.base.controllers.Subscriber.subscribe(this.TOPIC_GROUP, "/app/content/revision/restore/onComplete", this, function(data) {
			this._helper.closeDialog();
			dojo.publish("/app/global/notification", [{
				message: this._i18n.revision.restore[(data.result == "APP_RESULT_OK") ? "success" : "error"],
				type: (data.result == "APP_RESULT_OK") ? "message" : "error"
			}]);
			
			if (data.result == "APP_RESULT_OK") {
				// this.searchRevisions();
			}
		});
		
		// View revision handler
		dojo.connect(revisionGrid, "onViewRevision", this, "viewRevision");
		core.js.base.controllers.Subscriber.subscribe(this.TOPIC_GROUP, "/app/content/revision/view/onClose", this, function() {
			this._helper.removePane();
		});
		
		// Delete revision handler
		dojo.connect(revisionGrid, "onDeleteRevision", this, "deleteRevision");
		core.js.base.controllers.Subscriber.subscribe(this.TOPIC_GROUP, "/app/content/revision/delete/onCancel", this, function() {
			this._helper.closeDialog();
		});
		core.js.base.controllers.Subscriber.subscribe(this.TOPIC_GROUP, "/app/content/revision/delete/onComplete", this, function(data) {
			this._helper.closeDialog();
			dojo.publish("/app/global/notification", [{
				message: this._i18n.revision["delete"][(data.result == "APP_RESULT_OK") ? "success" : "error"],
				type: (data.result == "APP_RESULT_OK") ? "message" : "error"
			}]);
			
			if (data.result == "APP_RESULT_OK") {
				// this.searchRevisions();
			}
		});
		
		return this;	// content.js.controllers.RevisionController
	},
	
	deleteRevision: function(/*dojo.data.Item*/ item) {
		// summary:
		//		Deletes given revision
		var params = {
			revision_id: item.revision_id[0]
		};
		var url = core.js.base.controllers.ActionProvider.get("content_revision_delete").url + "?" + dojo.objectToQuery(params);
		this._helper.showDialog(url, {
			title: this._i18n.revision["delete"].title,
			style: "width: 250px",
			refreshOnShow: true
		});
	},
	
	restoreRevision: function(/*dojo.data.Item*/ item) {
		// summary:
		//		Restores given revision
		var params = {
			revision_id: item.revision_id[0]
		};
		var url = core.js.base.controllers.ActionProvider.get("content_revision_restore").url + "?" + dojo.objectToQuery(params);
		this._helper.showDialog(url, {
			title: this._i18n.revision.restore.title,
			style: "width: 250px",
			refreshOnShow: true
		});
	},
	
	searchRevisions: function(/*Object*/ criteria) {
		// summary:
		//		Searches for revisions
		criteria = dojo.mixin(this._defaultCriteria, criteria);
		if (this._revisionGrid) {
			this._revisionGrid.show(criteria);
		}
	},
	
	viewRevision: function(/*dojo.data.Item*/ item) {
		// summary:
		//		Shows revision's details
		var params = {
			revision_id: item.revision_id[0]
		};
		var url = core.js.base.controllers.ActionProvider.get("content_revision_view").url + "?" + dojo.objectToQuery(params);
		this._helper.showPane(url, {
			region: "bottom",
			splitter: true,
			gutters: false,
			minSize: 400,
			style: "height: 75%; width: 100%"
		});
	}
});
