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

			var msg = new Message(Object.merge({
				iconPath: '/mod/cssjs/images/message/',
				icon: icon,
				title: 'Information',
				message: txt,
				offset: 150,
				centered: true,
				width: 360,
				top: true,
				stack: true 
			}, options)).say();
		} 

}

window.addEvent('domready', function(){
	if ($('message') && $('message').get('html') != '') {
		CaptainHook.Message.show($('message').get('html'), $('message').get('rel'), {width: 320});
		$('message').set('html', '');
	}
});
