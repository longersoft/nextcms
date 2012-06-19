/**
 * NextCMS
 * 
 * @author		Nguyen Huu Phuoc <thenextcms@gmail.com>
 * @copyright	Copyright (c) 2011 - 2012, Nguyen Huu Phuoc
 * @license		http://nextcms.org/license.txt	(GNU GPL version 2 or later)
 * @link		http://nextcms.org
 * @category	modules
 * @package		media
 * @subpackage	js
 * @since		1.0
 * @version		2011-10-18
 */

dojo.provide("media.js.controllers.PhotoEditorMediator");

dojo.declare("media.js.controllers.PhotoEditorMediator", null, {
	// _undoButton: dijit.form.Button
	_undoButton: null,
	
	// _redoButton: dijit.form.Button
	_redoButton: null,
	
	// _undoableStates: Array
	_undoableStates: [],
	
	// _redoableStates: Array
	_redoableStates: [],
	
	setUndoButton: function(/*dijit.form.Button*/ undoButton) {
		// summary:
		//		Sets the undo button
		this._undoButton = undoButton;
	},
	
	setRedoButton: function(/*dijit.form.Button*/ redoButton) {
		// summary:
		//		Sets the redo button
		this._redoButton = redoButton;
	},
	
	addUndoableState: function(/*Object*/ state) {
		// summary:
		//		Adds the undoable state
		// state:
		//		Contains two member:
		//		- previous: The previous status of data
		//		- current: The current status of data
		this._undoableStates.push(state);
		this._updateUndoRedoButtonStatus();
	},
	
	_updateUndoRedoButtonStatus: function() {
		// You see the following checker: 
		//		this._undoableStates.length <= 1
		// because the undoable states array contains the original state
		this._undoButton.set("disabled", this._undoableStates.length <= 1);
		this._undoButton.attr("iconClass", "appIcon " + (this._undoableStates.length <= 1 ? "appUndoDisabledIcon" : "appUndoIcon"));
		this._redoButton.set("disabled", this._redoableStates.length == 0);
		this._redoButton.attr("iconClass", "appIcon " + (this._redoableStates.length == 0 ? "appRedoDisabledIcon" : "appRedoIcon"));
	},
	
	undo: function() {
		if (this._undoableStates.length == 0) {
			return null;
		}
		
		var state = this._undoableStates.pop();
		this._redoableStates.push(state);
		this._updateUndoRedoButtonStatus();
		return state;
	},
	
	redo: function() {
		if (this._redoableStates.length == 0) {
			return null;
		}
		
		var state = this._redoableStates.pop();
		this._undoableStates.push(state);
		this._updateUndoRedoButtonStatus();
		return state;
	}
});
