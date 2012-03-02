{extends tplextends('page/layout', 'onajax:page_content')}
{block name='page_menu' append}
	{if $smarty.server.REQUEST_URI != '/page/list/'}
	 <li><a href="/page/edit/{$page.pid}"><i class="icon-edit glyph-white"></i>  Edit</a></li>
	{/if}
{/block}
{block name='page_content'}
	<div class="page-header" id="page_title">
		{if \mod\user\Main::userHasRight('Manage page')}<a class="float" href="/page/edit/{$page.pid}"><i class="icon-edit"></i></a>{/if}
		<h1 lang="fr-FR">{$page.name}</h1>
		<small lang="fr-FR">
			Created  {$page.created|date_format: '%d %b %Y'} by {$page.full_name} : last updated - {$page.updated|date_format: '%d %b %Y'}
		</small>
	</div>
	<div id="page_rawcontent" lang="fr-FR">{$page.content}</div>
{/block}

