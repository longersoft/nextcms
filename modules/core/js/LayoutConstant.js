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
 * @version		2011-12-24
 */

// summary:
//		This class provides all constants for the Layout Editor
//		DO NOT CHANGE VALUES OF CONSTANTS
dojo.provide("core.js.LayoutConstant");

// PORTLET_CONTAINER_CLASS: [const] String
//		The CSS class of portlet and tab container
//		The grid container can consist of portlet or tab container, so this CSS
//		class is used to query the number of portlets inside a grid column, or 
//		index of an element in grid column
core.js.LayoutConstant.PORTLET_CONTAINER_CLASS = "appPortletContainer";

// WIDGET_INPUT_CLASS: [const] String
//		The CSS class of widget elements inside the portlet
core.js.LayoutConstant.WIDGET_INPUT_CLASS = "appWidgetInput";

// WIDGET_INPUT_TINYMCE_CLASS: [const] String
//		The CSS class of textarea elements which attach an TinyMCE editor
core.js.LayoutConstant.WIDGET_INPUT_TINYMCE_CLASS = "appWidgetInputTinyMce";
