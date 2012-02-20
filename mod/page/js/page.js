//  -*- mode:js; tab-width:2; c-basic-offset:2; -*-
var Page = new Class({
		Implements: [Options,Events],
		options: {
			updateElement: 'page_content',
			paginate: 'true',
			onComplete: null
		},
		initialize: function(options) {
			this.setOptions(options);
		},
		delPage: function(param) {
			new Request.JSON({
				'url': '/ajax/call/page/deletePage',
				'onSuccess': function(resJSON, resText) {
					window.location.replace("../list/");
				}
			}).get({'pid': param});
		},
		postForm: function(url,formElements,params) {
			
			var myreq = new Request.JSON({
				'url': url,
				'onSuccess': function(resJSON, resText) {
					// redirect to page list 
					window.location.replace("../list/");
				}
			}).send(params);
		}
});
