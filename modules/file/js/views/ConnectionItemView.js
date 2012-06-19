/**
 * NextCMS
 * 
 * @author		Nguyen Huu Phuoc <thenextcms@gmail.com>
 * @copyright	Copyright (c) 2011 - 2012, Nguyen Huu Phuoc
 * @license		http://nextcms.org/license.txt	(GNU GPL version 2 or later)
 * @link		http://nextcms.org
 * @category	modules
 * @package		file
 * @subpackage	js
 * @since		1.0
 * @version		2012-03-28
 */

dojo.provide("file.js.views.ConnectionItemView");

dojo.require("core.js.base.Encoder");

dojo.declare("file.js.views.ConnectionItemView", null, {
	// _domNode: DomNode
	_domNode: null,
	
	// _connection: Object
	_connection: null,
	
	// _connectionListView: file.js.views.ConnectionListView
	_connectionListView: null,
	
	constructor: function(/*DomNode*/ domNode, /*file.js.views.ConnectionListView?*/ connectionListView) {
		this._domNode			 = domNode;
		this._connectionListView = connectionListView;
		this._connection		 = core.js.base.Encoder.decode(dojo.attr(domNode, "data-app-entity-props")); 
		this._init();
	},
	
	_init: function() {
		// summary:
		//		Initialize node
		var _this = this;
		dojo.connect(this._domNode, "oncontextmenu", function(e) {
			e.preventDefault();
		});
		dojo.connect(this._domNode, "onmousedown", this, function(e) {
			if (dojo.mouseButtons.isRight(e)) {
				e.preventDefault();
				this._connectionListView.onMouseDown(this);
			}
		});
	},
	
	getDomNode: function() {
		return this._domNode;	// DomNode
	},
	
	getConnection: function() {
		// summary:
		//		Gets the connection's properties
		return this._connection;	// Object
	},
	
	getNameNode: function() {
		return dojo.query(".fileConnectionName", this._domNode)[0];		// DomNode
	}
});
