/**
 * NextCMS
 * 
 * @author		Nguyen Huu Phuoc <thenextcms@gmail.com>
 * @copyright	Copyright (c) 2011 - 2012, Nguyen Huu Phuoc
 * @license		http://nextcms.org/license.txt	(GNU GPL version 2 or later)
 * @link		http://nextcms.org
 * @category	modules
 * @package		comment
 * @subpackage	js
 * @since		1.0
 * @version		2011-10-18
 */

dojo.provide("comment.js.controllers.ThreadMediator");

dojo.declare("comment.js.controllers.ThreadMediator", null, {
	// _commentContextMenu: comment.js.views.CommentContextMenu
	_commentContextMenu: null,
	
	setCommentContextMenu: function(/*comment.js.views.CommentContextMenu*/ commentContextMenu) {
		// summary:
		//		Sets the comment's context menu
		this._commentContextMenu = commentContextMenu;
		
		dojo.connect(commentContextMenu, "onContextMenu", this, function(commentItemView) {
			var status = commentItemView.getComment().status;
			commentContextMenu.allowToReportSpam(status != "spam");
		});
	}
});
