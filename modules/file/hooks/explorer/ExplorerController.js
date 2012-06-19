/**
 * NextCMS
 * 
 * @author		Nguyen Huu Phuoc <thenextcms@gmail.com>
 * @copyright	Copyright (c) 2011 - 2012, Nguyen Huu Phuoc
 * @license		http://nextcms.org/license.txt	(GNU GPL version 2 or later)
 * @link		http://nextcms.org
 * @category	modules
 * @package		file
 * @subpackage	hooks
 * @since		1.0
 * @version		2012-03-10
 */

dojo.provide("file.hooks.explorer.ExplorerController");

dojo.require("core.js.base.controllers.ActionProvider");
dojo.require("core.js.base.Encoder");

dojo.declare("file.hooks.explorer.ExplorerController", null, {
	// _id: String
	_id: null,
	
	// _toolbar: file.hooks.explorer.ExplorerToolbar
	_toolbar: null,
	
	// _fileListView: file.hooks.explorer.FileListView
	_fileListView: null,
	
	// _pathBreadcrumb: file.js.views.PathBreadcrumb
	_pathBreadcrumb: null,
	
	// _criteria: Object
	_criteria: {
		path: "",
		page: 1,
		per_page: 30,
		view_type: "grid"
	},
	
	constructor: function(/*String*/ id) {
		this._id = id;
	},
	
	setToolbar: function(/*file.hooks.explorer.ExplorerToolbar*/ toolbar) {
		// summmary:
		//		Sets the toolbar
		this._toolbar = toolbar;
		
		// Go home handler
		dojo.connect(toolbar, "onGoHome", this, function() {
			this._criteria.page = 1;
			this.gotoPath("/");
		});
		
		// Refresh handler
		dojo.connect(toolbar, "onRefresh", this, "searchFiles");
		
		// Change view type handler
		dojo.connect(toolbar, "onChangeViewType", this, function(viewType) {
			this._criteria.view_type = viewType;
			if (this._fileListView) {
				this._fileListView.setViewType(viewType);
			}
		});
		
		// Update page size handler
		dojo.connect(toolbar, "onUpdatePageSize", this, function(perPage) {
			this.searchFiles({
				per_page: perPage
			});
		});
		
		return this;	// file.hooks.explorer.ExplorerController
	},
	
	setFileListView: function(/*file.hooks.explorer.FileListView*/ fileListView) {
		// summary:
		//		Sets the file list view
		this._fileListView = fileListView;
		
		// Open directory handler
		dojo.connect(fileListView, "onOpenDir", this, "gotoPath");
		
		// Paging handler
		dojo.subscribe("/app/file/hooks/explorer/search/onGotoPage", this, function(page) {
			this.searchFiles({
				page: page
			});
		});
		
		return this;	// file.hooks.explorer.ExplorerController
	},
	
	setPathBreadcrumb: function(/*file.js.views.PathBreadcrumb*/ pathBreadcrumb) {
		// summary:
		//		Sets the path breadcrumb
		this._pathBreadcrumb = pathBreadcrumb;
		
		dojo.connect(pathBreadcrumb, "onGotoPath", this, "gotoPath");
		return this;	// file.hooks.explorer.ExplorerController
	},
	
	gotoPath: function(/*String*/ path) {
		// summary:
		//		Opens given path
		if (!path) {
			return;
		}
		while (path[0] == "." || path[0] == "/") {
			path = path.substring(1);
		}
		this._pathBreadcrumb.show(path);
		this.searchFiles({
			path: path
		});
	},
	
	searchFiles: function(/*Object*/ criteria) {
		// summary:
		//		Searches for files
		dojo.mixin(this._criteria, criteria);
		var q = core.js.base.Encoder.encode(this._criteria);

		var params = {
			_type: "hook",
			_mod: "file",
			_name: "explorer",
			_method: "search",
			q: q
		};
		dijit.byNode(this._fileListView.getDomNode())
			 .set("href", core.js.base.controllers.ActionProvider.get("core_extension_render").url + "?" + dojo.objectToQuery(params));
	}
});
