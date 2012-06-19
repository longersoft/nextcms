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
 * @version		2011-10-18
 */

dojo.provide("category.js.controllers.CategoryMediator");

dojo.declare("category.js.controllers.CategoryMediator", null, {
	// _categoryTreeView: category.js.views.CategoryTreeView
	_categoryTreeView: null,
	
	setCategoryTreeView: function(/*category.js.views.CategoryTreeView*/ categoryTreeView) {
		// summary:
		//		Sets the category tree view
		this._categoryTreeView = categoryTreeView;
		
		dojo.connect(categoryTreeView, "onNodeContextMenu", this, function(item) {
			// Disable delete/edit/rename menu items when selecting the root node
			var isRoot = item.root;
			categoryTreeView.allowToDelete(!isRoot)
							.allowToEdit(!isRoot)
							.allowToRename(!isRoot);
		});
	}
});
