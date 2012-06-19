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

dojo.provide("core.js.base.dnd.TargetManager");

dojo.require("dojo.dnd.Source");

dojo.require("core.js.base.controllers.ActionProvider");
dojo.require("core.js.base.Encoder");

dojo.declare("core.js.base.dnd.TargetManager", null, {
	// ID_PREFIX: [const] String
	ID_PREFIX: "baseDndTarget_",
	
	// _uniqueId: Integer
	_uniqueId: 0,
	
	// _targets: Array
	_targets: [],
	
	// _handlers: Array
	_handlers: [],
	
	add: function(/*DomNode*/ node, /*Array*/ acceptClasses, /*Function*/ onDropExternalCallback) {
		// summary:
		//		It is impossible to execute multiple onDropExternal handlers on dojo.dnd.Target instances
		//		of the same node as follow:
		//		| 	new dojo.dnd.Target(node, {
		//		|		accept: ["a"],
		//		|		onDropExternal: function(source, nodes, copy) {
		//		|			console.log("a");
		//		|		}
		//		| 	});
		//	
		//		| 	new dojo.dnd.Target(node, {
		//		|		accept: ["b"],
		//		|		onDropExternal: function(source, nodes, copy) {
		//		|			console.log("b");
		//		|		}
		//		| 	});
		//		You will get only "a" or "b" (in the console window) depending on which target instance is created first.
		//		This method allows you to add many onDropExternal callbacks as you want.
		// example:
		//		| 	var manager = core.js.base.dnd.TargetManager.getInstance();
		//		| 	manager.add(node, ["a"], function(node, source, nodes, copy) {
		//		|		console.log("a");
		//		| 	});
		//		| 	manager.add(node, ["b"], function(node, source, nodes, copy) {
		//		|		console.log("b");
		//		| 	});
		//		And you will see both "a" and "b" (in the console window)
		//		after dropping acceptable nodes to the target node.
		
		var id = dojo.attr(node, "id");
		if (!id) {
			// Generate unique Id for the node
			this._uniqueId++;
			id = this.ID_PREFIX + this._uniqueId;
			dojo.attr(node, "id", id);
		}
		
		if (!this._targets[id]) {
			// If the node is not in the list, create new target
			var target = new dojo.dnd.Target(node, {
				accept: acceptClasses,
				onDropExternal: function(source, nodes, copy) {
					onDropExternalCallback(target, source, nodes, copy);
				}
			});
			this._targets[id]  = target;
			this._handlers[id] = [ onDropExternalCallback ];
		} else {
			var target = this._targets[id];
			// Update "accept" property
			for (var i = 0; i < acceptClasses.length; i++) {
				if (!target.accept[acceptClasses[i]]) {
					target.accept[acceptClasses[i]] = 1;
				}
			}
			
			// Avoid to duplicate handlers
			if (this._handlers[id].indexOf(onDropExternalCallback) == -1) {
				this._handlers[id].push(onDropExternalCallback);
				
				// Connect the onDropExternal method
				dojo.connect(target, "onDropExternal", function(source, nodes, copy) {
					onDropExternalCallback(target, source, nodes, copy);
				});
			}
		}
	},
	
	addTarget: function(/*dojo.dnd.Target*/ target) {
		// summary:
		//		Adds given target instance
		var id = dojo.attr(target.node, "id");
		this._targets[id]  = target;
		this._handlers[id] = new Array();
	},
	
	deleteTarget: function(/*String*/ id) {
		// summary:
		//		Removes the dnd target of given element
		// id:
		//		Id of element
		if (this._targets[id]) {
			delete this._targets[id];
		}
	}
});

// Unique instance
core.js.base.dnd.TargetManager._instance = null;

core.js.base.dnd.TargetManager.getInstance = function() {
	// summary:
	//		Gets the unique instance of TargetManager
	if (core.js.base.dnd.TargetManager._instance == null) {
		core.js.base.dnd.TargetManager._instance = new core.js.base.dnd.TargetManager();
	}
	return core.js.base.dnd.TargetManager._instance;	// core.js.base.dnd.TargetManager
};

////////// COMMON HANDLERS //////////

