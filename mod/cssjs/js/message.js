var msgCurrentlyDisplayed = [];

CaptainHook.Message = {
	show: function(txt, messageType, options) {
		var icon;
		switch (messageType) {
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
			options.duration = Math.floor(2000 + (((txt.length - 35) / 35) * 200));
		}
		if (!msgCurrentlyDisplayed.contains(txt)) {
			msgCurrentlyDisplayed.push(txt);
			var msg = new Message(Object.merge({
				iconPath: '/mod/cssjs/images/message/',
				icon: icon,
				title: 'Information',
				message: txt,
				offset: 50,
				centered: true,
				width: 480,
				top: true,
				stack: true,
				onHide: function() {
					var mm = [];
					msgCurrentlyDisplayed.each(function(m) {
						if (m != txt) {
							mm.push(m);
						}
					});
					msgCurrentlyDisplayed = mm;
				}
			}, options))
				.say();
		}
	}

}

CaptainHook.DialogConfirm = {};

CaptainHook.DialogConfirm.dialog = null;

CaptainHook.DialogConfirm.show = function(el, options) {
	options = Object.merge({
		element: null,
		title: 'Confirmation',
		message: 'Sure ?',
		labelOk: 'OK',
		labelCancel: 'Cancel',
		onConfirm: null
	}, options);
	el = (typeof el == 'string') ? $(el) : el;
	el.addEvent('click', function(e) {
		e.stop();
		CaptainHook.DialogConfirm.buildDialog(options);
	});
};

CaptainHook.DialogConfirm.buildDialog = function(options) {
	if (!CaptainHook.DialogConfirm.dialog) {
		CaptainHook.DialogConfirm.dialog = new Modal.Base(document.body);

	}
	CaptainHook.DialogConfirm.dialog.setTitle(options.title);
	CaptainHook.DialogConfirm.dialog.setBody(options.message);
	if ($('#footerHack')) {
		$('#footerHack').destroy();
	}
	CaptainHook.DialogConfirm.dialog.setFooter('<div id="footerHack"></div>');
	var footer = new Element('div');
	var a = new Element('a')
		.addClass('btn btn-primary')
		.set('text', options.labelOk)
		.addEvent('click', function(e) {
			e.preventDefault();
			if (typeof options.onConfirm == 'function') options.onConfirm();
			CaptainHook.DialogConfirm.dialog.fireEvent('confirm');
			CaptainHook.DialogConfirm.dialog.hide();
		})
		.inject(footer);

	new Element('a')
		.addClass('btn')
		.set('text', options.labelCancel)
		.addEvent('click', function() {
			CaptainHook.DialogConfirm.dialog.fireEvent('cancel');
			CaptainHook.DialogConfirm.dialog.hide();
		})
		.inject(footer);	
		footer.inject($('footerHack'));

	CaptainHook.DialogConfirm.dialog.show();
};