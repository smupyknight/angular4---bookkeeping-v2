'use strict';

function ProductServiceCtrl( $scope, $compile, CRUDService, MiscService ) {

	$scope.isEditDialog = 0;
	$scope.productService = {};

	$scope.duplicateProductService = function( productServiceId ) {
		CRUDService.retrieve( 'product_service', productServiceId ).done( function( response ) {
			var product = response.data.content;
			product.product_category = $scope.productCategoryUINames[ product.product_category_id ];
			product.tableName = 'product_service';
			product.foreign_keys = [ 'product_category' ];
			product.name += '-1';
			product.sku += '-1';
			CRUDService.create( product ).done(function( response ) {
				toastr.success( 'Successfully duplicated' );
				CommonFunc().appendRowToDataTable( $scope.dataTable, $scope.generateProductServiceRow( response.data.content ) );
			} );
		} );
	}

	$scope.generateProductServiceRow = function( row ) {
		return [ row.id, row.name, row.sku, $scope.productCategoryUINames[ row.product_category_id ], row.price, CommonFunc().productServiceActionRow( row ) ];
	}

	$scope.initialize = function() {
		$scope.dataTable = CommonFunc().initializeDataTable( '#datatable', [ "ID", "Name", "SKU", "Category", "Sales Price", "Action" ], $scope, $compile );

		MiscService.massRetrieve( [ 'product_category', 'product_service' ] ).done( function( response ) {
			var content = response.data.content;

			$scope.productCategoryNames = MiscService.extractNameList( content.productCategory );
			$scope.productCategoryUINames = MiscService.extractUniqueNameList( content.productCategory );
			$scope.productServices = content.productService;
			$scope.newProductService.product_category = content.productCategory[0].name;

			CommonFunc().redrawDataTable( $scope.dataTable, content.productService, $scope.generateProductServiceRow );
		} );
	}

	$scope.initialize();
}

angular
	.module( 'bookkeeping' )
	.controller( 'ProductServiceCtrl', [ '$scope', '$compile', 'CRUDService', 'MiscService', ProductServiceCtrl ] );