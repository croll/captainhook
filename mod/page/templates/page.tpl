{extends tplextends('page/layout', 'onajax:page_content')}
{block name='page_menu' append}
	{if $smarty.server.REQUEST_URI != '/page/list/'}
	 <li><a href="/page/edit/{$page.pid}"><i class="icon-edit glyph-white"></i>  {t d='page' m='Edit'}</a></li>
	{/if}
{/block}
{block name='page_content'}
	<div class="page-header" id="page_title">
		{if \mod\user\Main::userHasRight('Manage page')}<a class="float" href="/page/edit/{$page.pid}"><i class="icon-edit"></i></a>{/if}
		<h1>{$page.name}</h1>
		<small>
			{t d='page' m='Created %s by %s - last updated: %s' p0=$page.created|date_format: '%d %b %Y' p1=$page.full_name p2=$page.updated|date_format: '%d %b %Y'}
		</small>
	</div>
	<div id="page_rawcontent" class="clearfix">{$page.content}</div>
{/block}

