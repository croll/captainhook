{extends tplextends('webpage/webpage_main')}
{block name='webpage_body'}
<div>{t d='site_test' m='Welcome on Captain Hook (module %s) !' p0='site_test'}</div>
<div>{t d='site_test' m='Il fait beau %s !' p0="Aujourd'hui"}</div>

<div>Language: 
 <a href="javascript:ch_setlang('de_DE')">de_DE</a>
 <a href="javascript:ch_setlang('fr_FR')">fr_FR</a>
 <a href="javascript:ch_setlang('en_US')">en_US</a>
</div>
{/block}
