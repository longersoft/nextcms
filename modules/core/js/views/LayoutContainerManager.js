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
 * @version		2011-10-18
 */

dojo.provide("core.js.views.LayoutContainerManager");

// Manages LayoutContainer instances
core.js.views.LayoutContainerManager._containers = {};

core.js.views.LayoutContainerManager.add = function(/*core.js.views.LayoutContainer*/ container) {
	// summary:
	//		Adds an instance of LayoutContainer
	core.js.views.LayoutContainerManager._containers[container.getId()] = container;
};

core.js.views.LayoutContainerManager.get = function(/*String*/ id) {
	// summary:
	//		Gets the instance of container by given its Id
	// id:
	//		Id of container
	return core.js.views.LayoutContainerManager._containers[id];	// core.js.views.LayoutContainer
};
