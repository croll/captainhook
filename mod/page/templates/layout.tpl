{extends tplextends('webpage/webpage_main')}
{block name='webpage_head' append}
	{js file="/mod/cssjs/js/captainhook.js"}
	{js file="/mod/cssjs/js/messageclass.js"}
	{js file="/mod/cssjs/js/message.js"}
	{js file="/mod/cssjs/js/mooniform.js"}
	{js file="/mod/page/js/page.js"}
	{js file="/mod/cssjs/js/chmypaginate.js"}
	{js file="/mod/cssjs/js/chfilter.js"}
	
	{css file="/mod/cssjs/css/captainhook.css"}
	{css file="/mod/cssjs/ext/twitter-bootstrap/css/bootstrap.css"}
	{css file="/mod/cssjs/css/meioautocomplete.css"}
	{css file="/mod/cssjs/css/message.css"}
	{css file="/mod/cssjs/css/mooniform.css"}
	{css file="/mod/cssjs/css/mypaginate.css"}
	{css file="/mod/page/css/extra.css"}
	{css file="/mod/page/css/page.css"}
	{css file="/mod/page/css/icon.css"}
{/block}
{block name='webpage_body' }
<div class="topbar">
      <div class="fill">
        <div class="container">
          <a class="brand" href="/page/list/">{t d='page' m='Captainhook page module demo'}</a>
		<ul class="nav">
		<li ><a  class="top-btn" href="/"><i class="icon-home glyph-white"></i>  {t d='page' m='Accueil'}</a></li>
	   {if \mod\user\Main::userHasRight('Manage page')}
  		<li class="dropdown active" onclick="this.toggleClass('open');">
    		<a href="#"
          		class="dropdown-toggle"
          		data-toggle="dropdown">
				{t d='page' m='Page'}
          		<b class="caret"></b>
    		</a>
    		<ul class="dropdown-menu">
		{block name='page_menu' }
		<li><a class="top-btn" href="/page/list/"><i class="icon-th-list glyph-white"></i>  {t d='page' m='List'}</a></li>
        	<li><a class="top-btn" href="/page/edit/0"><i class="icon-pencil glyph-white"></i>  {t d='page' m='Add'}</a></li>
                {/block}
    		</ul>
  		</li>
	    {/if}
            	<li ><a  class="top-btn" href="/user/"><i class="icon-user glyph-white"></i>  {t d='user' m='User'}</a></li>
	</ul>
           </div>
      </div>
</div>

<div class="container">
	<div id="page_content">
	{block name='page_content'}
	{/block}
	</div>
</div>
{/block}
<div id="footer">
footer
</div>
