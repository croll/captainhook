var Multiselect = new Class({
		Implements: [Options,Events],
		options: {
			list: 'list',
			elementClass: 'chooseme',
			selectedClass: 'selected',
			disabledClass: 'disabled',
			maximum: 0,
			onComplete: null
		},
		initialize: function(options) {

			this.setOptions(options);

			this.listEl = $(options.list);
			this.listElements = this.listEl.getElements('.'+this.options.elementClass);
			this.selectedIndex = null;
			this.elementsSelected=[];

			var num = 0;
			this.listElements.each(function(el,i){

				if (typeof $(el).onselectstart != 'undefined') {
					$(el).addEvent('selectstart',function() { return false; });
				} else if (typeof $(el).style.MozUserSelect != 'undefined') {
					$(el).setStyle('MozUserSelect', 'none');
				} else if (typeof $(el).style.WebkitUserSelect != 'undefined') {
					$(el).setStyle('WebkitUserSelect', 'none');
				} else if (typeof $(el).unselectable  != 'undefined') {
					$(el).setProperty('unselectable','on');
				}

				$(el).setProperty('unselectable', 'on');
				$(el).setProperty('sel', options.selectedClass);
				$(el).setProperty('rel', num);
				num++;
				if ($(el).hasClass(this.options.disabledClass))
					return false;
				$(el).addEvent('mousedown', function(){
					this.dragging = true;
					this.selectedIndex = $(el).getProperty('rel');
					if($(el).hasClass(this.options.selectedClass)){
						$(el).removeClass(this.options.selectedClass);
						this.highlight = false;
					}else{
						$(el).addClass(this.options.selectedClass);
						this.highlight = true;
					}
				}.bind(this));

				$(el).addEvent('mouseup', function(){
					this.dragging = false;
					this.fireEvent('onComplete');
				}.bind(this));

				$(el).addEvent('mouseout', function(){
				}.bind(this));

				$(el).addEvent('mouseover', function(){
					if(this.dragging){
						var value = $(el).getProperty('rel')
						var start = Math.min(this.selectedIndex,value); 
						var end = Math.max(this.selectedIndex,value); 
						for (i=0;i<this.listElements.length;i++) {
							if (i>=start && i<=end) {
								if (this.highlight && !this.listElements[i].hasClass(this.options.disabledClass)) {
									this.listElements[i].addClass(this.options.selectedClass);
									this.elementsSelected.include(this.listElements[i].get("id"));
								} else if (!this.listElements[i].hasClass(this.options.disabledClass)) {
									this.listElements[i].removeClass(this.options.selectedClass);
								  this.elementsSelected.erase(this.listElements[i].get("id"));
								}
							}
						}
					}
				}.bind(this));

			}.bind(this));
		}
});
