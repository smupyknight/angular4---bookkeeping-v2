<?php

namespace App\Helper;

use DB;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;

use App\Models\Customer;
use App\Models\Supplier;

class RestInputValidators {

    public static function accountValidator($data) {
        return Validator::make($data, 
            [
                'name'                      =>      'required|min:2|max:255' . ($data['REQUEST_METHOD'] == 'POST' ? '|unique:account' : ''),
                'account_detail_type'       =>      'required|exists:account_detail_type,name',
                'account_category_type'     =>      'required|exists:account_category_type,name'
            ]
        );
    }

    public static function accountCategoryTypeValidator($data) {
        return Validator::make($data, 
            [
                'name'                      =>      'required|min:2|max:255' . ($data['REQUEST_METHOD'] == 'POST' ? '|unique:account_category_type' : '')
            ]
        );
    }

    public static function accountDetailTypeValidator($data) {
        return Validator::make($data, 
            [
                'name'                      =>      'required|min:2|max:255' . ($data['REQUEST_METHOD'] == 'POST' ? '|unique:account_detail_type' : ''),
                'account_category_type'     =>      'required|exists:account_category_type,name'
            ]
        );
    }

    public static function attachmentValidator($data) {
        return Validator::make($data, 
            [
                'attachment_name'           =>      'required|min:2|max:255',
                'attachment_link'           =>      'required|url'
            ]
        );
    }

    public static function companyProfileValidator($data) {
        return Validator::make($data, 
            [
                'company_name'              =>      'required|min:2|max:255',
                'company_email'             =>      'email|max:255',
                'company_phone'             =>      'numeric',
                'business_id_no'            =>      'integer',
                'company_website'           =>      'url',
                'company_logo_link'         =>      'url'
            ]
        );
    }

    public static function customerValidator($data) {
        return Validator::make($data, 
            [
                'name'                      =>      'required|min:2|max:255' . ($data['REQUEST_METHOD'] == 'POST' ? '|unique:customer' : ''),
                'city'                      =>      'required|min:2|max:255',
                'email'                     =>      'required|email|max:255' . ($data['REQUEST_METHOD'] == 'POST' ? '|unique:customer' : ''),
                'phone'                     =>      'required|min:100000|numeric',
                'company'                   =>      'required|min:2|max:255',
                'country'                   =>      'required|min:2|max:255',
                'address1'                  =>      'required|min:2',
                'active'                    =>      ['required', Rule::in([0, 1])]
            ]
        );
    }

	public static function expenseValidator($data) {
		return Validator::make($data, 
            [
                'date'                      =>      'required|date',
                'payee'                     =>      'required',//|'exists:customer,name',
                'total'                     =>      'required|numeric',
                'account'                   =>      'required|exists:account,name',
                'transaction_type'          =>      'required|integer'
            ]
        );
	}

	public static function expenseAccountValidator($data) {
		return Validator::make($data, 
            [
                'rank'                      =>      'required|integer',
                'amount'                    =>      'required|numeric|min:0',
                'account'                   =>      'required|exists:account,name',
            ]
        );
	}

	public static function expenseItemValidator($data) {
		return Validator::make($data, 
            [
                'qty'                       =>      'integer|min:0',
                'rank'                      =>      'required|integer|min:1',
                'rate'                      =>      'numeric|min:0',
                'amount'                    =>      'required|numeric|min:0',
                'product_service'           =>      'required|exists:product_service,name',
            ]
        );
	}

    public static function invoiceValidator($data) {
        return Validator::make($data, 
            [
                'date'                      =>      'required|date',
                'customer'                  =>      'required|exists:customer,name',
                'total'                     =>      'required|numeric|min:0',
                'deposit'                   =>      'numeric|min:0',
                'due_date'                  =>      'required|date|after:date',
                'shipping'                  =>      'numeric|min:0',
                'sub_total'                 =>      'required|numeric|min:0',
                'discount_type'             =>      ['required', Rule::in(['Discount percent', 'Discount value'])],
                'discount_amount'           =>      'numeric|min:0',
                'transaction_type'          =>      ['required', 'integer', Rule::in([1])],
            ], 
            [
                'due_date.after'                     =>      'The due date must be after invoice date.'
            ]
        );
    }

    public static function invoiceItemValidator($data) {
        return Validator::make($data, 
            [
                'qty'                       =>      'numeric|min:0',
                'rank'                      =>      'required|integer|min:1',
                'rate'                      =>      'numeric|min:0',
                'amount'                    =>      'required|numeric|min:0',
                'product_service'           =>      'required|exists:product_service,name',
            ]
        );
    }

