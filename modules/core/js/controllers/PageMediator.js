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
 * @version		2012-02-05
 */

dojo.provide("core.js.controllers.PageMediator");

dojo.declare("core.js.controllers.PageMediator", null, {
	// _pageToolbar: core.js.views.PageToolbar
	_pageToolbar: null,
	
	// _pageGrid: core.js.views.PageGrid
	_pageGrid: null,
	
	setPageToolbar: function(/*core.js.views.PageToolbar*/ toolbar) {
		// summary:
		//		Sets the page toolbar
		this._pageToolbar = toolbar;
		
		dojo.connect(this._pageToolbar, "onSelectTemplate", this, function(template) {
			this._pageToolbar.allowToSaveOrder(template && this._pageToolbar.criteria.language);
		});
		dojo.connect(this._pageToolbar, "onSwitchToLanguage", this, function(language) {
			this._pageToolbar.allowToSaveOrder(language && this._pageToolbar.criteria.template);
		});
	},
	
	setPageGrid: function(/*core.js.views.PageGrid*/ grid) {
		// summary:
		//		Sets the page grid
		this._pageGrid = grid;
		
		dojo.connect(grid, "onRowContextMenu", this, "onPageGridContextMenu");
	},
	
	onPageGridContextMenu: function(/*dojo.data.item*/ item) {
		// summary:
		//		Called when right-clicking the page grid
		var layoutData = this._pageGrid.getCurrentLayoutData();
		// Don't allow to paste the layout to original page
		this._pageGrid.allowToCopyLayout(item.layout[0])
					  .allowToPasteLayout(layoutData.layout && layoutData.page_id && item.page_id[0] != layoutData.page_id)
					  .allowToExportLayout(item.layout[0]);
	}
});
