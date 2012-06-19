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
 * @version		2012-01-07
 */

dojo.provide("file.hooks.explorer.FileItemView");

dojo.require("dojo.dnd.Source");

dojo.require("core.js.base.Encoder");

dojo.declare("file.hooks.explorer.FileItemView", null, {
	// _domNode: DomNode
	_domNode: null,

	// _fileListView: file.hooks.explorer.FileListView
	_fileListView: null,
	
	// _file: Object
	_file: null,
	
	constructor: function(/*DomNode*/ node, /*file.hooks.explorer.FileListView*/ fileListView) {
		this._domNode	   = node;
		this._fileListView = fileListView;
		this._file		   = core.js.base.Encoder.decode(dojo.attr(this._domNode, "data-app-entity-props"));
		this._init();
	},
	
	_init: function() {
		var _this = this;
		var iconNode = dojo.query(".fileHooksExplorerFileIcon", this._domNode)[0];
		
		this.setViewType(this._file.view_type);
		
		if (this._file.is_dir) {
			// Open the dir when click on the icon
			dojo.connect(iconNode, "onclick", this, function() {
				this._fileListView.onOpenDir(this._file.path);
			});
		}
		
		if (this._file.extension) {
			var image = dojo.query("img", iconNode)[0];
			dojo.addClass(image, "dojoDndItem");
			
			// Drag and drop
			var dndtype, data;
			switch (this._file.extension.toLowerCase()) {
				case "bmp":
				case "gif":
				case "jpeg":
				case "jpg":
				case "png":
					dndtype = "appDndImage";
					dojo.attr(image, {
						dndtype: "appDndImage",
						"data-app-dndimage": dojo.toJson({
							url: dojo.attr(image, "src"),
							title: this._file.name
						})
					});
					break;
				// TODO: Add more extensions supported by the Strobe Media Playback
				case "f4v":
				case "flv":
				case "m4v":
				case "mov":
				case "mp3":
				case "mp4":
				case "swf":
					dojo.attr(image, {
						dndtype: "appDndVideo,appDndLink",
						"data-app-dndlink": core.js.base.Encoder.encode({
							url: this._file.upload_dir + "/" + this._file.path,
							title: this._file.name
						}),
						"data-app-dndvideo": core.js.base.Encoder.encode({
							src: this._file.upload_dir + "/" + this._file.path,
							poster: ""
						})
					});
					break;
				default:
					dndtype = "appDndLink";
					dojo.attr(image, {
						dndtype: "appDndLink",
						"data-app-dndlink": core.js.base.Encoder.encode({
							url: this._file.upload_dir + "/" + this._file.path,
							title: this._file.name
						})
					});
					break;
			}
			
			new dojo.dnd.Source(iconNode, {
				accept: [ dndtype ],
				selfAccept: false,
				selfCopy: false
			});
		}
	},
	
	setViewType: function(/*String*/ viewType) {
		// summary:
		//		Sets the view type
		// viewType:
		//		Can be "grid" or "list"
		var currentClass = (viewType == "list") ? "fileHooksExplorerGridView" : "fileHooksExplorerListView";
		var newClass	 = (viewType == "list") ? "fileHooksExplorerListView" : "fileHooksExplorerGridView";
		
		dojo.removeClass(this._domNode, currentClass);
		dojo.addClass(this._domNode, newClass);
	}
});
