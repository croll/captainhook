<!DOCTYPE html>
<html lang="fr">
    <head>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8">
    <title>Time Stacker</title>
		{css file="css/style.css"}
		{js file="ext/mootools.js"}
		{js file="ext/mootools.more.js"}
		{js file="js/core.js"}
		{hook name="css"}
		{hook name="js"}
  </head>
  <body>
    <div id="maincontainer">
      <div id="page">
        <div id="content">
	    		<div id="sitetitle"></div> 
						{* CONTAINER *}
						<div id="pagecontainer">
							{* HEADER *}
							<div id="header">
								{if $page != 'login'}
									Header
								{/if}
							</div>
							{* FIN HEADER *}
							{if isset($displayLogin)}
								{include file="login.tpl"}
							{else}
								{include file="$content"}
							{/if}
						</div>
					</div>
      <div class="clearfix"></div>
      </div>
      <div id="footer"</div>
		CROLL
      </div>
    </div>
  </body>
</html>
