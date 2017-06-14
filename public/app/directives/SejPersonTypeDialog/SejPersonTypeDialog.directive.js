'use strict';

function SejPersonTypeDialog() {

	var output = {}

	output.transclude = true;
	output.templateUrl = 'app/directives/SejPersonTypeDialog/SejPersonTypeDialog.html';
	output.link = function( scope, element, attrs ) {
		
		$( document ).mouseup( function( e ) {
			var container = $( '.slt-open-dd' );

	      	if ( !container.is( e.target ) && container.has( e.target ).length === 0 ) {
	        	container.hide();
	      	}
		} );

		scope.setPersonType = function() {
			scope.newPerson.tableName = scope.targetTableName;
		}
	}

	return output;
}

angular
	.module( 'bookkeeping' )
	.directive( 'sejPersonTypeDialog', SejPersonTypeDialog );