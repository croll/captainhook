{extends tplextends('webpage', 'webpage_main')}
{block name='webpage_body'}
 {if isset($site_test_myform)}
  {$site_test_myform}
 {else}
  <p>firstname: {$site_test_firstname|escape}</p>
  <p>lastname: {$site_test_lastname|escape}</p>
 {/if}
{/block}
