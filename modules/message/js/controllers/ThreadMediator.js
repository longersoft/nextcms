/**
 * NextCMS
 * 
 * @author		Nguyen Huu Phuoc <thenextcms@gmail.com>
 * @copyright	Copyright (c) 2011 - 2012, Nguyen Huu Phuoc
 * @license		http://nextcms.org/license.txt	(GNU GPL version 2 or later)
 * @link		http://nextcms.org
 * @category	modules
 * @package		message
 * @subpackage	js
 * @since		1.0
 * @version		2012-02-28
 */

dojo.provide("message.js.controllers.ThreadMediator");

dojo.declare("message.js.controllers.ThreadMediator", null, {
	// _messageContextMenu: message.js.views.MessageContextMenu
	_messageContextMenu: null,
	
	setMessageContextMenu: function(/*message.js.views.MessageContextMenu*/ messageContextMenu) {
		// summary:
		//		Sets the message's context menu
		this._messageContextMenu = messageContextMenu;
		
		dojo.connect(messageContextMenu, "onContextMenu", function(/*message.js.views.MessageItemView*/ messageItemView) {
			var message = messageItemView.getMessage();
			messageContextMenu.allowToMoveExceptOne(message.folder_id);
			
			if (message.folder_id != "inbox" || message.deleted == "1") {
				messageContextMenu.allowToMove("inbox");
			}
			
			messageContextMenu.allowToStar(message.deleted + "" == "0");
		});
	}
});
