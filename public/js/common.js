function CommonFunc() {

	var output = {};

	output.barChartOptions = {
		height: 200,
        legend: { position: 'none' },
        bar: { groupWidth: '100%' },
        isStacked: true,
        colors: ['#BFAEEA','#A68EE1', '#542FB1'],
        chartArea: {left: '3%', top: '20%', width: '100%', height: 50},
        hAxis: {textPosition: 'none', gridlines:{color: 'transparent' }, minValue: 0},
        vAxis: {textPosition: 'none', gridlines:{count: 0, color: 'transparent'}, minValue: 0 },
        baselineColor: '#fff',
        width: '100%',
        height: '100%'
	}

	output.columnChartOptions = {
	    title:"Yearly Coffee Consumption by Country",
	    height:250,
	    legend: {position: "none",},
	    vAxis: {title: ""}, isStacked: true,
	    hAxis: {title: ""},
	    colors: ['#542FB1','#BFAEEA'],
	    chartArea: {left: "10%", top: "3%", height: "80%", width: "90%"},
	}

	output.comboChartOptions = {
    	title : 'Monthly Coffee Production by Country',
        seriesType: 'bars',
        series: {5: {type: 'line'}},
        colors: ['#BFAEEA','#e9e9e9', '#FF847C', '#E84A5F', '#474747'],
        height: 250,
        fontSize: 12,
        chartArea: { left: '10%', width: '90%', top: '3%', height: '80%' },
        vAxis: { title: '', gridlines:{ color: '#e9e9e9', count: 5 }, minValue: 0 },
        hAxis: { title: '', gridlines:{ color: '#e9e9e9', count: 5 }, minValue: 0 },
	}

	output.pieChartOptions_Dashboard = {
		height: 200,
	  	colors: ['#734ED0','#542FB1','#A68EE1','#BFAEEA','#8067AF'],
	  	legend: {position:'left'},
	  	pieHole: 0.4,
	  	chartArea: {left: "10%", top: "3%", height: "80%", width: "100%"},
	  	animation: {duration:800,easing:'in'}
	}

	output.pieChartOptions_Snapshot = {
        title: '',
        height: 400,
        fontSize: 12,
        colors:['#734ED0','#542FB1', '#A68EE1', '#BFAEEA', '#8067AF'],
        chartArea: { left: '5%', width: '90%', height: 350 },
    };

    output.actionRow = function(row, tableName) {
		var rowAction = '';
		if (row.active == 0) {
            rowAction += '<button class="btn btn-secondary" data-ng-click="updatePerson(' + row.id + ', {active: 1, tableName: \'' + tableName + '\'})"> <span style="padding: 32px;"> Make Active </span> </button>';
        } else if (row.active == 1) {
            rowAction += '<div class="dropdown">';
            rowAction += '	<button class="btn btn-secondary dropdown-toggle" type="button" data-toggle="dropdown"> Create Sales Receipt </button>';
            rowAction += '	<div class="dropdown-menu">';
            rowAction += '		<a class="dropdown-item" href="" data-ng-click="goToNewSalesReceipt(\'' + row.name + '\')"> Create Sales Receipt </a> ';
            if (tableName == 'customer') {
            	rowAction += '		<a class="dropdown-item" href="" data-ng-click="goToNewInvoice(\'' + row.name + '\')"> Create Invoice </a>';
            	rowAction += '		<a class="dropdown-item" href="" data-ng-click="goToNewPayment(\'' + row.name + '\')"> Receive Payment </a>';
            }
            rowAction += '		<a class="dropdown-item" href="" data-ng-click="goToNewExpense(\'' + row.name + '\')"> Create Expense </a>';
            rowAction += '		<a class="dropdown-item" href="" data-ng-click="showEditPersonDialog(' + row.id + ')"> Edit </a>';
            rowAction += '		<a class="dropdown-item" href="" data-ng-click="updatePerson(' + row.id + ', {active: 0, tableName: \'' + tableName + '\'})"> Make Inactive </a>';
            rowAction += '	</div>';
            rowAction += '</div>';
        }

        return rowAction;
	}

	output.appendRowToDataTable = function(dataTable, row) {
		dataTable.row.add(row);
		dataTable.draw();
	}

	output.cloneObject = function ( srcObject ) {
		return JSON.parse( JSON.stringify( srcObject ) );
	}

	output.editDeleteActionRow = function(row, tableName) {

		var rowAction 	=	'<div class="dropdown">';
			rowAction 	+=	'	<button class="btn btn-secondary dropdown-toggle" type="button" data-toggle="dropdown"> Edit </button>';
			rowAction 	+=	'	<div class="dropdown-menu">';
			rowAction 	+=	'		<a class="dropdown-item" href="" data-ng-click="showEdit' + tableName + 'Dialog(' + row.id + ')"> Edit </a>';
			rowAction 	+=	'		<a class="dropdown-item" href="" data-ng-click="showDeleteDialog(' + row.id + ')"> Delete </a>';
			rowAction 	+=	'	</div>';
			rowAction 	+=	'</div>';

		return rowAction;
	}

	output.drawBarChart = function(chartId, data, options) {	
      	var chart = new google.visualization.BarChart(document.getElementById(chartId));
      	chart.draw(data, options);
	}

	output.drawPieChart = function(chartId, data, options) {
		var chart = new google.visualization.PieChart(document.getElementById(chartId));
		chart.draw(data, options);
	}

	output.drawColumnChart = function(chartId, data, options) {
		var chart = new google.visualization.ColumnChart(document.getElementById(chartId));
    	chart.draw(data, options);
	}

	output.drawComboChart = function(chartId, data, options) {
		var bar = new google.visualization.ComboChart(document.getElementById(chartId));
        bar.draw(data, options);
	}

	output.goToTransaction = function(transactionId, transactionType, $state) {
		sessionStorage.preset = 1;
		sessionStorage.transactionId = transactionId;
		var states = $state.current.name.split('.');
		$state.go(states[0] + '.' + states[1] + '.' + states[2] + '.new-' + transactionType.toLowerCase());
	}

	output.initializeCheckBox = function(checkBoxSelector) {    // Used in Login page
		if($(checkBoxSelector).length){
			$(checkBoxSelector).iCheck({
				checkboxClass: 'icheckbox_square-blue',
				radioClass: 'iradio_square-blue',
			});
		}
	}

	output.initializeCheckRadioBox = function(checkBoxClass) {   // User in Balance sheet page
		$('.colors li').on('click', function() {
	      	var self = $(this);
	      	if (!self.hasClass('active')) {
	        	self.siblings().removeClass('active');

		        var skin = self.closest('.skin'),
		          	color = self.attr('class') ? '-' + self.attr('class') : '',
		          	checkbox = skin.data('icheckbox'),
		          	radio = skin.data('iradio'),
		          	checkbox_default = 'icheckbox_minimal',
		          	radio_default = 'iradio_minimal';

		        if (skin.hasClass(checkBoxClass)) {
		          	checkbox_default = 'icheckbox_square';
		          	radio_default = 'iradio_square';
		          	checkbox === undefined && (checkbox = 'icheckbox_square-red', radio = 'iradio_square-red');
		        }

		        checkbox === undefined && (checkbox = checkbox_default, radio = radio_default);

		        skin.find('input, .skin-states .state').each(function() {
		          	var element = $(this).hasClass('state') ? $(this) : $(this).parent(),
		            	element_class = element.attr('class').replace(checkbox, checkbox_default + color).replace(radio, radio_default + color);
		          		element.attr('class', element_class);
		        });

		        skin.data('icheckbox', checkbox_default + color);
		        skin.data('iradio', radio_default + color);
		        self.addClass('active');
	      	}
	    });

	    $('.' + checkBoxClass + ' input').iCheck({
	        checkboxClass: 'icheckbox_square-red',
	        radioClass: 'iradio_square-red',
	    });
	}

	output.initializeDataTable = function(tableSelector, columns, scope, compile) {
		var aoColumns = [];
		for (i = 0; i < columns.length; i++) {
			aoColumns.push( { sTitle: columns[i], bSortable: true } );
		}

		var tableData = {
			oLanguage: {
				sSearch: "Filter"
			},
			dom: 'Blfrtip',
	        buttons: [
	            'copy', 'csv', 'excel', 'pdf', 'print'
	        ],
			sPaginationType: "full_numbers",
			aoColumns: aoColumns,
			fnCreatedRow: function( nRow, aData, iDataIndex ) {
				compile(nRow)(scope);
			}
		};

		$(".icon-print").click(function(event) {
			event.stopPropagation();
			$(".buttons-print").trigger("click");
		});

		$(".icon-file-excel-o").click(function(event) {
			event.stopPropagation();
			$(".buttons-excel").trigger("click");
		});

		$(".icon-pencil3").click(function(event) {
			event.stopPropagation();
			event.preventDefault();
		});

		$("ul.list-inline li, ul.list-inline li a").click(function(event) {
			event.preventDefault();
			event.stopPropagation();
		});

		var dataTable = $(tableSelector).DataTable(tableData);

		$( window ).resize( function() {
    		dataTable.columns.adjust().draw();
		} );

		return dataTable;
	}

	output.initializeDatePicker = function(datePickerSelector, scope) {
		$( datePickerSelector ).datepicker({
			changeMonth: true,
			changeYear: true,
			dateFormat: 'yy-mm-dd'
		});

		setTimeout( function() {
			$( datePickerSelector ).datepicker( 'setDate', new Date() );
			$( datePickerSelector ).trigger( 'change' );
		}, 1000);

		$( datePickerSelector ).keydown(function(event) {
			event.preventDefault();
		});

		$('.ui-datepicker').wrap('<div class="dp-skin"/>');
	}

	output.initializeDropzone = function(dropzoneSelector) {
		$(dropzoneSelector).dropzone({ url: "/" });
	}

	output.initializeClickAndHide = function(selector) {
		$(document).mouseup(function (e) {
	      	var container = $(selector);

	      	if (!container.is(e.target) && container.has(e.target).length === 0) {
	        	container.hide();
	      	}
	    });
	}

	output.initializeSelect = function(selectSelector) {

		var $select = $(selectSelector).selectize({
			create: true,
			sortField: {
				field: 'text',
				direction: 'asc'
			},
			dropdownParent: 'body'
		});
		
	    $('#select-beast-selectized').trigger(jQuery.Event('keydown', {keyCode: 8}));
	}

	output.initializeSwitch = function(switchSelector) {
		var $html = $('html');

	    $('.switch:checkbox').checkboxpicker();
	    $(".switchBootstrap").bootstrapSwitch();

	    var elems = $(switchSelector);
	    $.each( elems, function( key, value ) {
	        var switchery = new Switchery($(this)[0], { className: "switchery", color: "#967ADC" });
	    });
	}

	output.initializeValidation = function( formSelector, onSuccess ) {
		if ( $( formSelector ).attr( "novalidate" ) != undefined ) {
			$( formSelector ).find( "input,select,textarea" ).not( "[type=submit]" ).jqBootstrapValidation( {
				preventSubmit: true, 
		        submitSuccess: onSuccess
			} );
		}
	}

	output.productServiceActionRow = function(row) {

		var rowAction 	=	'';
		if (row.active == 0) {
			rowAction 	+=	'<button class="btn btn-secondary" href="" data-ng-click="updateProductService(' + row.id + ', {active: 1, tableName: \'product_service\'})">  Make Active </button>';
		} else {
			rowAction 	+=	'<div class="dropdown">';
			rowAction 	+=	'	<button class="btn btn-secondary dropdown-toggle" type="button" data-toggle="dropdown"> Edit </button>';
			rowAction 	+=	'	<div class="dropdown-menu">';
			rowAction 	+=	'		<a class="dropdown-item" href="" data-ng-click="showEditProductServiceDialog(' + row.id + ')"> Edit </a>';
			rowAction 	+=	'		<a class="dropdown-item" href="" data-ng-click="updateProductService(' + row.id + ', {active: 0, tableName:\'product_service\'})"> Make Inactive </a>';
			rowAction   += 	'		<a class="dropdown-item" href="" data-ng-click="duplicateProductService(' + row.id + ')"> Duplicate </a>';
			rowAction 	+=	'	</div>';
			rowAction 	+=	'</div>';
		}
			
		return rowAction;
	}

	output.redrawDataTable = function(dataTable, data, rowCallback, tableName) {
		dataTable.clear().draw();
		for (var i = 0; i < data.length; i++) {
			dataTable.row.add(rowCallback(data[i], i, tableName));
		}

		if ( tableName == 'sales' || tableName == 'expense' )
			dataTable.columns.adjust().order( [ 1, 'desc' ] ).draw();
		else
			dataTable.columns.adjust().order( [ 1, 'asc' ] ).draw();
	}

	output.setPayeeTypeId = function(payeeType, payeeId) {
		localStorage.setItem('payeeId', payeeId);
		localStorage.setItem('payeeType', payeeType);
	}

	output.toggle = function(className,ele)
	{
		if($("."+className).css("display") === "none")
		{
		  $(ele).removeClass('icon-caret-up').addClass('icon-caret-down')
		}
		else
		{
		  $(ele).removeClass('icon-caret-down').addClass('icon-caret-up')
		}
		$("."+className).slideToggle("slow");
	}

	output.toggleEditHeader = function(id, id2) {
	   $(id).toggle();
	   $(id2).toggle();
	}

	output.toggleEditNotes = function(id) {
	    $(id).toggle()
	    //$(id).toggle(".addEditNotes").delay(500).fadeTo();
	    $('html, body').animate({
	      scrollTop: $("#addEditNotes").offset().top
	    }, 1000)
	}

	return output;

}