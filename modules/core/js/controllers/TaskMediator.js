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

dojo.provide("core.js.controllers.TaskMediator");

dojo.declare("core.js.controllers.TaskMediator", null, {
	// _taskGrid: core.js.views.TaskGrid
	_taskGrid: null,
	
	setTaskGrid: function(/*core.js.views.TaskGrid*/ grid) {
		// summary:
		//		Sets the task grid
		this._taskGrid = grid;
		
		dojo.connect(grid, "onRowContextMenu", this, function(item) {
			var isInstalled = item.is_installed[0]; 
			if (isInstalled) {
				grid.allowToUninstall(true);
			} else {
				grid.allowToInstall(true);
			}
			grid.allowToConfig(isInstalled && item.is_configuable[0])
				.allowToSchedule(isInstalled);
		});
	}
});