    public static function loginValidator($data) {
        return Validator::make($data, 
            [
                'email'                     =>      'required|email|max:255',
                'password'                  =>      'required|min:6',
            ]
        );
    }

    public static function paymentValidator($data) {
        return Validator::make($data, 
            [
                'date'                      =>      'required|date',
                'customer'                  =>      'required|exists:customer,name',
                'invoice_id'                =>      'integer|min:1|exists:invoice,transaction_id',
                'amount_received'           =>      'required|numeric|min:0',
                'transaction_type'          =>      ['required', 'integer', Rule::in([2])],
            ]
        );
    }

    public static function productCategoryValidator($data) {
        return Validator::make($data, 
            [
                'name'                      =>      'required|min:2|max:255' . ($data['REQUEST_METHOD'] == 'POST' ? '|unique:product_category' : '')
            ]
        );
    }

    public static function productServiceValidator($data) {
        return Validator::make($data, 
            [
                'sku'                       =>      'required|min:2|max:255' . ($data['REQUEST_METHOD'] == 'POST' ? '|unique:product_service' : ''),
                'name'                      =>      'required|min:2|max:255' . ($data['REQUEST_METHOD'] == 'POST' ? '|unique:product_service' : ''),
                'price'                     =>      'required|numeric',
                'product_category'          =>      'required|exists:product_category,name'
            ]
        );
    }

    public static function supplierValidator($data) {
        return Validator::make($data, 
            [
                'city'                      =>      'required|min:2|max:255',
                'name'                      =>      'required|min:2|max:255' . ($data['REQUEST_METHOD'] == 'POST' ? '|unique:supplier' : ''),
                'email'                     =>      'required|email|max:255' . ($data['REQUEST_METHOD'] == 'POST' ? '|unique:supplier' : ''),
                'phone'                     =>      'required|min:100000|numeric',
                'company'                   =>      'required|min:2|max:255',
                'country'                   =>      'required|min:2|max:255',
                'address1'                  =>      'required|min:2'
            ]
        );
    }

    public static function signupValidator($data) {
        return Validator::make($data, 
            [
                'name'                      =>      'required|max:255',
                'email'                     =>      'required|email|max:255|unique:user',
                'password'                  =>      'required|min:6',
            ]
        );
    }

    public static function tableNameValidator($data) {
        // Used when validating table names. If a table name that is not in this array comes, error message is returned.
        $availableTableNames = ['account', 'account_category_type', 'account_detail_type', 'company_profile', 'customer', 'expense', 'payee', 'product_category', 'product_service', 'sales', 'supplier', 'user_profile'];
        return Validator::make($data,
            [
                'tableName'                 =>      ['required', Rule::in($availableTableNames)],
                'transaction_type'          =>      ($data['REQUEST_METHOD'] == 'POST' ? 'required_if:table_name,sales,expense' : '')
            ]
        );
    }

    public static function userProfileValidator($data) {
        return Validator::make($data, 
            [
                'name'                      =>      'required|min:2|max:255',
                'city'                      =>      'required|min:2|max:255',
                'email'                     =>      'required|max:255|email',
                'mobile'                    =>      'required|min:100000|numeric',
                'address'                   =>      'required|min:2',
                'country'                   =>      'required|min:2|max:255',
                'new_password'              =>      'required|confirmed|min:6',
                'email_verified'            =>      'required|boolean',
                'mobile_verified'           =>      'required|boolean',
                'current_password'          =>      'required|min:6',
                'new_password_confirmation' =>      'required|min:6',
            ]
        );
    }












    public static function endPointIdValidator($input) {
        return Validator::make($input, 
            [
                'endPointId'        =>   'required|in:invoice,payment,expense,sales_receipt,getInvoiceById,getInvoiceNumber,massRetrieve,massUpdate,retrieveAccountDetailTypeNames,retrieveCustomerInvoices,setUserProfile,getUserName'
            ]
        );
    }

    public static function getInvoiceByIdEndPointValidator($data) {
        return Validator::make($data,
            [
                'invoiceId'         =>  'required|exists:sales,invoice_receipt_no'
            ]
        );
    }

    public static function getInvoiceNumberEndPointValidator( $data ) {
        return Validator::make( $data,
            [
            
            ]
        );
    }

    public static function getUserNameEndPointValidator($data) {
        return Validator::make($data,
            [
                
            ]
        );
    }

    public static function massRetrieveEndPointValidator( $data ) {
        $availableTableNames = [ 'account', 'account_category_type', 'account_detail_type', 'customer', 'expense', 'payee', 'product_category', 'product_service', 'sales', 'supplier' ];
        return Validator::make( $data,
            [
                'tableNames'            =>  'required',
                'tableNames.*'          =>  [ Rule::in( $availableTableNames ) ]
            ]
        );
    }

