{extends tplextends('user/admin/layout')}

{block name='user_menu' append}
		{if $user.uid !=0} 
		<li><a  href="/user/{$user.sysname}"><i class="icon-eye-open glyph-white"></i>  View</a></li>
		{/if}
{/block}
{block name='user_content'}
	<div class="user-header" id="user_title">
		<h1>{$user.login}</h1>
		<small>
			Created  {$user.created|date_format: '%d %b %Y'} : last updated - {$user.updated|date_format: '%d %b %Y'}
		</small>
	</div>
	<div class="row">
	<fieldset>
		<form id="user_edit" method="POST" action="">
			<input type="hidden" name="field_fieldform_id" value="user_edit">
			<input type="hidden" name="uid" value="{$user.uid}" >
			<input type="hidden" name="created" value="{$user.created}" >
			<input type="hidden" name="updated" value="{$user.updated}" >

			<div class="clearfix"><label for="login"><span>User login:</span></label><input class="xlarge" title="login" type="text" name="login" value="{$user.login}" ></div>
			<div class="clearfix"><label for="full_name"><span>User Full Name:</span></label><input class="xlarge" title="full_name" type="text" name="full_name" value="{$user.full_name}" ></div>
			<div class="clearfix"><label for="email"><span>User Email:</span></label><input class="xlarge" title="email" type="text" name="email" value="{$user.email}" ></div>
			<div class="clearfix"><label for="password"><span>User password:</span></label><input class="xlarge" title="password" type="text" name="password" value="" ></div>
			<div class="clearfix"><label for="pass2"><span>Repeat password:</span></label><input class="xlarge" title="pass2" type="text" name="pass2" value="" ></div>
			<div class="clearfix"><label for="active"><span>User active:</span><input onclick="var c=this.get('value'); if (c==0) this.set('value', 1); else this.set('value', 0);" class="checkbox" title="active" type="checkbox" name="active" {if $user.status == 1} checked ="checked" {/if} value="{$user.status}" ></label></div>

			<div class="action">
				<input class="btn primary" id='user_edit_submit'  type="submit" name="submit" value="Save changes">
				<button type="reset" class="btn">Cancel</button>
			</div>
		
		</form>
</fieldset>
	<div>


<script>
	var myuser = new User();
	$('user_edit_submit').addEvent('click', function(event){
		event.stop(); //Prevents the browser from following the link.
		var params = $('user_edit'); 
		myuser.postForm('/ajax/call/user/saveUser', 'user_edit', params);
	});
</script>
{/block}

