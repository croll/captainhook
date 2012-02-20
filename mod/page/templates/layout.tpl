{extends tplextends('webpage/webpage_main')}
{block name='webpage_head' append}
	{js file="/mod/cssjs/js/mootools.js"}
	{js file="/mod/cssjs/js/mootools.more.js"}
	{js file="/mod/cssjs/js/captainhook.js"}
	{js file="/mod/cssjs/js/meioautocomplete.js"}
	{js file="/mod/cssjs/js/messageclass.js"}
	{js file="/mod/cssjs/js/message.js"}
	{js file="/mod/cssjs/js/mooniform.js"}
	{js file="/mod/cssjs/js/multiselect.js"}
	{js file="/mod/page/js/page.js"}
	{js file="/mod/cssjs/js/chmypaginate.js"}
	{js file="/mod/cssjs/js/chfilter.js"}
	
	{css file="/mod/cssjs/css/captainhook.css"}
	{css file="/mod/cssjs/css/bootstrap.css"}
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
          <a class="brand" href="/page/list/">Captainhook page module demo</a>
		<ul class="nav">
		<li ><a  class="top-btn" href="/page/accueil"><i class="icon-home glyph-white"></i>  Accueil</a></li>
	   {if \mod\user\Main::userHasRight('Manage page')}
  		<li class="dropdown active" onclick="this.toggleClass('open');">
    		<a href="#"
          		class="dropdown-toggle"
          		data-toggle="dropdown">
				Page
          		<b class="caret"></b>
    		</a>
    		<ul class="dropdown-menu">
		{block name='page_menu' }
		<li><a class="top-btn" href="/page/list/"><i class="icon-th-list glyph-white"></i>  List</a></li>
        	<li><a class="top-btn" href="/page/edit/0"><i class="icon-pencil glyph-white"></i>  Add</a></li>
                {/block}
    		</ul>
  		</li>
	    {/if}
            	<li ><a  class="top-btn" href="/user/"><i class="icon-user glyph-white"></i>  User</a></li>
	</ul>
           </div>
      </div>
</div>

<div class="container">
	<div id="page_content">
	{block name='page_content'}
		test
	{/block}
	</div>
</div>
{/block}
<script>
    var behavior = new Behavior().apply(document.body);
    var delegator = new Delegator({
      getBehavior: function(){ return behavior; }
    }).attach(document.body);
  </script>
<div id="footer">
footer
</div>
