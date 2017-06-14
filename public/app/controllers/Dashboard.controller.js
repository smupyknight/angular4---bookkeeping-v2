'use strict';

function DashboardCtrl( $scope, CRUDService, MiscService, RprtService ) {

    $scope.accounts             =   [];

    $scope.totalLoss            =   0;
    $scope.totalSales           =   0;
    $scope.totalProfit          =   0;
    $scope.totalExpense         =   0;

    $scope.thisYearInfo         =   {};

    $scope.salesPeriod          =   'Last 30 days';
    $scope.expensePeriod        =   'Last 30 days';
    $scope.profitLossPeriod     =   'Last 30 days';

    $scope.drawIncomeChart = function() {
        RprtService.retrieveReport( '/chart/income_bar' ).then( function( response ) {

            var content = response.data.content;
            var data = google.visualization.arrayToDataTable(
                [
                    [ 'Results', 'OPEN INVOICE', 'OVERDUE', 'PAID LAST 30 DAYS',{ role: 'annotation' } ],
                    [ '', content.open_invoices - content.over_due, content.over_due, content.paid_last_30_days, '' ]
                ]
            );

            $scope.overDue             =   content.over_due;
            $scope.openInvoices        =   content.open_invoices;
            $scope.paidLast30Days      =   content.paid_last_30_days;

            CommonFunc().drawBarChart( 'incomeChart', data, CommonFunc().barChartOptions );
        } );
    }

    $scope.drawExpenseChart = function() {
        RprtService.retrieveReport( '/chart/expense/dashboard', $scope.expensePeriod ).then( function( response ) {
            var content = response.data.content;
            
            MiscService.massRetrieve( [ 'account' ] ).done( function( response ) {
                var data = new google.visualization.DataTable();

                $scope.accounts = MiscService.extractNameList( response.data.content.account );
                data.addColumn( 'string', 'Task' );
                data.addColumn( 'number', 'Hours per Day' );

                $scope.totalExpense = 0;
                for ( var i = 0; i < content.length; i++ ) {
                    data.addRows(
                        [
                            [ '$' + content[ i ].expense + ' ' + $scope.accounts[ content[ i ].category_id ], content[ i ].expense ]
                        ]
                    );
                    $scope.totalExpense += content[ i ].expense;
                }

                CommonFunc().drawPieChart( 'expenseChart', data, CommonFunc().pieChartOptions_Dashboard );
            } );
        } );
    }

    $scope.drawSalesChart = function() {
        RprtService.retrieveReport( '/chart/sales', $scope.salesPeriod ).then( function( response ) {

            var sales       =   [ [ 'Date', 'Sales' ] ];
            var content     =   response.data.content;

            $scope.totalSales = 0;
            for ( var i = 0; i < content.length; i++ ) {
                sales.push( [ content[ i ].display, content[ i ].sales ] );
                $scope.totalSales += content[ i ].sales;
            }

            var data = google.visualization.arrayToDataTable( sales );
            var options = JSON.parse( JSON.stringify( CommonFunc().comboChartOptions ) );
            options.height = 300;
            
            CommonFunc().drawComboChart( 'salesChart', data, options );
        });
    }

    $scope.drawProfitAndLossChart = function() {
        RprtService.retrieveReport( '/chart/profit_loss', $scope.profitLossPeriod ).then( function( response ) {

            var content     =   response.data.content;
            var profitLoss  =   [ [ 'Date', 'Profit', 'Loss' ] ];

            $scope.totalLoss    =   0;
            $scope.totalProfit  =   0;

            for ( var i = 0; i < content.length; i++ ) {
                profitLoss.push( [ content[ i ].display, content[ i ].profit, content[ i ].loss ] );
                $scope.totalLoss    +=  content[ i ].loss;
                $scope.totalProfit  +=  content[ i ].profit;
            }

            var data = google.visualization.arrayToDataTable( profitLoss );
            CommonFunc().drawColumnChart( 'profitAndLossChart', data, CommonFunc().columnChartOptions );
        } );
    }

    $scope.fetchDashboardData = function() {
        RprtService.retrieveReport( '/chart/dashboard_home' ).then( function( response ) {
            $scope.thisYearInfo = response.data.content;
        } );
    }

    $scope.drawDashboardCharts = function() {
        $scope.drawSalesChart();
        $scope.drawIncomeChart();
        $scope.drawExpenseChart();
        $scope.drawProfitAndLossChart();

        $scope.fetchDashboardData();
    }

	if ( typeof google !== 'undefined' && typeof google.visualization !== 'undefined' ) {
		$scope.drawDashboardCharts();
	}

    $scope.initialize = function() {
        CommonFunc().initializeSwitch( '.switchery' );

        if ( typeof google != 'undefined' ) {
            google.load( 'visualization', '1.0', 
                {
                    callback: function() {
                        $scope.drawDashboardCharts();
                    }, 
                    packages:[ 'corechart' ]
                }
            );
        }

        $scope.today = ( new Date() ).toString();
        $scope.companyName = sessionStorage.companyName;
    }

    $scope.initialize();
}

angular
	.module( 'bookkeeping' )
	.controller( 'DashboardCtrl', [ '$scope', 'CRUDService', 'MiscService', 'RprtService', DashboardCtrl ] );