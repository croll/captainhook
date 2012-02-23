//  -*- mode:js; tab-width:2; c-basic-offset:2; -*-
var Perm = new Class({
	Implements: [Options,Events],
	options: {
		updateElement: 'user_content',
		paginate: 'true',
		onComplete: null
	},
	initialize: function(options) {
		this.setOptions(options);
	},	
	delPerm: function(param) {
		new CaptainHook.Message.Ask({
			element: 'permissions',
			onConfirm: function() {
				new Request.JSON({
					'url': '/ajax/call/user/deletePerm',
					'onSuccess': function(resJSON, resText) {
							
						CaptainHook.Message.show('Permission deleted!' , 'OK', { width: 320 });
						myuser.reloadActiveTab('user-tabs');
						$$('div.modal').hide();
					}
				}).get({'rid': param});
			}
		});
	},
	delAssign: function(rid,gid) {
		new CaptainHook.Message.Ask({
			element: 'permissions',
			onConfirm: function() {
				new Request.JSON({
				'url': '/ajax/call/user/deleteGroupPerm',
				'onSuccess': function(resJSON, resText) {
					gmod.hide();
					$$('div.modal').hide();
					CaptainHook.Message.show('Permission group assignation removed !' , 'OK', { width: 320 });
					myuser.reloadActiveTab('user-tabs');
				}
				}).get({'rid': rid, 'gid': gid});
			}
		});
	},
	savePerm: function() {
		var myForm = $('perm_edit');
		new Request.JSON({
			'url': '/ajax/call/user/savePerm',
			'onSuccess': function(resJSON, resText) {
				pmod.hide();
				CaptainHook.Message.show('Permission saved!' , 'OK', { width: 320 });
				myuser.reloadActiveTab('user-tabs');
			}
		}).send(myForm);
	},
	savePermGroups: function(rid) {
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
				'url': '/ajax/call/user/savePermGroups',
				'onSuccess': function(resJSON, resText) {
					pmod.hide();
					CaptainHook.Message.show('Permission group assignation saved !' , 'OK', { width: 320 });
					myuser.reloadActiveTab('user-tabs');
				}
			}).get({'rid': rid , 'groups' : params});
		}
	},
	permList: function() {
		var userList = new Request.JSON({
                	'url': '/ajax/call/user/permList',
                        'onSuccess': function(resJSON, resText) {
				var button ='<span class="float"><a class="btn" href="#" onclick="myperm.setPerm();"><i class="icon-edit"></i>Add permission</a></span>';
				var list = new CHTable({
					rows: resJSON, 
					thHide: 'rid',
					thLabel: 'Id, Name, Description',
					id: 'rid',
					actions: 'perm,myperm.userPerms,icon-perm|groups, mygroup.userGroups,icon-group|edit,myuser.getUser,icon-edit|del,myuser.delUser,icon-remove'
				});
				pmod.setTitle(button + '<h4>Permissions</h4>');
				pmod.setBody(list.get('html'));
				pmod.setFooter('');
				pmod.show();
                        }
                }).send();
	},
	userPerms: function(uid) {
		var perms = new Request.JSON({
			'url': '/ajax/call/user/userPerms',
			'onSuccess': function(resJSON, resText) {
				var list = new CHTable({
					rows: resJSON, 
					thHide: 'rid',
					thLabel: 'Id,Permission name,Description'
				});
				pmod.setTitle('<h4>User granted permissions<</h4>');
				pmod.setBody(list.get('html'));
				pmod.setFooter('');
				pmod.show();
			}
		}).get({'uid': uid});
	},
	addPerm: function (gid) {
		var list = new Request.JSON({
			'url': '/ajax/call/user/selectPerm',
			'onSuccess': function(resJSON, resText) {
			 	myperm.assignPerm(resJSON, gid);
			}
		}).get({'gid': gid});

	},
	assignPermGroups: function(groups, rid) {
		var myContainer = new Element('div');
		var myList = new Element('ul', {'id': 'agroup_edit'});
		groups.each(function(value, key) {
			
			var myRow = new Element('li', {'class': 'row select link clearfix'});
			var myGroup = new Element('div', {'html': '<h3>'+value.name+'</h3><span>'});
			myRow.adopt(myGroup);	
			myList.adopt(myRow);	
		});
		myContainer.adopt(myList);
		pmod.setTitle('<h4>Assign permission to groups</h4>');
                pmod.setBody(myContainer.get('html'));
                pmod.setFooter('<a class="btn primary" onclick="myperm.savePermGroups('+rid+')">Save</a>');
                var myvar = $('agroup_edit').getChildren('li');
		myvar.each(function(el) {
			el.addEvents({
    				click: function(){
					el.toggleClass('checked').tween('background-color','#87C6DB','#FFF');
				}
			});
		});
	 	pmod.show();
	},
	assignPerm: function(perms, gid) {
		var myContainer = new Element('div');
		var myList = new Element('ul', {'id': 'aperm_edit'});
		perms.each(function(value, key) {
			
			var myRow = new Element('li', {'class': 'row select link clearfix'});
			var myPerm = new Element('div', {'html': '<h3>'+value.name+'</h3><span>'+ value.description +'</span>'});
			myRow.adopt(myPerm);	
			myList.adopt(myRow);	
		});
		myContainer.adopt(myList);
		pmod.setTitle('<h4>Assign new permissions to group</h4>');
                pmod.setBody(myContainer.get('html'));
                pmod.setFooter('<a class="btn primary" onclick="mygroup.saveGroupPerms('+gid+')">Save</a>');
                var myvar = $('aperm_edit').getChildren('li');
		myvar.each(function(el) {
			el.addEvents({
    				click: function(){
					el.toggleClass('checked').tween('background-color','#87C6DB','#FFF');
				}
			});
		});
	 	pmod.show();
	},
	getPerm: function(rid) {
		var perm = new Request.JSON({
			'url': '/ajax/call/user/getPerm',
			'onSuccess': function(resJSON, resText) {
			 	myperm.setPerm(resJSON, rid);
			}
		}).get({'rid': rid});
	},
	setPerm: function(resJSON, rid) {
		var myContainer = new Element('div');
		var myForm = new Element('form', {'id': 'perm_edit'});
		var mynRow = new Element('div', {'class': 'row'});
		var mynLabel = new Element('label', {'for': 'name', 'html': 'Permission name'});
		var myName = new Element('input', {'type': 'text', 'name': 'name', 'class': 'medium', 'value': name});
		var myRid = new Element('input', {'type': 'hidden', 'name': 'rid', 'value': 0});
		var myaRow = new Element('div', {'class': 'row span7'});
		var myDesc = new Element('textarea', {'name': 'description', 'col': 3, 'rows': 5, 'class': 'span7'});
		mynRow.adopt(mynLabel);
	 	mynRow.adopt(myName);
	 	mynRow.adopt(myRid);
		myForm.adopt(mynRow);
		myaRow.adopt(myDesc);
		myForm.adopt(myaRow);
		myContainer.adopt(myForm);
		pmod.setTitle('<h4>Add/Edit permissions</h4>');
                pmod.setBody(myContainer.get('html'));
                pmod.setFooter('<a class="btn primary" onclick="myperm.savePerm()">Save</a>');
		if (resJSON) {
			var keys = Object.keys(resJSON[0]);
			var values = Object.values(resJSON[0]);
			$('perm_edit').getChildren('div.row input').each(function(item) {
				if (item.get('name') == 'name') item.setProperty('value', values[1]);
				if (item.get('name') == 'rid') { 
					item.set('value', rid);
				}
			});
			$('perm_edit').getChildren('div.row textarea').each(function(item) {
					item.set('html', values[2]);
			});
		}
		pmod.show();	
	}
});