core.js.base.dnd.TargetManager.handleDropImage = function(/*dojo.dnd.Target*/ target, source, nodes, copy) {
	// summary:
	//		Called when dropping an image, which has dndtype attribute as "appDndImage",
	//		to textboxes, textareas, TinyMCE editors
	// description:
	//		To support dragging and dropping image, use one of the following markups:
	//
	//		<div class="dojoDndItem" dndtype="appDndImage,otherDndType" data-app-dndimage="data">
	//		</div>
	//		where data is encoded JSON string from an object that has the following properties:
	//		- url: The image's source
	//		- title: The image's title
	//		
	//		Or:
	//		
	//		<div class="dojoDndItem" dndtype="appDndImage,otherDndType">
	//			<img src="url" title="title" />
	//		</div>
	var dndType = dojo.attr(nodes[0], "dndtype");
	if (!dndType) {
		return;
	}
	dndType = dndType.split(",");
	if (dojo.indexOf(dndType, "appDndImage") == -1) {
		return;
	}
	
	var data = dojo.attr(nodes[0], "data-app-dndimage"), url, title;
	if (data) {
		data  = dojo.fromJson(data);
		url   = data.url;
		title = data.title || "";
	} else {
		// Try to find the first img element
		var images = dojo.query("img", nodes[0]);
		if (images.length > 0) {
			url   = dojo.attr(images[0], "src");
			title = dojo.attr(images[0], "title") || "";
		}
	}
	
	if (!url) {
		return;
	}
	var node = target.node;
	switch (true) {
		case (node.tagName == "INPUT"):
			dojo.attr(node, "value", url);
			break;
		case (node.tagName == "SPAN"):
			// TinyMCE generates a span element to contains TinyMCE's iframe:
			// 		<span id="textareaID_parent"></span>
			// Since Dojo does not support dropping a dragged object to an iframe,
			// I have to drop to the span element.
			if (dojo.hasClass(node, "mceEditor")) {
				var editorId = dojo.attr(node, "id");
				editorId     = editorId.substr(0, editorId.length - String("_parent").length);
				
				var originalImageUrl = core.js.Constant.normalizeUrl(url);
				var thumbs = dojo.attr(nodes[0], "data-app-dndthumbnails");
				if (thumbs) {
					thumbs = dojo.fromJson(thumbs);
					if (thumbs.original) {
						originalImageUrl = core.js.Constant.normalizeUrl(thumbs.original);
					}
				}
				tinyMCE.getInstanceById(editorId).execCommand('mceInsertContent', false, '<span class="appDroppedImage"><a href="' + originalImageUrl + '" title="' + title + '"><img src="' + originalImageUrl + '" title="' + title + '" /></a><br /><cite>' + title + "</cite></span>");
			}
			break;
		case (node.tagName == "TEXTAREA"):
			if (widget = dijit.byNode(node)) {
				widget.set("value", widget.get("value") + url);
			} else {
				node.innerHTML += url;
			}
			break;
		default:
			break;
	}
	
	dojo.publish("/app/global/dnd/onDropImage", [{
		url: url,
		title: title,
		source: nodes[0],
		target: node
	}]);
};

