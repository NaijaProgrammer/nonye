//@author Michael Orji (NaijaProgrammer)
var TabManager = {
	
	activeTabID : 0,
	tabsLength  : 0,
	
	setTabsLength : function(n)
	{
		this.tabsLength = n ;
	},
	
	getTabsLength : function()
	{
		this.setTabsLength(this.getTabNavs().length);
		return this.tabsLength;
	},
	
	getTabNavs : function( tabNavClass )
	{
		tabNavClass = tabNavClass || '.tab-nav';
		return document.querySelectorAll(tabNavClass);
		//return document.querySelectorAll('.tab-nav');
	},
	
	getMaxIndex : function()
	{
		return this.getTabsLength() - 1;
	},
	
	setActiveTab : function(tab)
	{
		this.activeTab = tab;
	},
	
	setActiveTabID : function(tabID)
	{
		this.activeTabID = tabID;
	},
	
	getActiveTab : function()
	{
		return this.activeTab;
	},
	
	getActiveTabID : function()
	{
		return this.activeTabID;
	},
	
	updateTabs : function(activeTabNav)
	{
		var tabNavs = this.getTabNavs();
		
		for(var i = 0, len = tabNavs.length; i < len; i++)
		{
			currTabNav = tabNavs[i];
			
			if(currTabNav == activeTabNav)
			{  
				this.show(currTabNav.getAttribute('data-tab-content'));
				this.setActiveTab(currTabNav.getAttribute('data-tab-content'));
				this.setActiveTabID(i);
				Site.Util.addClassTo(currTabNav, 'active-tab-nav');
				//$Style(currTabNav).border = '2px solid #ccc';
				//$Style(currTabNav).fontWeight = 'bold';
			}
			else
			{  
				this.hide(currTabNav.getAttribute('data-tab-content'));
				Site.Util.removeClassFrom(currTabNav, 'active-tab-nav');
				//$Style(currTabNav).border = '1px solid #ccc';
				//$Style(currTabNav).fontWeight = 'normal';
			}
		}
	},
	
	next : function()
	{
		var currTabID = this.getActiveTabID();
		var nextTabID = currTabID + 1;
		nextTabID     = ( nextTabID > this.getMaxIndex() ? 0 : nextTabID ); //if we go beyond the last tab, move to first tab
		this.moveToTab( nextTabID );
		this.setActiveTabID( nextTabID );
	},

	previous : function()
	{
		var currTabID = this.getActiveTabID();
		var prevTabID = currTabID - 1;
		prevTabID     = ( prevTabID < 0 ? this.getMaxIndex() : prevTabID ); //if we go below the first tab, move to last tab
		this.moveToTab( prevTabID );
		this.setActiveTabID( prevTabID );
	},

	moveToTab : function(tabID)
	{
		this.updateTabs( this.getTabNavs()[tabID] ); //TabManager.updateTabs( TabManager.getTabNavs()[0] );
	},
	
	show : function(elem)
	{
		jQuery('#' + elem).slideDown();
		//Site.Util.removeClassFrom(elem, 'no-display');
	},
		
	hide : function(elem)
	{ 
		jQuery('#' + elem).slideUp();
		//Site.Util.addClassTo(elem, 'no-display'); 
	}
}