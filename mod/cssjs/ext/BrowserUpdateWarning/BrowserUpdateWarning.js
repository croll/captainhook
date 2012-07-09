/*
---

name: BrowserUpdateWarning

description: Browser Version Check and Warn

version: 1.1.2

license: MIT-style license

authors:
  - Andy Fleming
  
requires: [Core/Class, Core/Object, Core/Element.Event]

provides: [BrowserUpdateWarning]

...
*/

//##################################################################################################################
//	BrowserUpdateWarning Class
//##################################################################################################################

	var BrowserUpdateWarning = new Class({
	
		Implements: [Options, Events],
	
	// ------------------------------------------------------------------------------------
	//	Options
	// ------------------------------------------------------------------------------------
	
		options: {
			
			showPopup: true,
			
			// Allow user to close prompt and continue to website
			allowContinue: false,
			
			imagesDirectory: 'images/',
			
			shade: true,
			opacity: 88,
			
			// Minimum Versions
			minVersion_ie: 7,
			minVersion_safari: 5,
			minVersion_firefox: 5,
			minVersion_chrome: 15,
			minVersion_opera: 10,
			
			// Update Links
			updateLink_ie: 'http://windows.microsoft.com/en-US/internet-explorer/downloads/ie',
			updateLink_safari: 'http://www.apple.com/safari/download/',
			updateLink_firefox: 'http://getfirefox.com/',
			updateLink_chrome: 'https://www.google.com/chrome',
			updateLink_opera: 'http://www.opera.com/download/',

			
			// A list of update options to show to the user
			downloadOptions: ['ie','safari','firefox','chrome','opera']
			
			// Events
			//onFailure: $empty
			//onSuccess: $empty
				
		},
	
	// ------------------------------------------------------------------------------------
	//	Initialize
	// ------------------------------------------------------------------------------------
		
		// Init
		initialize: function (options) {
			this.setOptions(options);
		},
	
	// ------------------------------------------------------------------------------------
	//	isGoogleInstantPreview()
	//		Helper method (to detect Google Instant Preview in Web Search)
	// ------------------------------------------------------------------------------------
	
		isGoogleInstantPreview: function () {
			return (navigator.userAgent.match(/Google Web Preview/i) != null);
		},
	
	// ------------------------------------------------------------------------------------
	//	Check (and warn if necessary)
	// ------------------------------------------------------------------------------------
		
		check: function() {
			
			var self = this;
			
			// If the minimum browser 
			if (Browser.version < self.options['minVersion_'+Browser.name] && !self.isGoogleInstantPreview()) {
				self.fireEvent('onFailure');
				if (self.options.showPopup) { self.showBrowserUpdateWarning(); }
			} else {
				self.fireEvent('onSuccess');
			}
			
			return;
							
		},
		
	
	// ------------------------------------------------------------------------------------
	//	showBrowserUpdateWarning
	//		Shows warning; called in check()
	// ------------------------------------------------------------------------------------
	
		showBrowserUpdateWarning: function() {

			var self = this;

			// If the shade is turned on, show it
			if (self.options.shade) { self.showShade(); }

			var updateLink = self.options['updateLink_'+Browser.name];
			
			html  = '<div id="BrowserUpdateWarningContent">';
				html += '<h1>'+ch_t('cssjs', "It\'s time to upgrade your browser.")+'</h1>';
				html += '<div class="yourBrowser">';
					html += '<img src="'+self.options.imagesDirectory+'icon-'+Browser.name+'.png" />';
					html += '<a href="'+updateLink+'" target="_blank">'+ch_t('cssjs', "Click here to update your current browser.")+'</a>';
				html += '</div>';
				html += '<div class="otherBrowsers">';
					this.options.downloadOptions.each(function(key,index) {
						if (key != Browser.name) {
							html += '<a href="'+self.options['updateLink_'+key]+'">';
							html += '<img src="'+self.options.imagesDirectory+'icon-'+key+'.png" />';
							html += ch_t('cssjs', 'Download ');
							if (key == 'ie') {
								html += 'Internet Explorer';
							} else {
								html += key.substring(0,1).toUpperCase() + key.substring(1);
							}
							
							
							html += ' &raquo;</a>';
						}
					});
				html += '</div>';
				html += '<div class="whyUpgrade">';
					html += '<h2>'+ch_t('cssjs', "Why should I upgrade?")+'</h2>';
					html += '<ul>';
						html += '<li>'+ch_t('cssjs', "Websites load faster")+'</li>';
						html += '<li>'+ch_t('cssjs', "Websites render correctly")+'</li>';
						html += '<li>'+ch_t('cssjs', "Safer browsing")+'</li>';
						html += '<li>'+ch_t('cssjs', "Other great features")+'</li>';
					html += '</ul>';
				html += '</div>';
				if (self.options.allowContinue) {
					html += '<a href="javascript:void(0);" class="continueToSite" onclick="';
					html += '$(\'BrowserUpdateWarningWrapper\').setStyle(\'display\',\'none\');';
					if (self.options.shade) html += '$(\'BrowserUpdateWarningShade\').setStyle(\'display\',\'none\');';
					html += '">'+ch_t('cssjs', "Continue to Site.")+'</a>';
				}
				html += '<div style="clear:both"></div>';
			
			
			
			html += '</div>';

			// Create DIV element and inject into body
			var div  = new Element('div', {id: 'BrowserUpdateWarningWrapper'});
			div.set('html',html).inject(document.body);
						
		},

	// ------------------------------------------------------------------------------------
	//	showShade
	// ------------------------------------------------------------------------------------
	
		showShade: function() {
			
			var opacity = this.options.opacity;
			
			// Create shade and append to body
			var shade = new Element('div',{id: 'BrowserUpdateWarningShade'});			
			shade.setStyle('opacity',(opacity/100));
			shade.setStyle('-ms-filter','"progid:DXImageTransform.Microsoft.Alpha(Opacity='+opacity+')"');
			shade.setStyle('filter','alpha(opacity = '+opacity+')');
			
			shade.inject(document.body);
		
		}
	


	}); // End of Class: BrowserUpdateWarning
	
	
	
	
	
	
	
