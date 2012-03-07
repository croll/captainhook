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
		onComplete: null
	},
	initialize: function(options) {
		this.setOptions(options);
		this.setOptions(options);
		this.createFilters(options);
		if (options.filter) this.populateFilters(options);
	},
	createFilters: function(options) {
		this.setOptions(options);
		var filterForm = new Element('form', {'id': options.formElement, 
				'html': '<h4>Filter By</h4>'});
		var filterEl = options.filters.each(function (item, index) {
			clearDiv = this.createFilter(options, item).inject(filterForm);
		}.bind(this));
		
		//add submit button
		var mySubmit=new Element('input', {'class':'float btn btn-primary clearfix',
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
	populateFilters: function(options) {

	},
	setFilterBtn: function(options) {
		var myLabel = $(options.paginateElement).getChildren('ul'); 
		var myFilterBtn = new Element('li', {'id': 'filterbtn', 'class': 'label link info', 'html': 'Filter'});
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
		
	},
	hasFilter: function(options, filter) {
		
	},
	createFilter: function(options, item) {
		var clearDiv= new Element('div').addClass('clearfix');
		var divField = new Element('div').addClass('input');
		var BoxCheck  = new Element('div').addClass('input-prepend');
		var clabel = new Element('label').addClass('add-on');
	
		var checkboxField = new Element('input', {'disabled': true, 'type': 'checkbox', 'name': item[1][1]}).inject(clabel);
		clabel.inject(BoxCheck);
		BoxCheck.inject(divField);
		var label = new Element('label').inject(divField);
		var spanField = new Element('span', {'html': item[0][1]}).inject(label);
		if (item[2][1] == 'text') {
			var inputField= this.textFilterInit(options, item).inject(divField);
		} else if (item[2][1] == 'select') {
			var mySelectField = this.selectFilterInit(options, item).inject(divField);
		}
		divField.inject(clearDiv);
		return clearDiv;
	},
	textFilterInit: function(options,item) {
		this.setOptions(options);
		var inputField = new Element('input', {'type': 'text', 'value': '', 'class': 'large'});
		
		return inputField;
	},
	selectFilterInit: function(options,item) {
		this.setOptions(options);
		var mySelect =new Element('select', {'name': 'select', 'class': 'btn btn-primary'});
		if (item[3][1] == 'func') {
			var callFunc=item[4][1];
			var sels = CaptainHook.callPHP('page', callFunc,callFunc, function(res) {
				
				var sl = new Element('option', {'value': -1, 'html': 'No restriction'}).inject(mySelect);
				var myRes = res.each(function (item, index) {
					var sEl = new Element('option', {'value': item.login, 'html': item.full_name}).inject(mySelect);
				}.bind(this));
			}.bind(this)); 

		} else if (item[3][1] == 'bool') {
			var sv=item[4][1].split('|');
			var sl = new Element('option', {'value': -1, 'html': 'See All'}).inject(mySelect);
			var so = sv.each(function (item, index) {
				var sEl = new Element('option', {'value': index, 'html': item}).inject(mySelect);
			}.bind(this));

		}
		mySelect.addEvent('change', function() {
			// set checkbox ton activate / desactivate filter
			var myVal = this.get('value');
			if (myVal != -1) {
				// set checkbox to active
				var cb = this.getParent('div.input').getChildren('div.input-prepend label.add-on input').set('value', myVal).setProperty('checked', true);
			} else {
				// set checkbox to inactive
				var cb = this.getParent('div.input').getChildren('div.input-prepend label.add-on input').set('value', '').removeProperty('checked');
			}
			
		});	
		return mySelect;
	},
	sendFilterForm: function(options, full) {
		this.setOptions(options);
		// get filter form
		var form = $(options.formElement).getChildren('div.clearfix div.input div.input-prepend label.add-on input');
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
			CaptainHook.Message.show('You must set at list one filter');
		}		
	}
	
});
