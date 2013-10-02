//  -*- mode:js; tab-width:2; c-basic-offset:2; -*-
var User = new Class({
	Implements: [Options,Events],
	options: {
		updateElement: 'user_content',
		paginate: 'true',
		onComplete: null
	},
	initialize: function(options) {
		this.setOptions(options);
	},
	reloadActiveTab: function(tabElement) {
		
		$(tabElement).getChildren('ul.tabs li.active a').each(function(el) {
			var url = el.getProperty('href');
			var updateElement = el.getChildren('span').get('html');
			updateElement = updateElement[0].toLowerCase();
			var c = new Element('div');

			// get Tabs parameters
			if (el.getProperty('data-btn')) {
				var btnLabel= el.getProperty('data-btn');
				var btnClick= el.getProperty('data-bclick');
				var btnIcon= el.getProperty('data-bicon');
				var btn = new Element('span', {'class':'float', html: '<a onclick="'+ btnClick +'" class="btn" href="#"><i class="'+ btnIcon +'"></i>'+ btnLabel +'</a>'});
				c.adopt(btn);

			}
			
			if (el.getProperty('data-actions')) {
				var action = el.getProperty('data-actions');
			}
			if (el.getProperty('data-id')) {
				var aid = el.getProperty('data-id');
			}
			if (el.getProperty('data-cid')) {
				var acid = el.getProperty('data-cid');
			}
			if (el.getProperty('data-update')) {
				updateElement = el.getProperty('data-update');
			}
			if (el.getProperty('data-title')) {
				var title= new Element('h3', {'html': el.getProperty('data-title')});
				c.adopt(title);
			}

			if (url && el.getProperty('data-update')) {
				var tab = new Request.JSON({
					'url': url,
					'onSuccess': function(resJSON, resText) {
						var list = new CHTable({
							rows: resJSON,
							actions: action,
							id: aid,
							cid: acid
						});
						c.adopt(list);
						var ew = $(updateElement).empty().set('html', c.get('html'));
					}
				}).send();
			}

		});
},
setTabs: function(tabElement) {
	var myTabPane = new TabPane(tabElement);
	$(tabElement).getChildren('ul.tabs li a').each(function(el) {
		el.addEvent('click', function(event) {
			event.preventDefault();
			var url = this.getProperty('href');
			var updateElement = this.getChildren('span').get('html');
			updateElement = updateElement[0].toLowerCase();
			var c = new Element('div');

			// get Tabs parameters
			if (this.getProperty('data-btn')) {
				var btnLabel= this.getProperty('data-btn');
				var btnClick= this.getProperty('data-bclick');
				var btnIcon= this.getProperty('data-bicon');
				var btn = new Element('span', {'class':'float', html: '<a onclick="'+ btnClick +'" class="btn" href="#"><i class="'+ btnIcon +'"></i>'+ btnLabel +'</a>'});
				c.adopt(btn);

			}
			
			if (this.getProperty('data-actions')) {
				var action = this.getProperty('data-actions');
			}

			if (this.getProperty('data-id')) {
				var aid = this.getProperty('data-id');
			}
			if (this.getProperty('data-cid')) {
				var acid = this.getProperty('data-cid');
			}
			if (this.getProperty('data-update')) {
				updateElement = this.getProperty('data-update');
			}
			if (this.getProperty('data-title')) {
				var title= new Element('h3', {'html': this.getProperty('data-title')});
				c.adopt(title);
			}

			if (url && this.getProperty('data-update')) {
				var tab = new Request.JSON({
					'url': url,
					'onSuccess': function(resJSON, resText) {
						var list = new CHTable({
							rows: resJSON,
							actions: action,
							id: aid,
							cid: acid
						});
						c.adopt(list);
						var ew = $(updateElement).empty().set('html', c.get('html'));
					}
				}).send();
			} else if (url){
				window.location.replace(url);
			}
		});
});
},
getUser: function(uid) {
	var user = new Request.JSON({
		'url': '/ajax/call/user/getUser',
		'onSuccess': function(resJSON, resText) {
			myuser.setUser(resJSON, uid);
		}
	}).get({'uid': uid});
},
setUser: function(resJSON,uid) {
	var myContainer = new Element('div');
	var myForm = new Element('form', {'id': 'user_edit'});
	var mynRow = new Element('div', {'class': 'row'});
	var mynLabel = new Element('label', {'for': 'login', 'html': 'User login'});
	var mynName = new Element('input', {'type': 'text', 'name': 'login'});
	mynRow.adopt(mynLabel);
	mynRow.adopt(mynName);
	myForm.adopt(mynRow);

	var myfnRow = new Element('div', {'class': 'row'});
	var myfnLabel = new Element('label', {'for': 'full_name', 'html': 'User full name'});
	var myfnName = new Element('input', {'type': 'text', 'name': 'full_name', 'class': 'large'});
	myfnRow.adopt(myfnLabel);
	myfnRow.adopt(myfnName);
	myForm.adopt(myfnRow);


	var myemRow = new Element('div', {'class': 'row'});
	var myemLabel = new Element('label', {'for': 'email', 'html': 'User email'});
	var myemName = new Element('input', {'type': 'text', 'name': 'email', 'class': 'medium'});
	myemRow.adopt(myemLabel);
	myemRow.adopt(myemName);
	myForm.adopt(myemRow);


	var myUid = new Element('input', {'name': 'uid', 'type': 'hidden', 'value': 0});
	var myaRow = new Element('div', {'class': 'link row'});
	var myaLabel = new Element('label', {'for': 'active', 'html': 'User is active'});
	var myActive = new Element('input', {'name': 'active', 'type': 'checkbox'});
	myaRow.adopt(myaLabel);
	myaRow.adopt(myActive);
	myaRow.adopt(myUid);
	myForm.adopt(myaRow);

	var mypwRow = new Element('div', {'class': 'row'});
	var mypwLabel = new Element('label', {'for': 'password', 'html': 'User password'});
	var mypwName = new Element('input', {'type': 'password', 'name': 'password', 'value' : '', 'class': 'medium'});
	mypwRow.adopt(mypwLabel);
	mypwRow.adopt(mypwName);
	myForm.adopt(mypwRow);

	var mywpRow = new Element('div', {'class': 'row'});
	var mywpLabel = new Element('label', {'for': 'password2', 'html': 'Retype password'});
	var mywpName = new Element('input', {'type': 'password', 'name': 'password2', 'value' : '', 'class': 'medium'});
	mywpRow.adopt(mywpLabel);
	mywpRow.adopt(mywpName);
	myForm.adopt(mywpRow);
	myContainer.adopt(myForm);
	umod.setTitle("<h1>Add user</h1>");
	umod.setBody(myContainer.get('html'));
		// assign var in edit mode
		if (resJSON) {
			$('user_edit').getChildren('div input').each(function(item) {
				if (item.get('name') == 'login') {
					item.set('value', resJSON.login);
				}
				if (item.get('name') == 'uid') {
					item.set('value', resJSON.uid);
				}
				if (item.get('name') == 'full_name') item.set('value', resJSON.full_name);
				if (item.get('name') == 'email') item.set('value', resJSON.email);
				if (item.get('name') == 'active') {
					item.set('value', resJSON.status);
					if (resJSON.status == 1) item.set('checked', 'checked');
					item.addEvent('click', function(event) {
						if (item.get('value') === 0) {
							item.set('value', 1);
							item.set('checked', 'checked');
						} else {
							item.set('value', 0);
							item.removeProperty('checked');
						}
					});
				}
			});
		}
		umod.setFooter('<a class="btn primary" onclick="myuser.saveUser()">Save</a>');
		umod.show();
	},
	userList: function() {
		var userList = new Request.JSON({
			'url': '/ajax/call/user/userList',
			'onSuccess': function(resJSON, resText) {
				var button ='<span class="float"><a class="btn" href="#" onclick="myuser.setUser();"><i class="icon-edit"></i>Add user</a></span>';
				var list = new CHTable({
					rows: resJSON,
					thHide: 'uid',
					thLabel: 'Id, Login, Full name',
					id: 'gid',
					actions: 'perm,myperm.userPerms,icon-perm|groups, mygroup.userGroups,icon-group|edit,myuser.getUser,icon-edit|del,myuser.delUser,icon-remove'
				});
				umod.setTitle(button+'<h4>Add user</h4>');
				umod.setBody(list.get('html'));
				umod.setFooter('');
				umod.show();
			}
		}).send();
	},
	saveUserGroups: function(uid) {
		var myvar = $('agroup_edit').getChildren('li');
		var params = '';
		myvar.each(function(el) {
			if (el.hasClass('checked'))  {
				var f = el.getChildren('div h3').get('html');
				params += f[0]+',';
			}
		}.bind(this));
		if (params.length >1) {
			new Request.JSON({
				'url': '/ajax/call/user/saveUserGroups',
				'onSuccess': function(resJSON, resText) {
					CaptainHook.Message.show('Membership saved!' , 'icon-user', { width: 320 });
					myuser.reloadActiveTab('user-tabs');
				}
			}).get({'uid': uid , 'groups' : params});
		}
	},
	saveUser: function() {
		var myForm = $('user_edit');
		new Request.JSON({
			'url': '/ajax/call/user/saveUser',
			'onSuccess': function(resJSON, resText) {
				umod.hide();
				CaptainHook.Message.show('User saved!' , 'icon-user', { width: 320 });
				myuser.reloadActiveTab('user-tabs');
			}
		}).send(myForm);
	},
	delUser: function(param) {
		CaptainHook.DialogConfirm.show('users', {
			onConfirm: function() {
				new Request.JSON({
					'url': '/ajax/call/user/deleteUser',
					'onSuccess': function(resJSON, resText) {
						$$('div.modal').destroy();
						CaptainHook.Message.show('User deleted!' , 'icon-ok', { width: 320 });
						myuser.reloadActiveTab('user-tabs');
					}
				}).get({'uid': param});
			}
		});
	},
	postForm: function(url,formElements,params) {
		var myreq = new Request.JSON({
			'url': url,
			'onSuccess': function(resJSON, resText) {
				// redirect to user list
				window.location.replace("../user/");
			}
		}).send(params);
	}
});
