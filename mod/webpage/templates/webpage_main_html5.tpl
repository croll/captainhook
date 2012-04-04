{extends tplextends('webpage/webpage_html5')}

{block name='webpage_head'}
	<meta charset="utf-8">
  <title>{$title|escape}</title>
  <link rel="shortcut icon" href="{$favicon}" type="image/x-icon" />
  <!--[if lt IE 9]>
    <script src="/mod/cssjs/ext/html5shiv/html5.js"></script>
  <![endif]-->
{/block}

{block name='webpage_body'}
  <div style="margin: 10px 0 10px 0;font-weight: bold">A fantastic body for a fanstastic Captain.</div>
{/block}
