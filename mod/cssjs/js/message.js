CaptainHook.Message = {
		show: function(msg, messageType, wait) {
			var container = document.id('message');
			var w;
			var self = this;
			if (!wait && wait != 0) w = 3000;
				else w = wait;
			
			if (typeOf(container) != 'element') {
				alert("Le div 'message' n'existe pas");
				return false;
			}
			var mClass;
			switch(messageType || null) {
			  case 'WARNING':
					mClass='.messageWarning';	
					break;
			  case 'ERROR':
					mClass='.messageError';	
					break;
				default:
					mClass='.messageOk';	
					break;
			}
			container.empty().set('html', msg);
			container.erase('class').addClass(mClass);
			
			var myEffect = new Fx.Morph(container, {
					duration: 500,
					link: 'chain',
					transition: Fx.Transitions.Sine.easeOut
			});
			var myEffectHide = new Fx.Morph(container, {
					duration: 500,
					link: 'chain',
					transition: Fx.Transitions.Sine.easeOut,
					onComplete: function() {
						self.hide.delay(w);
					}
			});
			if (wait == 0)
				myEffect.start(mClass);
			else
				myEffectHide.start(mClass);
		}, 

		hide: function() {
			var container = document.id('message');
			var myEffect = new Fx.Morph(container, {
					duration: 500,
					link: 'chain',
					transition: Fx.Transitions.Sine.easeOut,
					onComplete: function() {
						container.empty().set('html', '');
					}
			});
			myEffect.start('.messageHide');
		}
}

window.addEvent('domready', function(){
	if ($('message') && $('message').get('html') != '')
		CaptainHook.Message.show($('message').get('html'), $('message').get('rel'), 0);
});
