{extends tplextends('webpage/webpage_main')}

{block name='webpage_head' append}
<style>
  .module_title {
  margin-top: 10px;
  font-weight: bold;
  margin-bottom: 10px;
  }
  .langname {
  margin-top: 10px;
  font-weight: bold;
  margin-bottom: 10px;
  }
  .m {
  margin-top: 5px;
  font-weight: normal;
  }
  .trad {
  margin-bottom: 5px;
  width: 100%;
  }
  .
</style>
{/block}

{block name='webpage_body'}
<ul>
  {foreach from=$langs item=mod key=modname}
    <li class='module_title'>Module: {$modname|escape}</li>
    <ul>
      {foreach from=$mod item=trads key=lang}
        <form method='POST'>
          <li class='langname'>Language: {$lang|escape} <input type='submit' name='trad-submit' value='Submit this traduction'></li>
          <input type='hidden' name='modname' value='{$modname|escape}'/>
          <input type='hidden' name='lang' value='{$lang|escape}'/>
          {foreach from=$trads item=trad key=m}
            <div class='m'>{$m|escape}</div>
            <input type='text' class='trad' name='_{urlencode($m)|escape}' value='{$trad|escape}'>
          {/foreach}
	</form>
      {/foreach}
    </ul>
  {/foreach}
</ul>
{/block}
