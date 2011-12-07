//  -*- mode:js; tab-width:2; c-basic-offset:2; -*-
var CHPaginator =new Class({

		Implements: Events,

		initialize: function(elem_prev, elem_next, elem_nums, elem_num) {
				var me=this;
				this.elem_prev=elem_prev;
				this.elem_next=elem_next;
				this.elem_nums=elem_nums;
				this.elem_num=elem_num;
				this.rowsperpage=10;
				this.rowscount=0;
				this.page=1;

				this.elem_prev.set('style', 'cursor:pointer;'+this.elem_prev.get('style'));
				this.elem_next.set('style', 'cursor:pointer;'+this.elem_prev.get('style'));
				this.elem_prev.addEvent('click', function() {
						me.setCurPage(me.page-1);
				});
				this.elem_next.addEvent('click', function() {
						me.setCurPage(me.page+1);
				});

				this.updateDisplay();
		},

		setRowsPerPage: function(rowsperpage) {
				this.rowsperpage=rowsperpage;
				this.updateDisplay();
		},

		setRowsCount: function(rowscount) {
				this.rowscount=rowscount;
				this.updateDisplay();
		},

		setCurPage: function(page) {
				var pagescount = Math.floor((this.rowscount-1) / this.rowsperpage) +1;
				if ((page >= 1) && (page <= pagescount) && (page != this.page)) {
						this.page=page;
						this.fireEvent('page', {
								'page': page,
								'startrow': (page-1)*this.rowsperpage,
								'rowsperpage': this.rowsperpage
						});
						this.updateDisplay();
				}
		},

		updateDisplay: function() {
				var pagescount = Math.floor((this.rowscount-1) / this.rowsperpage) +1;

				if (this.page > 1) this.elem_prev.removeClass('disabled');
				else this.elem_prev.addClass('disabled');

				if (this.page < pagescount) this.elem_next.removeClass('disabled');
				else this.elem_next.addClass('disabled');

				this.elem_nums.empty();
				if (this.page > 4) {
						for (var i=0; i<3; i++) this.displayNum(i);
						this.elem_nums.adopt(new Element('span', { text: ' ... ' }));
						for (var i=(this.page-3); i<=this.page; i++) this.displayNum(i);
				} else {
						for (var i=0; i<=this.page; i++) this.displayNum(i);
				}

				if (this.page < (pagescount - 4)) {
						for (var i=(this.page+1); i<(this.page+2); i++) this.displayNum(i);
						this.elem_nums.adopt(new Element('span', { text: ' ... ' }));
						for (var i=(pagescount-3); i<pagescount; i++) this.displayNum(i);
				} else {
						for (var i=this.page; i<pagescount; i++) this.displayNum(i);
				}
		},

		displayNum: function(i) {
				var me=this;
				var elem_num = this.elem_num.clone();
				elem_num.set('text', i+1);
				elem_num.addClass('chpaginator_num');
				if ((this.page-1) == i) elem_num.addClass('selected');
				elem_num.set('style', 'cursor:pointer;'+elem_num.get('style'));
				elem_num.set('style', 'cursor:pointer;'+elem_num.get('style'));
				elem_num.addEvent('click', function(e) {
						var page=parseInt(e.target.get('text'));
						me.setCurPage(page);
				});
				this.elem_nums.adopt(elem_num);
		},
});

