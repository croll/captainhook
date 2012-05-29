//  -*- mode:js; tab-width:2; c-basic-offset:2; -*-
var CHTable = new Class({
		Implements: [Options,Events],
		options: {
			rows: {},
			rowActions: {},
			actions: false,
			id: false,
			cid: false,
			paginate: 'true',
			onComplete: $empty
		},
		initialize: function(options) {
			this.setOptions(options);
			return this.create(options);
		},
		create: function(options) {
			this.setOptions(options);
			var mc= new Element('div');
			var myTable = new Element('table', {'class':'table table-striped table-bordered table-condensed'});
			var myTHead = new Element('thead');
			var myTBody = new Element('tbody');	
			options.rows.each(function(item,index) {
				var myThTr = this.THead(item, index, options);
				myTHead.adopt(myThTr);
				myTBody.adopt(this.TBodyRow(item, options));	
			}.bind(this));
			myTable.adopt(myTHead);
			myTable.adopt(myTBody);
			mc.adopt(myTable);
			return mc;
		},
		THead: function(item, index, options) {
			if (index == 0) { 
				var mykeys = Object.keys(item);
				var myThTr = new Element('tr');
				// set table column headers
				mykeys.each(function(key) {
					var myTh= new Element('th', { 'html' : key});
					myThTr.adopt(myTh);
				}.bind(this));
				if (options.rowActions) {	
					var myTh= new Element('th', { 'html' : 'Action'});
					myThTr.adopt(myTh);
				}
				if (options.actions) {
					var myTh= new Element('th', { 'html' : 'Action'});
					myThTr.adopt(myTh);
				}
			}
			return myThTr;
		},
		TBodyRow: function(item, options) {
			var myTBody = new Element('tbody');
				// set table body row
				var myrow = Object.values(item);
				var myTr= new Element('tr');
				myrow.each(function(value) {
					var myTd= new Element('td', { 'html' : value });
					myTr.adopt(myTd);
				}.bind(this));
				if (options.rowActions) {	
					var buttons ='<a class="btn" href="#" onclick="'+ options.rowActions.func + '(' + item.rid + ', '+ options.rowActions.params.gid+');"><i class="' + options.rowActions['class'] +'"></i>'+ options.rowActions.label +'</a>';
					var myTd= new Element('td', { 'html' : buttons});
					myTr.adopt(myTd);
				}
				if (options.actions) {
					var buttons = options.actions.split('|');	
					var button ='';
					buttons.each(function(btn) {
						var b= btn.split(',');
						//options.id idName
						// b[0] label
						// b[1] func
						// b[2] icon
						// b[3] cidName
						var myIdName= options.id;
						myObject = Object.filter(item, function(value, key) {
								return key == myIdName ;
						});
						var param= Object.values(myObject);
						if (b[3]) {
							console.log(b[1]+','+b[3]);
							var myCidName= options.cid;
							myCid = Object.filter(item, function(value, key) {
								return key == myCidName ;
							});
							param += ',';
							param += Object.values(myCid);
						}
						
						button += '<a class="btn" href="#" onclick="'+ b[1] + '(' + param +');"><i class="' + b[2] +'"></i>'+ b[0] +'</a>';
					}.bind(this));
					var myTd= new Element('td', { 'html' : button});
					myTr.adopt(myTd);
				}
				myTBody.adopt(myTr);
			return myTBody;
		}
});
