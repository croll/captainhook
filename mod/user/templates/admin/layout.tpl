{extends tplextends('webpage/webpage_main')}
{block name='webpage_head' append}
	{js file="/mod/cssjs/js/mootools.js"}
	{js file="/mod/cssjs/js/mootools.more.js"}
	{js file="/mod/cssjs/js/tabs.js"}
	{js file="/mod/cssjs/js/captainhook.js"}
	{js file="/mod/cssjs/js/meioautocomplete.js"}
	{js file="/mod/cssjs/js/messageclass.js"}
	{js file="/mod/cssjs/js/message.js"}
	{js file="/mod/cssjs/js/mooniform.js"}
	{js file="/mod/cssjs/js/Modal.js"}
	{js file="/mod/cssjs/js/chtable.js"}
	{js file="/mod/user/js/user.js"}
	{js file="/mod/user/js/group.js"}
	{js file="/mod/user/js/perm.js"}
		
	{css file="/mod/cssjs/css/captainhook.css"}
	{css file="/mod/cssjs/css/bootstrap.css"}
	{css file="/mod/cssjs/css/meioautocomplete.css"}
	{css file="/mod/cssjs/css/message.css"}
	{css file="/mod/cssjs/css/mooniform.css"}
	{css file="/mod/cssjs/css/Modal.css"}
	{css file="/mod/user/css/user.css"}
	{css file="/mod/user/css/icon.css"}
{/block}
{block name='webpage_body' }
<div class="topbar">
      <div class="fill">
        <div class="container">
          <a class="brand" href="/user/">Captainhook user module demo</a>
	<ul class="nav">
		<li ><a  class="top-btn" href="/page/accueil"><i class="icon-home glyph-white"></i>  Accueil</a></li>
            	<li ><a  class="top-btn" href="/page/list/"><i class="icon-page glyph-white"></i>  Page</a></li>
	   {if \mod\user\Main::userHasRight('Manage rights')}
  		<li class="dropdown active" onclick="this.toggleClass('open');">
    		<a href="#"
          		class="dropdown-toggle"
          		data-toggle="dropdown">
				User
          		<b class="caret"></b>
    		</a>
    		<ul class="dropdown-menu">
		{block name='user_menu' }
		<li><a class="top-btn" href="/user/"><i class="icon-th-list glyph-white"></i>  manage user</a></li>
        	<li><a href="/user/edit/0" class="top-btn modal-overlay" ><i class="icon-pencil glyph-white"></i>  user add</a></li>
		{/block}
    		</ul>
  		</li>
	    {/if}
	</ul>
         </div>
      </div>
</div>

<div class="container">
	<div id="user_content">
	{block name='user_content'}
		test
	{/block}
	</div>
</div>
{/block}
<div id="footer">
footer
</div>
