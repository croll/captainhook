CaptainHook.Message = {
		show: function(txt, messageType, options) {
			var icon;
			switch(messageType) {
				case 'OK':
					icon = 'okMedium.png';
				break;
				case 'ERROR':
					icon = 'errorMedium.png';
				break;
				default:
					icon = 'cautionMedium.png';
			}
			
			if (options == undefined) options = {};
			if (options.duration == undefined) {
				options.duration = Math.floor(2000+(((txt.length-35)/35)*2000));
			}
			var msg = new Message(Object.merge({
				iconPath: '/mod/cssjs/images/message/',
				icon: icon,
				title: 'Information',
				message: txt,
				offset: 150,
				centered: true,
				width: 480,
				top: true,
				stack: true 
			}, options)).say();
		} 

}

CaptainHook.Message.Ask = new Class({
	
	Implements: [Events, Options],

	options: {
		element: null,
		title: 'Confirmation',
		message: 'Sure ?',
		labelOk: 'OK',
		labelCancel: 'Cancel',
		onConfirm: null,
		popupOptions: {
			persist: true,
			closeOnClickOut: true,
			closeOnEsc: true,
			mask: true,
			animale: true
		}
	},

	initialize: function(options) {
		this.builded = false;
		this.setOptions(options);
		this.el = (typeof(this.options.element) == 'element') ? this.options.element : $(this.options.element); 
		this.popupOptions = this.options.popupOptions;
		this.el.addEvent('click', function(e) {
				e.stop();
				this.buildDialog();
				this.popup = new Bootstrap.Popup(this.dialog, this.popupOptions);
		}.bind(this));
	},

	buildDialog: function() {
		this.dialog = new Element('div').addClass('modal');
		var header = new Element('div').addClass('modal-header').adopt(new Element('a').addClass('close')).adopt(new Element('h3').set('text', this.options.title));
		var body = new Element('div').addClass('modal-body').adopt(new Element('div').set('html', this.options.message));
		var footer = new Element('div').addClass('modal-footer');
		var ok = new Element('a').addClass('btn primary').set('text', this.options.labelOk).addEvent('click', function() {
					this.fireEvent('confirm');
					this.popup.hide();
				}.bind(this)).inject(footer);
		var cancel = new Element('a').addClass('btn').set('text', this.options.labelCancel).addEvent('click', function() {
					this.fireEvent('cancel');
					this.popup.hide();
				}.bind(this)).inject(footer);
		this.dialog.adopt(header).adopt(body).adopt(footer).inject(document.body);
  }
});
