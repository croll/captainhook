//  -*- mode:js; tab-width:2; c-basic-offset:2; -*-
var Group = new Class({
	Implements: [Options,Events],
	options: {
		updateElement: 'user_content',
		paginate: 'true',
		onComplete: null
	},
	initialize: function(options) {
		this.setOptions(options);
	},
	getGroup: function(gid) {
		var group = new Request.JSON({
			'url': '/ajax/call/user/getGroup',
			'onSuccess': function(resJSON, resText) {
			 	mygroup.setGroup(resJSON.gid, resJSON.name, resJSON.status);
			}
		}).get({'gid': gid});
	},
	assignRight: function(rid, gid) {
		var groups = new Request.JSON({
			'url': '/ajax/call/user/getNotAssignedGroups',
			'onSuccess': function(resJSON, resText) {
				gmod.hide();
			 	myperm.assignPermGroups(resJSON, rid);
			}
		}).get({'gid': gid, 'rid': rid});

	},

	groupList: function(uid) {
		var userList = new Request.JSON({
                	'url': '/ajax/call/user/groupList',
                        'onSuccess': function(resJSON, resText) {
				var button ='<span class="float"><a class="btn" href="#" onclick="mygroup.setGroup();"><i class="icon-edit"></i>Add group</a></span>';
				var list = new CHTable({
					rows: resJSON, 
					thHide: 'uid,gid',
					thLabel: 'Name, Status',
					id: 'gid',
					cid: 'uid',
					actions: 'perm,myperm.userPerms,icon-perm|groups, mygroup.userGroups,icon-group|edit,myuser.getUser,icon-edit|del,myuser.delUser,icon-remove'
				});
				gmod.setTitle(button+'<h4>Groups</h4>');
				gmod.setBody(list.get('html'));
				gmod.setFooter('');
				gmod.show();
                        }
                }).send();
	},
	userGroups: function(uid) {
		var groups = new Request.JSON({
			'url': '/ajax/call/user/userGroups',
			'onSuccess': function(resJSON, resText) {
				var button ='<span class="float"><a class="btn" href="#" onclick="mygroup.groupSubscribe('+uid+');"><i class="icon-edit"></i>Add user to groups</a></span>';
				var list = new CHTable({
					rows: resJSON, 
					thHide: 'uid,gid',
					thLabel: 'Name',
					id: 'gid',
					cid: 'uid',
					actions: 'resign,mygroup.resign,icon-remove,uid',
				});
				gmod.setTitle(button + '<h4>user group\'s membership</h4>');
				gmod.setBody(list.get('html'));
				gmod.setFooter('');
				gmod.show();
			}
		}).get({'uid': uid});
	},
	membersList: function(gid) {
		new Request.JSON({
			'url': '/ajax/call/user/membersList',
			'onSuccess': function(resJSON, resText) {						
				var button ='<span class="float"><a class="btn" href="#" onclick="mygroup.addMembers('+gid+');"><i class="icon-user"></i>Add member</a></span>';
				var list = new CHTable({
					rows: resJSON, 
					thHide: 'uid',
					thLabel: 'Id,Login,Full name, Email, Created, Updated',
					id: 'gid',
					cid: 'uid',
					actions: 'resign, mygroup.resign,icon-remove, uid'
				});
				gmod.setTitle(button + '<h4>Users group membership</h4>');
				gmod.setBody(list.get('html'));
				gmod.setFooter('');
				gmod.show();
			}
		}).get({'gid': gid });

	},
	permGroups: function(rid) {
		new Request.JSON({
			'url': '/ajax/call/user/permGroups',
			'onSuccess': function(resJSON, resText) {						
				var button ='<span class="float"><a class="btn" href="#" onclick="mygroup.assignRight('+rid+');"><i class="icon-perm"></i>Assign to groups</a></span>';
				var list = new CHTable({
					rows: resJSON, 
					thHide: 'rid',
					thLabel: 'Id,Login,Full name, Email, Created, Updated',
					id:'rid',
					cid: 'gid',
					actions: 'resign, myperm.delAssign,icon-remove,gid'
				});
				gmod.setTitle(button + '<h4>Permission group membership</h4>');
				gmod.setBody(list.get('html'));
				gmod.setFooter('');
				gmod.show();
			}
		}).get({'rid': rid });
	},
	groupPerms: function(gid) {
		var perms = new Request.JSON({
			'url': '/ajax/call/user/groupPerms',
			'onSuccess': function(resJSON, resText) {
				var button ='<span class="float"><a class="btn" href="#" onclick="myperm.addPerm('+gid+');"><i class="icon-edit"></i>Add permission to group</a></span>';
				var list = new CHTable({
					rows: resJSON, 
					thHide: 'rid',
					thLabel: 'Id,Permission name,Description',
					rowActions: {'label': 'del', 'func': 'mygroup.delGroupPerm', 'class': 'icon-remove', 'params': {'rid': 'item.rid','gid':gid}}
				});
				gmod.setTitle(button + '<h4>Group granted permissions</h4>');
				gmod.setBody(list.get('html'));
				gmod.setFooter('');
				gmod.show();
			}
		}).get({'gid': gid});
	},
	saveGroup: function() {
		var myForm = $('group_edit');
		new Request.JSON({
			'url': '/ajax/call/user/saveGroup',
			'onSuccess': function(resJSON, resText) {
				gmod.hide();
				CaptainHook.Message.show('Group saved!' , 'icon-ok', { width: 320 });
				//refresh tab;
				myuser.reloadActiveTab('user-tabs');
			}
		}).send(myForm);
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
					gmod.hide();
					CaptainHook.Message.show('Group membership saved!' , 'icon-ok', { width: 320 });
					myuser.reloadActiveTab('user-tabs');
				}
			}).get({'uid': uid , 'groups' : params});
		}
	},
	saveGroupMembership: function(gid) {
		var myvar = $('amember_edit').getChildren('li');
		var params = '';
		myvar.each(function(el) {
			if (el.hasClass('checked'))  {
				var f = el.getChildren('div h3').get('html');
				params += f[0]+',';
			}
		}.bind(this));
		if (params.length >1) {		
			new Request.JSON({
				'url': '/ajax/call/user/saveGroupMembership',
				'onSuccess': function(resJSON, resText) {
					gmod.hide();
					CaptainHook.Message.show('Group membership saved!' , 'icon-ok', { width: 320 });
					myuser.reloadActiveTab('user-tabs');
				}
			}).get({'gid': gid , 'users' : params});
		}
	},
	saveGroupPerms: function(gid) {
		var myvar = $('aperm_edit').getChildren('li');
		var params = '';;
		myvar.each(function(el) {
			if (el.hasClass('checked'))  {
				var f = el.getChildren('div h3').get('html');
				params += f[0]+',';
			}
		}.bind(this));
		if (params.length >1) {		
			new Request.JSON({
				'url': '/ajax/call/user/saveGroupPerms',
				'onSuccess': function(resJSON, resText) {
					pmod.hide();
					gmod.hide();
					CaptainHook.Message.show('Group permission saved!' , 'icon-ok', { width: 320 });
					myuser.reloadActiveTab('user-tabs');
				}
			}).get({'gid': gid , 'perms' : params});
		}
	},
	resign: function(gid, uid) {
		new Request.JSON({
			'url': '/ajax/call/user/resign',
			'onSuccess': function(resJSON, resText) {
				gmod.hide();
				CaptainHook.Message.show('Group membership resigned' , 'icon-ok', { width: 320 });
				myuser.reloadActiveTab('user-tabs');
			}
		}).get({'gid': gid , 'uid' : uid});
	},
	setGroup: function(gid, name, active) {
		var myContainer = new Element('div');
		var myForm = new Element('form', {'id': 'group_edit'});
		var mynRow = new Element('div', {'class': 'clearfix'});
		var mynLabel = new Element('label', {'for': 'name', 'html': 'Group name'});
		var myName = new Element('input', {'type': 'text', 'name': 'name'});
		var myGid = new Element('input', {'name': 'gid', 'type': 'hidden', 'value': 0});
		var myaRow = new Element('div', {'class': 'link clearfix'});
		var myaLabel = new Element('label', {'for': 'active', 'html': 'Group is active'});
		var myActive = new Element('input', {'name': 'active', 'type': 'checkbox', 'value' : active});
	 	mynLabel.adopt(myName);
	 	mynLabel.adopt(myGid);
		mynRow.adopt(mynLabel);
		myForm.adopt(mynRow);
		myaLabel.adopt(myActive);
		myaRow.adopt(myaLabel);
		myForm.adopt(myaRow);
		myContainer.adopt(myForm);
		gmod.setTitle('<h4>Add/Edit group</h4>');
                gmod.setBody(myContainer.get('html'));
                gmod.setFooter('<a class="btn primary" onclick="mygroup.saveGroup()">Save</a>');
                if (gid) {
			$('group_edit').getChildren('div input').each(function(item) {
				if (item.get('name') == 'name') item.set('value', name);
				if (item.get('name') == 'gid') { 
					item.set('value', gid);
				}
				if (item.get('name') == 'active') {
					item.set('value', active); 
					if (active == 1) item.set('checked', 'checked'); 
				}
			});
		}
		gmod.show();	
	},
	groupSubscribe: function (uid) {
		var list = new Request.JSON({
			'url': '/ajax/call/user/selectGroup',
			'onSuccess': function(resJSON, resText) {
			 	mygroup.assignGroup(resJSON, uid);
			}
		}).get({'uid': uid});
	},
	addMembers: function(gid) {
		var members = new Request.JSON({
			'url': '/ajax/call/user/usersNotMember',
			'onSuccess': function(resJSON, resText) {
			 	mygroup.assignMembers(resJSON, gid);
			}
		}).get({'gid': gid});
	},
	assignMembers: function(users, gid) {
		var myContainer = new Element('div');
		var myList = new Element('ul', {'id': 'amember_edit'});
		users.each(function(value, key) {
			
			var myRow = new Element('li', {'class': 'row select link clearfix'});
			var myMember = new Element('div', {'html': '<h3>'+value.login+'</h3>'});
			myRow.adopt(myMember);	
			myList.adopt(myRow);	
		});
		myContainer.adopt(myList);
		gmod.setTitle('<h4>Assign user to groups</h4>');
                gmod.setBody(myContainer.get('html'));
                gmod.setFooter('<a class="btn primary" onclick="mygroup.saveGroupMembership('+gid+')">Save</a>');
		var myvar = $('amember_edit').getChildren('li');
		myvar.each(function(el) {
			el.addEvents({
    				click: function(){
					el.toggleClass('checked').tween('background-color','#87C6DB','#FFF');
				}
			});
		});	
                gmod.show();
		},
	assignGroup: function(groups, uid) {
		var myContainer = new Element('div');
		var myList = new Element('ul', {'id': 'agroup_edit'});
		groups.each(function(value, key) {
			
			var myRow = new Element('li', {'class': 'row select link clearfix'});
			var myPerm = new Element('div', {'html': '<h3>'+value.name+'</h3>'});
			myRow.adopt(myPerm);	
			myList.adopt(myRow);	
		});
		myContainer.adopt(myList);
		gmod.setTitle('<h4>Assign user to groups</h4>');
                gmod.setBody(myContainer.get('html'));
                gmod.setFooter('<a class="btn primary" onclick="mygroup.saveUserGroups('+uid+')">Save</a>');
                var myvar = $('agroup_edit').getChildren('li');
		myvar.each(function(el) {
			el.addEvents({
    				click: function(){
					el.toggleClass('checked').tween('background-color','#87C6DB','#FFF');
				}
			});
		});
		gmod.show();
	},
	delGroup: function(param) {
		new Request.JSON({
			'url': '/ajax/call/user/deleteGroup',
			'onSuccess': function(resJSON, resText) {
				CaptainHook.Message.show('Group deleted!' , 'icon-ok', { width: 320 });
				myuser.reloadActiveTab('user-tabs');
			}
		}).get({'gid': param});
	},
	delGroupPerm: function(rid, gid) {
		new Request.JSON({
			'url': '/ajax/call/user/deleteGroupPerm',
			'onSuccess': function(resJSON, resText) {
				gmod.hide();
				CaptainHook.Message.show('Perm assignation to group deleted!' , 'icon-ok', { width: 320 });
				myuser.reloadActiveTab('user-tabs');
			}
		}).get({'rid': rid, 'gid': gid});
	}
});
