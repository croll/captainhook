{extends tplextends('webpage', 'webpage_main')}

{block name='webpage_head'}
	{js file="/mod/cssjs/js/mootools.js"}
	{js file="/mod/cssjs/js/mootools.more.js"}
	{js file="/mod/cssjs/js/core.js"}
{/block}

{block name='webpage_body'}
<div id="test"></div>
<script language="javascript">
	{literal}

	function testclick(button) {
		var req = new Request.JSON({url: '/tests/'+button.id, 
			onSuccess: function(json, text) {
				test.set('html', json.html);
				if (json.js) eval(json.js);
			},
			onError: function(text, error) {
				console.log(error.message);
			}
		}).get({});
	}
	{/literal}
</script>

<form action="{$smarty.server.SCRIPT_NAME}">
	<input type="button" id="ajax" value="test 1" onclick="testclick(this)">
	<input type="button" id="ajax2" value="test 2" onclick="testclick(this)">
</form>
{/block}
