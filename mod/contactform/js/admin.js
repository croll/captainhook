CaptainHook.Contact.Admin = {

	update: function() {
		var key = $('apikey').value;
		var privateKey = $('privateKey').value;
		var mailSubject = $('mailSubject').value;
		var sender = $('sender').value;
		if (key == '' || privateKey == '' || mailSubject == '') {
			CaptainHook.Message.show('Please fill all infos.', 'ERROR');
			return;
		}
		new Request.JSON({
				'url': '/ajax/call/contactform/update',
				'onSuccess': function(res) {
					if (res == false) {
						CaptainHook.Message.show('Unable to change configuration', 'ERROR');
					} else {
						CaptainHook.Message.show('Configuration updated', 'OK');
					}
				},
				'onFailure' : function(res) {
					CaptainHook.Message.show('Unable to change configuration', 'ERROR');
				}
		}).post({'key': key, 'privateKey': privateKey, 'mailSubject': mailSubject, 'sender': sender});
	},

	addCategory: function(el) {
		var name = $('newcategory').value;
		if (name == '') {
			CaptainHook.Message.show('Category name can\'t be empty.', 'WARNING');
			return;
		}
		new Request.JSON({
				'url': '/ajax/call/contactform/addCategory',
				'onSuccess': function(res) {
					CaptainHook.Message.show('Category created', 'OK');
					var html = $('categories').get('html')+'<fieldset><legend rel="'+name+'">'+name+' [<a href="javascript:void(0)" onclick="CaptainHook.Contact.Admin.deleteCategory(this)">x</a>]</legend><input type="text"><input type="button" value="Add" onclick="CaptainHook.Contact.Admin.addMail(this)"></fieldset>';
					$('categories').set('html', html);
				},
				'onFailure' : function(res) {
					CaptainHook.Message.show('Unable to add category', 'ERROR');
				}
		}).post({'category': name});
	},

	deleteCategory: function(el) {
		var category = el.getParent('legend').get('rel');
		new Request.JSON({
				'url': '/ajax/call/contactform/deleteCategory',
				'onSuccess': function(res) {
					CaptainHook.Message.show('Category deleted', 'OK');
					el.getParent('fieldset').dispose();
				},
				'onFailure' : function(res) {
					CaptainHook.Message.show('Unable to delete category', 'ERROR');
				}
		}).post({'category': category});
	},

	addMail: function(el) {
		var address = el.getPrevious('input[type=text]').get('value');	
		var category = el.getParent('fieldset').getElement('legend').get('rel');
		if (address == '') {
			CaptainHook.Message.show('Mail address can\'t be empty.', 'WARNING');
			return;
		}
		new Request.JSON({
				'url': '/ajax/call/contactform/addMail',
				'onSuccess': function(res) {
					CaptainHook.Message.show('Mail address added', 'OK');
					var html = '<div><span>'+address+'</span><input type="button" onclick="CaptainHook.Contact.Admin.deleteMail(this)" value="Del" /></div>';
					var container = el.getPrevious('div[class=mailList]');
					if (container == null) {
						container = new Element('div').addClass('mailList').inject(el.getPrevious('legend'), 'after');
					}
					container.set('html', container.get('html')+html);
					el.getPrevious('input[type=text]').set('value', '');
				},
				'onFailure' : function(res) {
					CaptainHook.Message.show('Unable to add mail address', 'ERROR');
				}
		}).post({'category': category, 'mail': address});
	},

	deleteMail: function(el) {
		var address = el.getPrevious('span').get('html');	
		var category = el.getParent('fieldset').getElement('legend').get('rel');
		new Request.JSON({
				'url': '/ajax/call/contactform/deleteMail',
				'onSuccess': function(res) {
					CaptainHook.Message.show('Mail address deleted', 'OK');
					el.getParent('div').dispose();
				},
				'onFailure' : function(res) {
					CaptainHook.Message.show('Unable to add mail address', 'ERROR');
				}
		}).post({'category': category, 'mail': address});
	}
}
