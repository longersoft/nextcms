/**
 * NextCMS
 * 
 * @author		Nguyen Huu Phuoc <thenextcms@gmail.com>
 * @copyright	Copyright (c) 2011 - 2012, Nguyen Huu Phuoc
 * @license		http://nextcms.org/license.txt	(GNU GPL version 2 or later)
 * @link		http://nextcms.org
 * @category	static
 * @package		js
 * @subpackage	tiny_mce
 * @since		1.0
 * @version		2012-03-19
 */

// Please ensure that you push the contentsplitter plugin after the contextmenu plugin
// when declaring the required plugins:
//		tinyMCE.init({
//			plugins: '...,contextmenu,...,contentsplitter,...
//			content_splitter_menu_label: 'Split to new widget'
//		});

dojo.require("dojo.NodeList-traverse");

dojo.require("core.js.views.LayoutContainerManager");

(function() {
	tinymce.create("tinymce.plugins.contentsplitter", {
		init : function(ed) {
			var t = this;

			t.editor = ed;
			t.onContextMenu = new tinymce.util.Dispatcher(this);

			ed.addCommand("splitContent", function() {
				if (ed.getContent() == "") {
					return;
				}
				
				// Get the selection content in HTML format
				var newWidgetContent = ed.selection.getContent({ format: "html" });
				
				// If user does not select text
				if (newWidgetContent == "") {
					// Get entire content of the editor
					var content = ed.getContent();
					
					// Get root node containing the editor
					var rootNode = ed.getBody().firstChild;
					
					var selection = ed.selection.getSel();
					
					// Create a range
					var range = ed.getDoc().createRange();
					try {
						// FIXME: It does not work
						range.setStart(selection.anchorNode, selection.anchorOffset);
						range.setEnd(rootNode.firstChild, (rootNode.lastChild.nodeType == 3) ? rootNode.lastChild.textContent.length : rootNode.lastChild.outerHTML.length);
						ed.selection.setRng(range);
						
						// Get content of range
						newWidgetContent = ed.selection.getContent({ format: "html" });
					} catch (ex) {
						console.log(ex);
					}
				}
				
				// Remove selection text from current editor
				ed.selection.getRng().extractContents();
				
				// console.log(newWidgetContent);
				
				// Our markup
				//	<div class="appLayoutContainer">	<!-- layout container node -->	
				//		<div class="gridContainer">		<!-- grid container node -->
				//			<table class="gridContainerTable">
				//				<tr>
				//					<td class="gridContainerZone">	<!-- zone node -->
				//						<div class="dojoxPortlet">	<!-- portlet node -->
				// 							<div class="dojoxPortletSettingsContainer">		<!-- portlet settings node -->
				//								...
				//								<textarea id="" name=""></textarea>
				//								...
				//							</div>
				//						</div>
				//					</td>
				//				</tr>
				//			</table>
				//		</div>
				//	</div>
				var textareaId	 = ed.id;
				var textareaName = dojo.attr(textareaId, "appWidgetInputName");
				var zoneNodes	 = dojo.query("#" + textareaId).closest(".gridContainerZone");
				var portletNodes = dojo.query("#" + textareaId).closest(".dojoxPortlet");
				var gridNodes	 = dojo.query("#" + textareaId).closest(".gridContainer");
				var layoutNodes	 = dojo.query("#" + textareaId).closest(".appLayoutContainer");
				if (zoneNodes.length == 0 || portletNodes.length == 0 || gridNodes.length == 0 || layoutNodes.length == 0) {
					return;
				}
				var layoutContainer = core.js.views.LayoutContainerManager.get(dojo.attr(layoutNodes[0], "id"));
				var gridContainer	= dijit.byNode(gridNodes[0]);
				var portlet			= dijit.byNode(portletNodes[0]);
				var zoneIndex		= dojo.indexOf(dojo.query(".gridContainerZone", zoneNodes[0].parentNode), zoneNodes[0]);
				var portletIndex	= dojo.indexOf(dojo.query(".dojoxPortlet", zoneNodes[0]), portletNodes[0]); 
				
				if (layoutContainer) {
					// Get the widget data via special property named "_widget" defined in LayoutContainer::addPortlet()
					// (See /modules/core/js/views/LayoutContainer.js)
					var widget = portlet._widget;
					widget.params = {};
					widget.params[textareaName] = newWidgetContent;
					
					layoutContainer.addPortlet(gridContainer, widget, zoneIndex, portletIndex + 1, true);
				}
			});
			
			ed.onContextMenu.add(function(ed, e) {
				// Get current context menu
				var contextMenu = ed.controlManager.get("contextmenu");
				contextMenu.addSeparator();
				
				// Add new menu item
				contextMenu.add({
					title: ed.getParam("contentsplitter_menu_label", "Split to new widget"),
					cmd: "splitContent"
				});
			});
		},

		getInfo : function() {
			return {
				longname : "ContentSplitter",
				author : "Nguyen Huu Phuoc",
				authorurl : "",
				infourl : "",
				version : "1.0"
			};
		}
	});

	// Register plugin
	tinymce.PluginManager.add("contentsplitter", tinymce.plugins.contentsplitter);
})();
