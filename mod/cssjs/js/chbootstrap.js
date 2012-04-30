window.addEvent('domready', function() {
	CaptainHook.Bootstrap.init();
});

CaptainHook.Bootstrap = {

	init: function() {
		this.initAlerts();
		this.initTabs();
	},

	initAlerts: function() {
		var els = document.body.getElements('div.alert .close') || undefined;
		if (els) {
			els.each(function(el) {
				el.addEvent('click', function() {
					this.getParent().setStyle('display', 'none');
				});
			});
		}
	},

	initTabs: function(selected) {
		var tabs = document.body.getElements('li [data-toggle=tab]') || undefined;
		var self = this;
		if (tabs) {
			tabs.each(function(el) {
				el.addEvent('click', function(e) {
					e.preventDefault();
					self.setActiveTab(el, tabs);
				});
				if (selected && el.get('href').indexOf(selected) != -1)
					self.setActiveTab(el, tabs);
				var dropdown = el.getParent('.dropdown');
				if (dropdown) dropdown.addClass('active');
			});
		}
	},

	setActiveTab: function(tab, tabs) {
		if (typeOf(tab) == undefined) return;
		else if (typeOf(tab) == 'string')
			tab = document.body.getElement('a[data-toggle=tab][href$='+tab+']');
		var tabName = tab.get('href').replace('#', '');
		var pane = document.body.getElements('div.tab-pane') || undefined;
		if (pane) {
			pane.each(function(el) {
				if (el.get('id') == tabName) {
					tab.getParent('li').addClass('active');
					el.setStyle('display', 'block');
				} else {
					el.setStyle('display', 'none');
				}
				var dropdown = el.getParent('.dropdown');
				if (dropdown) dropdown.addClass('active');
			});

			var t = tabs || document.body.getElements('li [data-toggle=tab]');
			t.each(function(el) {
				if (el.get('href').replace('#', '') != tabName) {
					el.getParent('li').removeClass('active');
				}
			});
		}
	}
}
