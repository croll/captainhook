var ch_lang='fr_FR';
var ch_langs={};

function ch_t(domain, blah) {
    var args=[];
    for (var i=1; i<arguments.length; i++) args[i-1]=arguments[i];

    if (ch_langs[ch_lang] && ch_langs[ch_lang][domain] && ch_langs[ch_lang][domain][blah])
	args[0]=ch_langs[ch_lang][domain][blah];

    //document.write('<span class="ch_lang_trad" paf="'+escape(objectToString(args))+'">'+sprintf.apply(null, args)+'</span>');
    return sprintf.apply(null, args);
}

function ch_lang_decodeURL(url){return unescape(url.replace(/\+/g,  " "));}

/*
function ch_setlang(lang) {
    ch_lang=lang;
    
    new Request.JSON({
	'url': '/mod/lang/cache/'+lang+'.js',
	onSuccess: function(gronk) {
	    ch_langs[lang]=gronk;

	    $$('span.ch_lang_trad').each(function(elem) {
		var paf=JSON.decode(ch_lang_decodeURL(elem.get('paf')));
		if (ch_langs[ch_lang][paf['d']] && ch_langs[ch_lang][paf['d']][paf['m']])
		    var args=[ ch_langs[ch_lang][paf['d']][paf['m']] ];
		else
		    var args=[ paf['m'] ];
		for (var i=0; i<paf['p'].length; i++) args[i+1]=paf['p'][i];
		elem.set('html',sprintf.apply(null, args));
	    });
	}
    }).get();
}
*/

function ch_setlang(lang, redirect) {
    if (!redirect) redirect='/';
    window.location.href='/mod/lang/set_lang/'+encodeURIComponent(lang)+'/'+redirect;
}
