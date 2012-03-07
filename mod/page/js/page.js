//  -*- mode:js; tab-width:2; c-basic-offset:2; -*-
var Page = new Class({
	Implements: [Options,Events],
	options: {
		updateElement: 'page_content',
		paginate: 'true',
		onComplete: null
	},
	initialize: function(options) {
		this.setOptions(options);
	},
	delPage: function(param) {
		new Request.JSON({
			'url': '/ajax/call/page/deletePage',
			'onSuccess': function(resJSON, resText) {
				window.location.replace("../list/");
			}
		}).get({'pid': param});
	},
	listIdLangReference: function(sysname, lang, update) {
		console.log(sysname);
		console.log(lang);
		new Request.JSON({
			'url': '/ajax/call/page/idLangReference',
			'onSuccess': function(resJSON, resText) {
				mypage.setIdLangReference(resJSON, update);
			}
		}).get({'sysname': sysname, 'lang': lang});
	},
	setIdLangReference: function(resJSON, update) {
		if($(update)) {
			var list = $(update).empty();
			var optionn = new Element('option', {'value': '', 'html': 'None'});
			list.adopt(optionn);
		}
		Object.each(resJSON, function(item, index) {
			var k = Object.values(item);
			var id_lang_reference = k[0];
			var option = new Element('option', {'value': id_lang_reference, 'html': k[2]});
			list.adopt(option);
		});

	},
	setPage: function(name) {
		new Request.JSON({
			'url': '/ajax/call/page/render',
			'onSuccess': function(resJSON, resText) {
					var v = Object.values(resJSON);
					var container = new Element('div');
					var mytexta= new Element('textarea', {'id': 'page-cont', 'class': 'span6 content','rows': '50', 'cols': '50', 'name': 'page_content', 'html': v[5]});
					container.adopt(mytexta);
					pmod.setTitle('<h2>'+v[2]+'</h2>');
					pmod.setBody(container.get('html'));
					var button ='<span class="float"><a id="page_edit_submit" class="btn btn-primary" href="#" >Save</a></span>';
					pmod.setFooter(button);
					var myeditor = new CHWysiwyg({'editorElement':'page-cont'});
        				$('page_edit_submit').addEvent('click', function(event){
                				event.stop(); //Prevents the browser from following the link.
                				var content = myeditor.prepareToSave();
                 
                				$('page-cont').set('value', content);
                				var params = $('page_edit'); 
                				mypage.postForm('/ajax/call/page/savePage', 'page_edit', params);
        				});

					pmod.show();
			}
		}).get({'sysname': name});
	},
	render: function(name,update,lang) {
		var mpage = new Request.JSON({
      			'url': '/ajax/call/page/render',
                       'onSuccess': function(resJSON, resText) {
				if (update) {
					var k = Object.keys(resJSON);
					var v = Object.values(resJSON);
					var title = new Element ('h2', {'html': v[2]});		
					var body = new Element ('div', {'class': 'well', 'html': v[5]});	
					$(update).empty().adopt(title).adopt(body).fade('toggle');	
				} else {
					//display as modal 	
					pmod.setTitle(button+'<h4>v[2]</h4>');
					pmod.setBody(v[5]);
					pmod.setFooter('');
					pmod.show();
				}
                        }
                }).get({'sysname': name, 'lang': 'fr_FR'});
	},
	postForm: function(url,formElements,params) {
		var myreq = new Request.JSON({
			'url': url,
			'onSuccess': function(resJSON, resText) {
				// redirect to page list 
				window.location.replace("../list/");
			}
		}).send(params);
	}
});
