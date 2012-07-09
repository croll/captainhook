{extends tplextends('webpage/webpage_main')}

{block name='webpage_head' append}
	{css file='/mod/user/css/login.css'}
	{js file='/mod/cssjs/js/mootools.js'}
	{js file='/mod/cssjs/js/mootools.more.js'}
	{if isset($url_redirect)}
		<meta http-equiv="Refresh" content="2; url={$url_redirect}/" />
	{/if}
{/block}

{block name='webpage_body'}
  {if $displayForm}
		{form mod="user" file="templates/loginForm.json"}
		<fieldset>
			<legend>Auth</legend>
			{if isset($login_failed)}
				<div id="message">{t d='user' m="Login failed"}</div>
			{/if}	
			<div>
				<div>
					{$loginForm.login}
				</div>
				<div>
					{$loginForm.password}
				</div>
			</div>
			<div>
				<input type="submit" value="{t d='user' m="Connection"}" ?>
			</div>
		</fieldset>
		<script>
		window.addEvent('domready', function(){
			document.id('loginForm').getElements('[type=text], [type=password]').each(function(el){
				new OverText(el, {
					poll: true
				});
			});
		});
		</script>
		{/form}
	{else}
		{if isset($login_ok)}
			{t d='user' m="Login successful, redirecting..."}
		{elseif isset($logout)}
			{t d='user' m="Logout ok, redirecting..."}
		{else}
			{t d='user' m="You are already identified, redirecting..."}
		{/if}
	{/if}	
{/block}
