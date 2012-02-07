var CHFilter = new Class({
	Implements: [Options,Events],
	options: {
		paginateElement: 'paginate',
		formElement: 'filterForm',
		path: '/page/list/',
		sort: 'created_desc',
		offset: 0,
		maxrow: 10,
		quant: 100,
		filters: {},
		filter: '',
		onComplete: this.onComplete
	},
	initialize: function(options) {
		this.setOptions(options);
		this.createFilters(options);
	},
	createFilters: function(options) {
		this.setOptions(options);
		var filterForm = new Element('form', {'id': options.formElement, 
					'class': 'well form-inline form-horizontal',
					'html': '<h4>Filter By</h4>'});
		var filterEl = options.filters.each(function (item, index) {
			clearDiv = this.createFilter(options, item).inject(filterForm);
		}.bind(this));
			
		//add submit button
		var mySubmit=new Element('input', {'class':'float btn primary',
				'type':'submit',
				'name': 'submit',
				'value': 'Filter'}).addEvent('click', function(event) {
					event.stop();
					this.sendFilterForm(options, true);
				}.bind(this)).inject(filterForm);
		
		filterForm.inject($('configBox'));
		// set FilterBtn
		this.setFilterBtn(options);
	},
	setFilterBtn: function(options) {
		var myLabel = $(options.paginateElement).getChildren('ul'); 
		var myFilterBtn = new Element("li", {"id": "filterbtn", "class": "link", "html": "<span class='label label-info' >Filter</span>"});
		var myFx = new Fx.Slide(options.formElement, {
                                duration: 700,
                                transition: Fx.Transitions.Pow.easeOut
                });
		// hide by default
		myFx.hide();
		
		myFilterBtn.addEvent('click', function() {
			// Toggles between slideIn and slideOut twice:
			myFx.toggle();
                }.bind(this));
		myLabel.adopt(myFilterBtn);
		
		// create close buton
		var myCloseBtn = new Element("i", {"class": "icon-remove-sign float link"});
		myCloseBtn.addEvent('click', function() {
			// Toggles between slideIn and slideOut twice:
			myFx.toggle();
                }.bind(this));
		myCloseBtn.inject(options.formElement, 'top');
	}.bind(this),
	createFilter: function(options, item) {
		var clearDiv= new Element('div').addClass('row '+item[1][1]);
		var BoxCheck  = new Element('div').addClass('input-prepend hidden');
		var clabel = new Element('label').addClass('add-on checkbox');
	
		var checkboxField = new Element('input', {'disabled': true, 'type': 'checkbox', 'name': item[1][1]}).inject(clabel);
		clabel.inject(BoxCheck);
		BoxCheck.inject(clearDiv);
		var label = new Element('label').inject(clearDiv);
		var spanField = new Element('span', {'html': item[0][1]}).inject(label);
		if (item[2][1] == 'text') {
			var inputField= this.textFilterInit(options, item).inject(clearDiv);
		} else if (item[2][1] == 'select') {
			var mySelectField = this.selectFilterInit(options, item).inject(clearDiv);
		}
		return clearDiv;
	},
	setFilterValues: function(options){
		this.setOptions(options);
		// get active filters	
		var filters = options.filter.split('@');
		var isFiltered = false;
		filters.each(function(f) {
			// get filter name value
			var filter = f.split(':');
			var myname = filter[0]; 
			var myval = filter[1];			
			this.setCheckBox(options, myname, myval);
		}.bind(this));
	},
	isFiltered: function(options, name, index) {
		// get active filters	
		var filters = options.filter.split('@');
		var isFiltered = false;
		filters.each(function(f) {
			// get filter name value
			var filter = f.split(':');
			var myname = filter[0]; 
			var myval = filter[1];
			if (index) {
				// used for bool type
				if (myname == name && myval == index) isFiltered = true;	
			} else {
				if (myname == name) isFiltered = true;
			}
		});
		return isFiltered;	
	},
	getFilterValue: function(options, filtername) {
		// get active filters	
		var filters = options.filter.split('@');
		var myrval = false;
		filters.each(function(f) {
			// get filter name value
			var name = f.split(':')[0]; 
			var myval = f.split(':')[1];
			if (name == filtername) {
				myrval = myval;			
			}
		}.bind(this));
		return myrval;
	},
	textFilterInit: function(options,item) {
		this.setOptions(options);
		var name = item[1][1];
		if (this.isFiltered(options, name)) {
			var myval = this.getFilterValue(options, name);
			var inputField = new Element('input', {'name': name, 'type': 'text', 'value': myval, 'class': 'input-large search-query '+ name});
		} else {
			var inputField = new Element('input', {'type': 'text', 'value': '', 'class': 'input-large search-query '+item[1][1]});
		}
		inputField.addEvent('change', function() {
			// set checkbox ton activate / desactivate filter
			this.setCheckBox(options, name, inputField.get("value"));
		}.bind(this));
		return inputField;
	},
	selectFilterInit: function(options,item) {
		this.setOptions(options);
		var name = item[1][1];
		var mySelect =new Element('select', {'name': name, 'class': 'btn-primary '+ name});
		if (item[3][1] == 'func') {
			var callFunc=item[4][1];
			var sels = CaptainHook.callPHP('page', callFunc,callFunc, function(res) {
				
				var sl = new Element('option', {'value': -1, 'html': 'No restriction'}).inject(mySelect);
				var myRes = res.each(function (lres, index) {
					if (this.isFiltered(options, name)) {
						//set option to SELECTED
						var sEl = new Element('option', {'value': lres.login, 'html': lres.full_name, 'selected': 'selected'});
					
					} else {
						var sEl = new Element('option', {'value': lres.login, 'html': lres.full_name});
					}
					sEl.inject(mySelect);
				}.bind(this));
			}.bind(this)); 

		} else if (item[3][1] == 'bool') {
			var sv=item[4][1].split('|');
			var sl = new Element('option', {'value': -1, 'html': 'See All'}).inject(mySelect);
			var so = sv.each(function (oitem, index) {
				if (this.isFiltered(options, name, index)) {
					//sEl.setProperty('SELECTED': true);
					var sEl = new Element('option', {'value': index, 'html': oitem, 'selected':'selected'});
				} else {
					var sEl = new Element('option', {'value': index, 'html': oitem});
				}
				sEl.inject(mySelect);
			}.bind(this));
			
		}
		mySelect.addEvent('change', function() {
			// set checkbox ton activate / desactivate filter
			this.setCheckBox(options, name, mySelect.get("value"));
		}.bind(this));	
		return mySelect;
	},
	setCheckBox: function(options, name, myVal) {
		var myRow =$(options.formElement).getChildren('.'+name);	
		if (!myRow.contains($('i'))) {
			var sicon = new Element('i');
			var myIcon = myRow.adopt(sicon);
		} 
		var myIcon = $(options.formElement).getChildren('.'+ name +' i');	
		var checkbox =$(options.formElement).getChildren('.'+ name +' .input-prepend .add-on input'); 
		 
		var myFlag = $(options.formElement).getChildren('.'+ name +' i')
		if (myVal != -1 || myVal != '') {
			// set checkbox to active
			var cb = checkbox.set('value', myVal).setProperty('checked', true);
			myFlag.addClass('icon-ok');
		} else {
			// set checkbox to inactive
			var cb = checkbox.set('value', '').removeProperty('checked');
			myFlag.toggleClass('icon-ok');
		}
	},
	sendFilterForm: function(options, full) {
		this.setOptions(options);
		// get filter form
		var form = $(options.formElement).getChildren('div.row div.input-prepend label.add-on input');
		// get activated filters 
		var filters = [];
		form.each(function (el, index) {
			// select only active filters 
			if (el.getProperty('checked')) {
				// get request params  
				filters[index] = el.getProperty('name') + ':' + el.getProperty('value');
				
			}
		});
		if (filters.length > 0) {
			var filterPath='filter/';
			filters.each(function (item,index) {
				filterPath += item;
				if (index < (filters.length -1)) {
					filterPath += '@';
				}
			}.bind(this));
			if (!full) {
				// return only the filter path
				return filterPath;
			} else {
				// return full path
				var path= options.path;
				if (options.offset > 0) {
					path +='offset/' + options.offset + '/';	
				}
				if (options.maxrow != 10) {
					path +='maxrow/' + options.maxrow + '/';	
				}
				if (options.maxrow != 'created_desc') {
					path +='sort/' + options.sort + '/';	
				}
				path += filterPath;
				window.location.replace(path);
			}	
		} else {
			return false;
		}		
	},
	onComplete: function() {
		if (options.filter) {
			this.setFilterValues(options);
		}
	}	
});
