{extends tplextends('user/admin/layout')}
{block name='webpage_head' append}
	{js file="/mod/cssjs/js/tabs.js"}
	{js file="/mod/cssjs/js/messageclass.js"}
	{js file="/mod/cssjs/js/message.js"}
	{js file="/mod/cssjs/js/mooniform.js"}
	{js file="/mod/cssjs/js/Modal.js"}
	{js file="/mod/cssjs/js/chtable.js"}
	{js file="/mod/user/js/user.js"}
	{js file="/mod/user/js/group.js"}
	{js file="/mod/user/js/perm.js"}
		
	{css file="/mod/cssjs/css/message.css"}
	{css file="/mod/cssjs/css/tabs.css"}
	{css file="/mod/cssjs/css/mooniform.css"}
	{css file="/mod/cssjs/css/Modal.css"}
	{css file="/mod/user/css/user.css"}
	{css file="/mod/user/css/icon.css"}
{/block}
{block name='user_content'}
<div class="user-list" id="user-tabs">
	<ul class="tabs" data-behavior="BS.Tabs">
  		<li class="tab"><a ><span>{t d='user' m='Settings'}S</span></a></li>
  		<li class="tab"><a
			data-title="{t d='user' m='User list'}"  
			data-actions="perm,myperm.userPerms,icon-perm|groups, mygroup.userGroups,icon-group|edit,myuser.getUser,icon-edit|del,myuser.delUser,icon-remove" 
			data-id="uid" 
			data-bicon="icon-user" 
			data-update="users" 
			data-btn="{t d='user' m='Add user'}" 
			data-bclick="myuser.setUser();" 
			href="/ajax/call/user/userList"><i class="icon-user"></i><span>{t d='user' m='Users'}</span></a></li>
  		<li class="tab"><a 
			data-title="{t d='user' m='Group list'}"  
			data-actions="members, mygroup.membersList,icon-group|perm, mygroup.groupPerms,icon-perm|edit,mygroup.getGroup,icon-edit|del,mygroup.delGroup,icon-remove" 
			data-id="gid" 
			data-bicon="icon-group" 
			data-update="groups" 
			data-btn="{t d='user' m='Add group'}" 
			data-bclick="mygroup.setGroup();" 
			href="/ajax/call/user/groupList"><i class="icon-group"></i><span>{t d='user' m='Groups'}</span></a></li>
  		<li class="tab"><a  
			data-title="{t d='user' m='Permission list'}"  
			data-actions="groups,mygroup.permGroups,icon-group|edit,myperm.getPerm,icon-edit|del,myperm.delPerm,icon-remove" 
			data-id="rid" 
			data-bicon="icon-perm" 
			data-update="permissions" 
			data-btn="{t d='user' m='Add permission'}" 
			data-bclick="myperm.setPerm();" 
			href="/ajax/call/user/permList"><i class="icon-perm"></i><span>{t d='user' m='Permissions'}</span></a></li>
  		<li class="tab"><a href="/logout"><i class="icon-loader"></i><span>{t d='user' m='Logout'}</span></a></li>
	</ul>
	<div id="settings" class="content">
			{*include file='user/admin/settings'*}
  	</div>
	<div id="users" class="content">
	    		loading
			...
  	</div>

  	<div id="groups" class="content">
	    		loading
			...
  	</div>
  	<div id="permissions" class="content">
			loading
	    		...
  	</div>

  	
</div>
<script>
	var myuser = new User();
	var mygroup = new Group();
	var myperm = new Perm();
	var umod = new Modal.Base(document.body);
	var gmod = new Modal.Base(document.body);
	var pmod = new Modal.Base(document.body);

 	window.addEvent('domready', function() {
		myuser.setTabs('user-tabs');
	});
</script>
{/block}
