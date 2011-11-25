{extends tplextends('webpage/webpage_html4')}

{block name='webpage_head'}
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <title>{$title|escape}</title>
  <link rel="shortcut icon" href="{$favicon}" type="image/x-icon" />
	<!--[if IE]>
	<style type="text/css">
		.clearfix {
			zoom: 1;
			display: block;
    }
	</style>
	<![endif]-->
{/block}

{block name='webpage_body'}
  <div style="margin: 10px 0 10px 0;font-weight: bold">A fantastic body for a fanstastic Captain.</div>
{/block}
