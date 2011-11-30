var CHCore =new Class({

	Implements: [Events, Options],

		hooks: {},
		jsStack: {},

		initialize: function() {
			this.fillJsStack();
		},

		registerHookListener: function(name, func) {
			if (typeOf(this.hooks[name]) != 'array')
				this.hooks[name] = [];
			this.hooks[name].include(func);
		},

		callHookListener: function(name) {
			if (typeOf(this.hooks[name]) != 'array' || this.hooks[name].length < 1)
				return false;
			this.hooks[name].each(function(f) {
				f.call();
			});
		},

		callPHP: function(mod, method, params, onSuccess) {
			var req = {url: '/ajax/call/'+mod+'/'+method};
			if (typeOf(onSuccess) == 'function')
				Object.append(req, {onSuccess: onSuccess});
			new Request.JSON(req).post(params);
		},

		fillJsStack: function() {
			document.getElements('script').get('src').each(function(src) {
				keys = Object.keys(this.jsStack);
				if (!keys || !keys.contains(name))
					this.addJsFile(src);
			}, this);
		},

		addJsFile: function(src) {
			if (typeOf(src) != 'string') return;
			var name = src.split('/').getLast().split('.')[0].camelCase();
			this.jsStack[name] = src;
		},

		addJs: function(options) {
			if (this.jsStack[options.script]) {
				this.callHookListener(options.hook);
				return;
			}
			this.callPHP('cssjs', 'getScriptFiles', {mod: options.mod, class: options.script}, function(response) {
				if (response == -1) {
					return false;
				} else {
					this.jsStack[options.name] = 'embedded';
					if (response.js)
						new Element('script').set('html', response.js).inject(document.head);
					if (response.css) {
						new Element('style', {type: 'text/css'}).set('html', response.css).inject(document.head);
					}
					this.callHookListener(options.hook);
				}}.bind(this)
			);
		}

});

var CaptainHook;

window.addEvent('domready', function() {
	CaptainHook = new CHCore();
});
