'use strict';

function TrxnService( $q, $http ) {

	var output = {};
	var baseURL = 'saudisms/whm/api/trxn';

	output.create = function( endPoint, data ) {

		var deferred = $.Deferred();

		$http.post( baseURL + '/' + endPoint, data )
			.then
			(
				function ( response ) {
					deferred.resolve( response );
				},
				function ( response ) {
					deferred.reject( response );
				}
			);

		return deferred.promise();
	}

	output.retrieve = function( endPoint, transactionId ) {

		var deferred = $.Deferred();
		var URL = baseURL + '/' + endPoint + '/' + transactionId + '?endPointId=' + endPoint;

		$http.get( URL )
			.then
			(
				function ( response ) {
					deferred.resolve( response );
				},
				function ( response ) {
					deferred.reject( response );
				}
			);

		return deferred.promise();
	}

	output.update = function( endPoint, transactionId, data ) {

		var deferred = $.Deferred();

		$http.put( baseURL + '/' + endPoint + '/' + transactionId, data )
			.then
			(
				function ( response ) {
					deferred.resolve( response );
				},
				function ( response ) {
					deferred.reject( response );
				}
			);

		return deferred.promise();
	}

	output.delete = function( endPoint, transactionId ) {

		var deferred = $.Deferred();
		var endPointId = endPoint == 'sales_receipt' ? 'salesReceipt' : endPoint;

		$http.delete( baseURL + '/' + endPoint + '/' + transactionId, { params: { endPointId: endPointId } } )
			.then
			(
				function( response ) {
					deferred.resolve( response );
				},
				function( response ) {
					deferred.resolve( response );
				}
			);

		return deferred.promise();
	}

	return output;
}

angular
	.module( 'bookkeeping' )
	.factory( 'TrxnService', [ '$q', '$http', TrxnService ] );