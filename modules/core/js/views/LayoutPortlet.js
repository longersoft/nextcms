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

dojo.provide("core.js.views.LayoutPortlet");

dojo.require("dojox.layout.ContentPane");
dojo.require("dojox.widget.Portlet");

// DOJO LESSON: Because dojox.widget.Portlet extends from dijit.layout.ContentPane,
// it cannot execute JS code inside the content:
//		porlet.set("content", "...");
// I create new class which extends from both dojox.widget.Portlet and dojox.layout.ContentPane
dojo.declare("core.js.views.LayoutPortlet", [dojox.widget.Portlet, dojox.layout.ContentPane], {});
