{extends tplextends('webpage', 'webpage_main')}

{block name='webpage_head' append}
	{css file='/mod/user/css/login.css'}
	{if isset($url_redirect)}
		<meta http-equiv="Refresh" content="2; url={$url_redirect}/" />
	{/if}
{/block}

{block name='webpage_body'}
  {if isset($loginform)}
		<fieldset>
			<legend>Auth</legend>
			{if isset($login_failed)}
				<div id="message">{l s='Login failed'}</div>
			{/if}	
			{$loginform}
		</fieldset>
	{else}
		{if isset($login_ok)}
			{l s='Login successful, redirecting...'}
		{elseif isset($logout)}
			{l s='Logout ok, redirecting...'}
		{else}
			{l s='You are already identified, redirecting...'}
		{/if}
	{/if}	
{/block}