core.js.base.dnd.TargetManager.handleDropLink = function(/*dojo.dnd.Target*/ target, source, nodes, copy) {
	// summary:
	//		Called when dropping a link container, which has dndtype attribute as "appDndLink",
	//		to textboxes, textareas, TinyMCE editors
	// description:
	//		To support dragging and dropping image, use one of the following markups:
	//
	//		<div class="dojoDndItem" dndtype="appDndLink,otherDndType" data-app-dndlink="data">
	//		</div>
	//		where data is encoded string formatted by the core.js.base.Encoder.encode(object) method
	//		object contains the following properties:
	//		- url: The URL
	//		- title: The link's title
	//
	//		Or:
	//		
	//		<div class="dojoDndItem" dndtype="appDndLink,otherDndType">
	//			<a href="url">title</a>
	//		</div>
	var dndType = dojo.attr(nodes[0], "dndtype");
	if (!dndType) {
		return;
	}
	dndType = dndType.split(",");
	if (dojo.indexOf(dndType, "appDndLink") == -1) {
		return;
	}
	
	var data = dojo.attr(nodes[0], "data-app-dndlink"), url, title;
	if (data) {
		data  = core.js.base.Encoder.decode(data);
		url   = data.url;
		title = data.title || "";
	} else {
		// Try to find the first link
		var links = dojo.query("a", nodes[0]);
		if (links.length > 0) {
			url   = dojo.attr(links[0], "href");
			title = dojo.attr(links[0], "innerHTML") || "";
		}
	}
	
	if (!url) {
		return;
	}
	url = core.js.Constant.normalizeUrl(url);
	var node = target.node;
	switch (true) {
		case (node.tagName == "INPUT"):
			dojo.attr(node, "value", url);
			break;
		case (node.tagName == "SPAN"):
			// TinyMCE generates a span element to contains TinyMCE's iframe:
			// 		<span id="textareaID_parent"></span>
			// Since Dojo does not support dropping a dragged object to an iframe,
			// I have to drop to the span element.
			if (dojo.hasClass(node, "mceEditor")) {
				var editorId = dojo.attr(node, "id");
				editorId = editorId.substr(0, editorId.length - String("_parent").length);
				if (dojo.indexOf(dndType, "appDndVideo") != -1) {
					// Do not insert the link if user drag a video
					tinyMCE.getInstanceById(editorId).execCommand('mceInsertContent', false, '<br /><cite>' + title + '</cite><br />');
				} else {
					tinyMCE.getInstanceById(editorId).execCommand('mceInsertContent', false, '<a href="' + url + '" title="' + title + '">' + title + '</a>');
				}
			}
			break;
		case (node.tagName == "TEXTAREA"):
			if (widget = dijit.byNode(node)) {
				widget.set("value", widget.get("value") + url);
			} else {
				node.innerHTML += url;
			}
			break;
		default:
			break;
	}
	
	dojo.publish("/app/global/dnd/onDropLink", [{
		url: url,
		title: title,
		source: nodes[0],
		target: node
	}]);
};

core.js.base.dnd.TargetManager.handleDropVideo = function(/*dojo.dnd.Target*/ target, source, nodes, copy) {
	// summary:
	//		Called when dropping a video container, which has dndtype attribute as "appDndVideo",
	//		to textboxes, textareas, TinyMCE editors
	// description:
	//		To support dragging and dropping video, use one of the following markups:
	//
	//		<div class="dojoDndItem" dndtype="appDndVideo,otherDndType" data-app-dndvideo="data">
	//		</div>
	//		where data is encoded string formatted by the core.js.base.Encoder.encode(object) method
	//		object contains the following properties:
	//		- src: The video source URL
	//		- poster: The video poster
	//		- width (optional): The width of the player
	//		- height (optional): The height of the player
	//		- url (optional): The link of URL that shows embed player
	var dndType = dojo.attr(nodes[0], "dndtype");
	if (!dndType) {
		return;
	}
	dndType = dndType.split(",");
	if (dojo.indexOf(dndType, "appDndVideo") == -1) {
		return;
	}
	var data = dojo.attr(nodes[0], "data-app-dndvideo");
	if (!data) {
		return;
	}
	data = core.js.base.Encoder.decode(data);
	if (!data.url && !data.src) {
		return;
	}
	var node = target.node, embedUrl;
	switch (true) {
		case (node.tagName == "INPUT"):
			break;
		case (node.tagName == "SPAN"):
			if (dojo.hasClass(node, "mceEditor")) {
				var editorId = dojo.attr(node, "id");
				editorId = editorId.substr(0, editorId.length - String("_parent").length);
				
				var width = data.width || 640, height = data.height || 360;
				// Get the embed URL
				if (data.url) {
					embedUrl = data.url;
				} else {
					embedUrl = core.js.base.controllers.ActionProvider.get("core_helper_play").url + "?src=" + core.js.Constant.normalizeUrl(data.src) + "&amp;poster=" + core.js.Constant.normalizeUrl(data.poster);
				}
				tinyMCE.getInstanceById(editorId).execCommand('mceInsertContent', false, '<iframe src="' + embedUrl + '" width="' + width + '" height="' + height + '"></iframe>');
			}
			break;
		case (node.tagName == "TEXTAREA"):
			break;
		default:
			break;
	}
	
	dojo.publish("/app/global/dnd/onDropVideo", [{
		url: embedUrl,
		source: nodes[0],
		target: node
	}]);
};
