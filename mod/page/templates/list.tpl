{extends tplextends('page/layout')}
{block name='page_content'}
<div class="page-list">
	{block name='paginator'}
	{* Pagination *}
	<div id="pagination" class="pagination">
  		<ul>
    			<li  class="prev"><a id='paginator_prev' href="#">&larr; {t d='page' m='Previous'}</a></li>
    			<li class="active"><a id="paginator_nums" href="#"></a></li>
    			<li class="next"><a id="paginator_next" href="#">{t d='page' m='Next'} &rarr;</a></li>
  		</ul>
	</div>
	{/block}
		{block name='page_list'}
	<table id="page_list" class="table zebra-striped condensed-table bordered-table table-list" summary="Page List" border="0" cellspacing="0" cellpadding="0">
		<caption class="list">{t d='page' m='Pages list'}</caption>
		<thead>
			<tr>
			<th><div >{t d='page' m='Name'} </div></th>
			<th><div>{t d='page' m='Created by'}</div></th>
			<th><div>{t d='page' m='Published'}</div></th>
			<th><div>{t d='page' m='Lang'}</div></th>
			<th><div>{t d='page' m='Translation setted'}</div></th>
			<th><div>{t d='page' m='Created'}</div></th>
			<th><div>{t d='page' m='Updated'}</div></th>
			<th>{t d='page' m='Action'}</th>
			</tr>
		</thead>
		<tbody>
			{section name=p loop=$list}
			<tr>
				<td><a href="/page/{$list[p].sysname}">{$list[p].name}</a></td>
				<td>{$list[p].login}</td>
				<td >{if $list[p].published eq 1}{t d='page' m='yes'}{else}{t d='page' m='no'}{/if}</td>
				<td><i class="flag {$list[p].lang}"></i></td>
				<td >{if $list[p].id_lang_reference eq 0}{t d='page' m='no'}{else}{t d='page' m='yes'}{/if}</td>
				<td >{$list[p].created|date_format: '%d %b %Y'}</td>
				<td >{$list[p].updated|date_format: '%d %b %Y'}</td>
				<td class="action">
	 				<a class="btn" href="/page/edit/{$list[p].pid}"><i class="icon-edit"></i>  {t d='page' m='Edit'}</div></a>
	 				
					<a class="ajaxLink btn" onclick="mypage.delPage({$list[p].pid});" href="#"><i class="icon-remove"></i>  {t d='page' m='Del'}</a>
				</td>
			</tr>
			{/section}
		</tbody>
	</table>
	{/block}
</div>
<script>
	
	var mypage = new Page();
	window.addEvent('domready', function() {
		$$('a.ajaxLink').addEvent('click', function(event){
				event.preventDefault();
		});
		var paginate= new CHMyPaginate({
			paginateElement: 'pagination',
			tableElement: 'page_list',
			path:'/page/list/',
			conf: true,
			sort: '{$sort}',
			sortable: ['name', 'login', 'published', 'created', 'updated'],
			filters: [[['label', 'Title'],
				   ['name', 'name'],
				   ['type', 'text'],
				   ['returnMethod', 'func'],
				   ['returnFunc', 'nameFilter']],
				  [['label', 'Created by'],
                                   ['name', 'login'],
                                   ['type', 'select'],
                                   ['returnMethod', 'func'],
                                   ['returnFunc', 'authorList']],
				  [['label', 'Published'],
                                   ['name', 'published'],
                                   ['type', 'select'],
                                   ['returnMethod', 'bool'],
                                   ['returnFunc', 'no|yes']], 
				  [['label', 'Lang'],
                                   ['name', 'lang'],
                                   ['type', 'select'],
                                   ['returnMethod', 'bool'],
                                   ['returnFunc', 'French|Deutsch']],
				  [['label', 'Translation setted'],
                                   ['name', 'id_lang_reference'],
                                   ['type', 'select'],
                                   ['returnMethod', 'bool'],
                                   ['returnFunc', 'Not set|Set']], 
				 ],
			filter: '{$filter}',		
			maxrow: {$maxrow},
			offset: {$offset},
			quant: {$quant}
		});
	});

</script>
{/block}