    public static function massUpdateEndPointValidator($data) {
        return Validator::make($data,
            [

            ]
        );
    }

    public static function retrieveAccountDetailTypeNamesEndPointValidator($data) {
        return Validator::make($data, 
            [

            ]
        );
    }

    public static function retrieveCustomerInvoicesEndPointValidator( $data ) {
        return Validator::make( $data,
            [
            
            ]
        );
    }

    public static function setUserProfileEndPointValidator($data) {
        return Validator::make($data, 
            [
                'new_password'          =>  'confirmed|min:6',
                'current_password'      =>  'required_with:new_password'
            ]
        );
    }

    public static function invoiceEndPointValidator($input) {
        if (isset($input['invoiceItems'])) {
            $input['invoiceItems'] = array_filter($input['invoiceItems']);
        }

        if ( $input[ 'REQUEST_METHOD' ] == 'GET' || $input[ 'REQUEST_METHOD' ] == 'DELETE' ) {
            return Validator::make($input,
                [
                    
                ]
            );
        } else {
            $validator = Validator::make($input,
                [
                    'transaction'                       =>  'required',
                    'invoice'                           =>  'required',
                    'invoiceItems'                      =>  'required|array',

                    'transaction.customer'              =>  'required|exists:customer,name',
                    'transaction.date'                  =>  'required|date',
                    'transaction.due_date'              =>  'required|date|after:sales.date',
                    'transaction.total'                 =>  'required|numeric',

                    'invoice.discount_type_id'          =>  'required|in:1,2,3',
                    'invoice.discount_amount'           =>  'required|numeric',
                    'invoice.sub_total'                 =>  'required|numeric',
                    'invoice.shipping'                  =>  'required|numeric',
                    'invoice.deposit'                   =>  'required|numeric',

                    'invoiceItems.*.rank'               =>  'required|integer',
                    'invoiceItems.*.item_type'          =>  'required|in:1,2',
                    'invoiceItems.0.product_service'    =>  'required|exists:product_service,name',
                    'invoiceItems.*.qty'                =>  'required_with:invoiceItems.*.product_service',
                    'invoiceItems.*.rate'               =>  'required_with:invoiceItems.*.product_service',
                    'invoiceItems.*.amount'             =>  'required_with:invoiceItems.*.product_service'
                ]
            );

            foreach ($input['invoiceItems'] as $key => $value) {
                $GLOBALS['INVOICE_ITEM'] = $value;
                if (!isset($value['product_service']))
                    continue;
                $validator->sometimes('invoiceItems.' . $key . '.qty', 'numeric', function($input) {
                    return !is_null($GLOBALS['INVOICE_ITEM']['product_service']);
                });
                $validator->sometimes('invoiceItems.' . $key . '.rate', 'numeric', function($input) {
                    return !is_null($GLOBALS['INVOICE_ITEM']['product_service']);
                });
                $validator->sometimes('invoiceItems.' . $key . '.amount', 'numeric', function($input) {
                    return !is_null($GLOBALS['INVOICE_ITEM']['product_service']);
                });
            }

            return $validator;
        }
    }

    public static function paymentEndPointValidator($input) {
        if ( $input[ 'REQUEST_METHOD' ] == 'GET' || $input[ 'REQUEST_METHOD' ] == 'DELETE' ) {
            return Validator::make($input,
                [
                    
                ]
            );
        }
        else {
            $validator = Validator::make($input,
                [
                    'transaction'                       =>  'required',
                    'payment'                           =>  'required',

                    'transaction.date'                  =>  'required|date',
                    'transaction.total'                 =>  'required|numeric',
                    'transaction.customer'              =>  'required|exists:customer,name',

                    'payment.account'                   =>  'required|exists:account,name|in:Cash,Accounts Receivable',
                    'payment.invoice_id'                =>  'required_if:payment.account,Accounts Receivable',
                ]
            );

            $validator->sometimes( 'payment.invoice_id', 'exists:sales,invoice_receipt_no', function( $input ) {
                return $input['payment']['account'] == 'Accounts Receivable';
            } );

            return $validator;
        }
    }

