'use strict';

function SejPersonDialog( CRUDService, MiscService ) {

	var output = {}

	output.transclude = true;
	output.templateUrl = 'app/directives/SejPersonDialog/SejPersonDialog.html';
	output.link = function( scope, element, attrs ) {

		scope.newPerson = { active: 1, account: 'Sales of Product Income', country: '', balance: 0 };
		scope.person = {};

		scope.createPerson = function() {
	        CRUDService.create( scope.person ).done( function( response ) {
	        	if ( scope.dataTable ) {
	        		CommonFunc().appendRowToDataTable( scope.dataTable, scope.generateRow( response.data.content, 0, scope.targetTableName ) );
	        	} else {
	        		MiscService.massRetrieve( [ 'customer', 'supplier' ] ).done( function( response ) {
	        			var content = response.data.content;

	        			if ( scope.personListType == 'customer')
	        				scope.customerNames = MiscService.extractNameList( content.customer );
	        			else if ( scope.personListType == 'payee' )
	        				scope.customerNames = MiscService.extractNameList( content.customer ).concat( MiscService.extractNameList( content.supplier ) );
	        		} );
	        	}

	        	$( '#' + attrs.id ).modal( 'hide' );
	        	toastr.success( 'Successfully created' );
	        	scope.person = CommonFunc().cloneObject( scope.newPerson );
	        } ).fail( function( response ) {
            	scope.personDialogErrorMessages = response.data.content;
            } );
		}

		scope.updatePerson = function( id, data ) {
			CRUDService.update( id, data ).done( function( response ) {
	        	$( '#' + attrs.id ).modal( 'hide' );
	        	toastr.success( "Successfully updated" );
	            CommonFunc().redrawDataTable( scope.dataTable, response.data.content, scope.generateRow, scope.targetTableName );
	        });
		}

		scope.showCreatePersonDialog = function() {
	    	scope.isEditDialog = 0;
	        scope.personDialogErrorMessages = [];
	    	scope.person = CommonFunc().cloneObject( scope.newPerson );
	    	$( '#' + attrs.id ).modal( 'show' );
	    }

	    scope.showEditPersonDialog = function( personId ) {
	    	CRUDService.retrieve( scope.targetTableName, personId ).then( function( response ) {
	    		scope.isEditDialog = 1;
	            scope.personDialogErrorMessages = [];
	    		scope.person = response.data.content;
	    		scope.person.tableName = scope.targetTableName;
	    		$( '#' + attrs.id ).modal( 'show' );
	    	} );
	    }

		function initialize() {
			scope.newPerson.tableName = scope.targetTableName;
			scope.person = CommonFunc().cloneObject( scope.newPerson );
			CommonFunc().initializeValidation( "form.form-horizontal.form-person", function( $form, errors ) {
	        	if ( scope.isEditDialog ) {
	        		scope.updatePerson( scope.person.id, scope.person );
	        	} else {
	        		scope.$eval( attrs.createEvent )();
	        	}
	        } );
		}
		
		initialize();
	}

	return output;
}

angular
	.module( 'bookkeeping' )
	.directive( 'sejPersonDialog', [ 'CRUDService', 'MiscService', SejPersonDialog ] );