//  -*- mode:js; tab-width:2; c-basic-offset:2; -*-
var Paginate = new Class({
	Implements: [Options,Events],
	options: {
		paginateElement: 'paginate',
		active: true,
		path: '/page/list/',
		sort: false,
		conf: false,
		sortable: false,
		sortedColor: '#FFF',
		sortedBack: '#CC9900',
		offset: 0,
		maxrow: 10,
		quant: 100,
		filters: {},
		filter: '',
		onComplete: null
	},
	initialize: function(options) {
		this.setOptions(options);
		this.create(options);
		if(options.filters) {
			this.setFilters(options);	
		}
		if (options.sortable) {
			this.sortableTableHead(options);
		}	
	},
	setFilters: function(options) {
		this.setOptions(options);
		// you need filter.js to call class Filter
		var myFilter = new Filter({
			paginateElement: options.paginateElement,
			formElement: 'filterForm',
			path: options.path,
			sort: options.sort,
			offset: options.offset,
			maxrow: options.maxrow,
			quant: options.quant,
			filters: options.filters,
			filter: options.filter
		});
	},
	hide: function(options) {
		this.setOptions(options);
		var el=$(paginateElement).toggle();
	},
	create: function(options) {
		this.setOptions(options);
		// calculate maxpage	
		var maxpage= (options.quant / options.maxrow).ceil();
		
		// set sort regroute
		if (options.sort) {
			var sortpath='sort/'+options.sort+'/';
		}	
		// set prev button
		this.setPrevBtn(options, maxpage , sortpath);	
		
		// set pagination label 
		this.setLabelBtn(options, maxpage, sortpath);
		// set configure interface
		this.setConfigBox(options, maxpage, sortpath);
			
		// set next button
		this.setNextBtn(options, maxpage, sortpath);	
	},
	setPrevBtn: function(options, maxpage, sortpath) {
		this.setOptions(options);
		if (options.offset > 0) {
			$('paginator_prev').addEvent('click', function(event){
    				event.stop(); 
				var url= options.path+'offset/'+ (options.offset - options.maxrow)+'/maxrow/'+options.maxrow+'/';
				if(options.sort) {
					url +=sortpath;
				}
				window.location.replace(url);
			});
		} else {
			$('paginator_prev').getParent('li.prev').addClass('disabled');
		}
	},
	setLabelBtn: function(options, maxpage, sortpath) {
		this.setOptions(options);
		
		var pageNbr = (options.offset / options.maxrow ) + 1;
		$('paginator_nums').set('html', 'page '+ pageNbr + ' on ' + maxpage);	
	},
	setNextBtn: function(options, maxpage, sortpath) {
		this.setOptions(options);
		
		if ((options.offset + options.maxrow) < options.quant) {		
			$('paginator_next').addEvent('click', function(event){
    				event.stop(); 
				var url= options.path+'offset/'+ (options.offset + options.maxrow)+'/maxrow/'+options.maxrow+'/';
				if(options.sort) {
					url +=sortpath;
				}
				window.location.replace(url);

			});
		} else {
			$('paginator_next').getParent('li.next').addClass('disabled');
		}
	},
	setConfigBox: function(options, maxpage, sortpath) {
		this.setOptions(options);
		if (options.conf) {
		var pel = $(options.paginateElement);
		pel.setStyle('margin-bottom',0);
		var configBox = new Element('div', {
			'id': 'configBox'
		});
		configBox.wraps(pel);
			
			var myConf= new Element('div', {
				'id': 'paginate_conf'
			});
			//set offset configuration
			var myOffset = new Element('select', {'class': 'btn primary'});
			var label = new Element('option').set('html', 'Jump to page').set('value',0).inject(myOffset);	
			var i=1;
			while (i <= maxpage ) {
				var newOffset= (i-1) * options.maxrow;
				var myText= 'page '+ i ;
				var myUrl= '/page/list/offset/'+ newOffset+'/maxrow/'+options.maxrow+'/';
				var myO = new Element('option');
				myO.set('value', myUrl);
				myO.set('html', myText);
				myO.inject(myOffset);
				i++;
			}
			myOffset.inject(myConf);
			myOffset.addEvent('click', function(event){
                                event.stop();
				if(this.value != 0) window.location.replace(this.value);
				
			});
			//set maxRow configuration
			var myMaxRow = new Element('select', {'class': 'btn secondary'});
			var label = new Element('option').set('html', 'list length').set('value', 0).inject(myMaxRow);	
;
			var i=10;
			while (i <= options.quant ) {
				var newMaxRow= i;
				var myText= i;
				var myUrl= '/page/list/offset/0/maxrow/'+ i +'/';
				var myO = new Element('option');
				myO.set('value', myUrl);
				myO.set('html', myText);
				myO.inject(myMaxRow);
				i = (i+(options.quant/5));
			}	
			var listAll = new Element('option').set('html', 'All results on 1 page').set('value', '/page/list/offset/0/maxrow/'+ options.quant +'/').inject(myMaxRow);	
			
			myMaxRow.inject(myConf);
			myMaxRow.addEvent('click', function(event){
                                event.stop();
				if(this.value != 0) window.location.replace(this.value);
				
			});

			var myli= new Element('div', {'class': 'config'});
			myConf.inject(myli);
			$('configBox').adopt(myli);
			
			var myCfx = new Fx.Slide('paginate_conf', {
                                mode: 'vertical',
				duration: 'short',
                                transition: Fx.Transitions.Pow.easeOut
                	});
			// hide by default
			myCfx.hide();
			
			$('paginator_nums').addEvent('click', function(event){
    				event.stop(); 
				myCfx.toggle();
			}.bind(this)).addClass('clearfix');
		}
	},
	sortableTableHead: function(options)  {
		this.setOptions(options);
		var fields = options.sortable;
		var table = $(options.tableElement);
		var tableHeader = table.getElements('thead tr th');
		tableHeader.each(function(el, index) {
			if (fields[index]) {
				if (options.sort == fields[index]+'_asc' || options.sort == fields[index]+'_desc'){
					var arrow = options.sort.split('_')[1];
					el.getChildren('div').addClass(arrow);
					el.addClass('sorted');
				} else {
					el.getChildren('span').addClass('asc');
				};

				el.addEvent('click', function(){
					if (el.hasClass('sorted')) {
    						if (options.sort.split('_')[1] == 'asc') {
							var sort='sort/'+fields[index]+'_desc/';
						} else {
							var sort='sort/'+fields[index]+'_asc/';
						}
					} else {
    						var sort='sort/'+fields[index]+'_asc/';
					}
					var url= options.path+'offset/'+ (options.offset)+'/maxrow/'+options.maxrow+'/'+sort;
					window.location.replace(url);
				}).addClass('link');
			}
		});
	}
}); 

