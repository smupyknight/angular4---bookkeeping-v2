'use strict';

function MiscService( $q, $http, CRUDService ) {

	var output = {};
	var baseURL = 'saudisms/whm/api/misc';

	output.getInvoiceById = function( invoiceId ) {

		var deferred = $.Deferred();

		$http.get( baseURL + '/get_invoice_by_id?endPointId=getInvoiceById&invoiceId=' + invoiceId )
			.then
			(
				function( response ) {
					deferred.resolve( response );
				},
				function( response ) {
					deferred.reject( response );
				}
			);

		return deferred.promise();
	}

	output.getInvoiceNumber = function( salesId ) {

		var deferred = $.Deferred();

		$http.get( baseURL + '/get_invoice_number?endPointId=getInvoiceNumber&salesId=' + salesId )
			.then
			(
				function( response ) {
					deferred.resolve( response );
				},
				function( response ) {
					deferred.reject( response );
				}
			)

		return deferred.promise();
	}

	output.getUserName = function() {

		var deferred = $.Deferred();

		$http.get( baseURL + '/get_user_name?endPointId=getUserName' )
			.then
			(
				function( response ) {
					deferred.resolve( response );
				},
				function( response ) {
					deferred.reject( response );
				}
			);

		return deferred.promise();
	}

	output.setUserProfile = function( data ) {

		var deferred = $.Deferred();

		data.endPointId = 'setUserProfile';

		$http.put( baseURL + '/set_user_profile', data )
			.then
			(
				function( response ) {
					deferred.resolve( response );
				},
				function( response ) {
					deferred.reject( response );
				}
			);

		return deferred.promise();
	}

	output.massRetrieve = function( tableNames ) {

		var deferred = $.Deferred();

		$http.get( baseURL + '/mass_retrieve' + '?endPointId=massRetrieve', { params: { tableNames: JSON.stringify( tableNames ) } } )
			.then
			(
				function( response ) {
					deferred.resolve( response );
				},
				function( response ) {
					deferred.reject( response );
				}
			);

		return deferred.promise();
	}

	output.massUpdate = function( data ) {

		var deferred = $.Deferred();

		data.endPointId = "massUpdate";

		$http.put( baseURL + '/mass_update', data )
			.then
			(
				function( response ) {
					deferred.resolve( response );
				},
				function( response ) {
					deferred.reject( response );
				}
			);

		return deferred.promise();
	}
	
	output.retrieveAccountDetailTypeNames = function( accountCategoryType ) {

		var deferred = $.Deferred();

		$http.get( baseURL + '/retrieve_account_detail_type_names?endPointId=retrieveAccountDetailTypeNames&accountCategoryType=' + accountCategoryType )
			.then
			(
				function( response ) {
					deferred.resolve( response.data.content );
				},
				function( response ) {
					deferred.reject( response );
				}
			);

		return deferred.promise();
	}

	output.retrieveCustomerInvoices = function( customerName ) {

		var deferred = $.Deferred();

		$http.get( baseURL + '/retrieve_customer_invoices?endPointId=retrieveCustomerInvoices&customerName=' + customerName )
			.then
			(
				function( response ) {
					deferred.resolve( response );
				},
				function( resposne ) {
					deferred.reject( response );
				}
			);

		return deferred.promise();
	}

	output.extractNameList = function( list ) {
		var result = [];
		for ( var i = 0; i < list.length; i++) {
			result.push( list[i].name );
		}
		return result;
	}

	output.extractUniqueList = function( list ) {
		var result = [ {name: '', id: 0} ];
		var defaultIndex = 0;
		for ( var i = 0; i < list.length; i++) {
			if ( defaultIndex >= list[i].id - 1 )
				result.push( list[i] );
			else {
				result.push( { name: '', id: defaultIndex } );
				i --;
			}
			defaultIndex ++;
		}
		return result;
	}

	output.extractUniqueNameList = function( list ) {
		return output.extractNameList( output.extractUniqueList( list ) );
	}

	return output;
}

angular
	.module( 'bookkeeping' )
	.factory( 'MiscService', [ '$q', '$http', 'CRUDService', MiscService ] );