    public static function expenseEndPointValidator($input) {
        $payees = json_decode(json_encode(Customer::select('name')->union(Supplier::select('name'))->pluck('name')));
        if (isset($input['expenseItems'])) {
            $input['expenseItems'] = array_filter($input['expenseItems']);
        }
        if (isset($input['expenseAccounts'])) {
            $input['expenseAccounts'] = array_filter($input['expenseAccounts']);
        }
        if ( $input[ 'REQUEST_METHOD' ] == 'GET' || $input[ 'REQUEST_METHOD' ] == 'DELETE' ) {
            return Validator::make($input,
                [
                    
                ]
            );
        }
        else {
            $validator = Validator::make($input,
                [
                    'transaction'                       =>  'required',
                    'expenseAccounts'                   =>  'required|array',
                    'expenseItems'                      =>  'array',

                    'transaction.customer'              =>  ['required', Rule::in( $payees )],
                    'transaction.date'                  =>  'required|date',

                    'expenseAccounts.*.rank'            =>  'required|integer',
                    'expenseAccounts.0.account'         =>  'required|exists:account,name',
                    'expenseAccounts.*.amount'          =>  'required_with:expenseAccounts.*.account',

                    'expenseItems.*.rank'               =>  'required|integer',
                    'expenseItems.*.qty'                =>  'required_with:expenseItems.*.product_service',
                    'expenseItems.*.rate'               =>  'required_with:expenseItems.*.product_service',
                    'expenseItems.*.amount'             =>  'required_with:expenseItems.*.product_service'
                ]
            );

            foreach ($input['expenseAccounts'] as $key => $value) {
                $GLOBALS['EXPENSE_ACCOUNT'] = $value;
                $validator->sometimes('expenseAccounts.' . $key . '.amount', 'numeric', function($input) {
                    return !is_null($GLOBALS['EXPENSE_ACCOUNT']['account']);
                });
            }

            foreach ($input['expenseItems'] as $key => $value) {
                $GLOBALS['EXPENSE_ITEM'] = $value;
                $validator->sometimes('expenseItems.' . $key . '.qty', 'numeric', function($input) {
                    return !is_null($GLOBALS['EXPENSE_ITEM']['product_service']);
                });
                $validator->sometimes('expenseItems.' . $key . '.rate', 'numeric', function($input) {
                    return !is_null($GLOBALS['EXPENSE_ITEM']['product_service']);
                });
                $validator->sometimes('expenseItems.' . $key . '.amount', 'numeric', function($input) {
                    return !is_null($GLOBALS['EXPENSE_ITEM']['product_service']);
                });
            }

            return $validator;
        }
    }

    public static function salesReceiptEndPointValidator( $input ) {
        if (isset($input['salesReceiptItems'])) {
            $input['salesReceiptItems'] = array_filter($input['salesReceiptItems']);
        }

        if ( $input[ 'REQUEST_METHOD' ] == 'GET' || $input[ 'REQUEST_METHOD' ] == 'DELETE' ) {
            return Validator::make($input,
                [
                    
                ]
            );
        } else {
            $validator = Validator::make($input,
                [
                    'transaction'                            =>  'required',
                    'salesReceipt'                           =>  'required',
                    'salesReceiptItems'                      =>  'required|array',

                    'transaction.customer'                   =>  'required|exists:customer,name',
                    'transaction.date'                       =>  'required|date',
                    'transaction.due_date'                   =>  'required|date|after:sales.date',
                    'transaction.total'                      =>  'required|numeric',

                    'salesReceipt.discount_type_id'          =>  'required|in:1,2,3',
                    'salesReceipt.discount_amount'           =>  'required|numeric',
                    'salesReceipt.sub_total'                 =>  'required|numeric',
                    'salesReceipt.shipping'                  =>  'required|numeric',
                    'salesReceipt.deposit'                   =>  'required|numeric',

                    'salesReceiptItems.*.rank'               =>  'required|integer',
                    'salesReceiptItems.*.item_type'          =>  'required|in:1,2',
                    'salesReceiptItems.0.product_service'    =>  'required|exists:product_service,name',
                    'salesReceiptItems.*.qty'                =>  'required_with:invoiceItems.*.product_service',
                    'salesReceiptItems.*.rate'               =>  'required_with:invoiceItems.*.product_service',
                    'salesReceiptItems.*.amount'             =>  'required_with:invoiceItems.*.product_service'
                ]
            );

            foreach ($input['salesReceiptItems'] as $key => $value) {
                $GLOBALS['INVOICE_ITEM'] = $value;
                if (!isset($value['product_service']))
                    continue;
                $validator->sometimes('salesReceiptItems.' . $key . '.qty', 'numeric', function($input) {
                    return !is_null($GLOBALS['INVOICE_ITEM']['product_service']);
                });
                $validator->sometimes('salesReceiptItems.' . $key . '.rate', 'numeric', function($input) {
                    return !is_null($GLOBALS['INVOICE_ITEM']['product_service']);
                });
                $validator->sometimes('salesReceiptItems.' . $key . '.amount', 'numeric', function($input) {
                    return !is_null($GLOBALS['INVOICE_ITEM']['product_service']);
                });
            }

            return $validator;
        }
    }

}