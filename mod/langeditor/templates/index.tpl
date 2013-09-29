{extends tplextends('webpage/webpage_main')}

{block name='webpage_head' append}
<style>
  .trad {
  margin-bottom: 5px;
  width: 90%;
  }
  .langbutton, .modbutton {
  font-weight: bold;
  padding: 5px;
  background: #aaa;
  cursor: pointer;
  border: solid 1px white;
  }
  .selected {
  background: #eee;
  border: solid 1px black;
  }
  .color-1 {
    background: #eee;
  }
  .color--1 {
    background: #bbb;
  }
</style>
{/block}

{block name='webpage_body'}
  <div style="margin: 15px">
    {foreach from=$languages item=l}
      <span class='langbutton' id='langbutton-{$l}' onclick='showlanguage("{$l}")'>{$l|escape}</span>
    {/foreach}
  </div>

  {foreach from=$languages item=l}
    <div class="langcontent" id="lang-{$l}" style="display: none">
      <div style="margin: 15px">
        {foreach from=$langs item=mod key=modname}
          <span class='modbutton modbutton-{$modname}' onclick='showmod("{$modname}")'>{$modname|escape}</span>
        {/foreach}
      </div>

      {foreach from=$langs item=mod key=modname}
        <div class="modcontent modcontent-{$modname}" style="display: none">
        <form method='POST'>
          <input type="hidden" name="lang" value="{$l}"/>
          <input type="hidden" name="modname" value="{$modname}"/>
          {$color=1}
          {foreach from=$mod[$l] item=trad key=m}
            <div class='color-{$color}'>
              <div class='m'>{$m|escape}</div>
              <input {if $trad === null} style='background: #fdd'{/if} type='text' class='trad' name='{md5($m)}' value='{$trad|escape}'>
            </div>
            <br />
            {$color=-$color}
          {/foreach}
          <input type='submit' name='trad-submit' value='Submit this traduction'>
        </form>
        </div>
      {/foreach}
    </div>
  {/foreach}

  <div>
    <br />
  </div>



{literal}
<script>
function showlanguage(l) {
         $$('.langcontent').setStyle('display', 'none');
         $('lang-'+l).setStyle('display', '');

         $$('.langbutton').removeClass('selected');
         $('langbutton-'+l).addClass('selected');
         console.log("show "+l);
}

function showmod(mod) {
         $$('.modcontent').setStyle('display', 'none');
         $$('.modcontent-'+mod).setStyle('display', '');

         $$('.modbutton').removeClass('selected');
         $$('.modbutton-'+mod).addClass('selected');
         console.log("show "+mod);
}
</script>
{/literal}


{/block}
