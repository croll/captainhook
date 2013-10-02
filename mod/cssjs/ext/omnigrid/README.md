OmniGrid
========

Advanced DataGrid for Mootools

![Screenshot](http://github.com/eerne/omnigrid/raw/master/omnigrid.png)

### Features

 * accordion
 * very light but rich data grid
 * pagination
 * server-side and client-side sorting
 * resizable columns

About
-----

The OmniGrid component is inspired by two similar components: [FlexGrid jQuery](http://groups.google.com/group/flexigrid) and [phatfusion:sortableTable](http://www.phatfusion.net/sortabletable) and partly uses their source code. It was developed due to a lack of powerful DataGrid for Mootools 1.2 library. The author is Marko Šantić from Omnisdata company.

Omnigrid - Advanced DataGrid for Mootools by Omnisdata Ltd http://www.omnisdata.com/ is licensed under a MIT License.

Demos
-----

- Online demo [omnisdata.com/omnigrid](http://www.omnisdata.com/omnigrid/)


Usage
-----

	<link rel="stylesheet" media="screen" href="omnigrid.css" type="text/css" />
	<script type="text/javascript" src="mootools/mootools-1.2.4-nc.js"></script>
	<script type="text/javascript" src="mootools/mootools-1.2.4-more.js"></script>
	<script type="text/javascript" src="omnigrid.js"></script>
	
	<script type="text/javascript">
	
		function onGridSelect(evt){
			var str = 'row: ' + evt.row + ' indices: ' + evt.indices;
			str += ' id: ' + evt.target.getDataByRow(evt.row).id;
			alert(str);
		}
		
		function gridButtonClick(button, grid){
			alert(button);
		}
		
		var cmu = [{
			header: 'ID',
			dataIndex: 'help_category_id',
			dataType: 'number'
		},
		{
			header: 'Parent ID',
			dataIndex: 'parent_category_id',
			dataType: 'number',
			width: 50
		},
		{
			header: 'Name',
			dataIndex: 'name',
			dataType: 'string',
			width: 200
		}];	
		
		window.addEvent('domready', function(){
		
		datagrid = new omniGrid('mygrid', {
			columnModel: cmu,
			buttons : [
				{name: 'Add', bclass: 'add', onclick : gridButtonClick},
				{name: 'Delete', bclass: 'delete', onclick : gridButtonClick},
				{separator: true},
				{name: 'Duplicate', bclass: 'duplicate', onclick : gridButtonClick}
			],
			url: 'data.php',
			perPageOptions: [10, 20, 50, 100, 200],
			perPage: 10,
			page: 1,
			pagination: true,
			serverSort: true,
			showHeader: true,
			alternaterows: true,
			showHeader: true,
			sortHeader: false,
			resizeColumns: true,
			multipleSelection: true,
			
			// uncomment this if you want accordion behavior for every row
			/*
			accordion: true,
			accordionRenderer: accordionFunction,
			autoSectionToggle: false,
			*/
			
			width: 600,
			height: 400
		});
		
		datagrid.addEvent('click', onGridSelect);
		
		});
	
	</script>
	
	<div id="mygrid" ></div>


