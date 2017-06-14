<?php

use Illuminate\Database\Seeder;

class AccountCategoryTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
    	DB::table('account_category_type')->truncate();


        // DB::table('account_category_type')->insert( [ 'name' => 'Accounts Receivable (A/R)' ] );
        // DB::table('account_category_type')->insert( [ 'name' => 'Current Assets' ] );
        // DB::table('account_category_type')->insert( [ 'name' => 'Cash and cash equivalents' ] );
        // DB::table('account_category_type')->insert( [ 'name' => 'Fixed assets' ] );
        // DB::table('account_category_type')->insert( [ 'name' => 'Non-current assets' ] );
        // DB::table('account_category_type')->insert( [ 'name' => 'Accounts payable (A/P)' ] );
        // DB::table('account_category_type')->insert( [ 'name' => 'Credit card' ] );
        // DB::table('account_category_type')->insert( [ 'name' => 'Current liabilities' ] );
        // DB::table('account_category_type')->insert( [ 'name' => 'Non-current liabilities' ] );
        // DB::table('account_category_type')->insert( [ 'name' => 'Owner\'s equity'  ] );
        // DB::table('account_category_type')->insert( [ 'name' => 'Income' ] );
        // DB::table('account_category_type')->insert( [ 'name' => 'Other income' ] );
        // DB::table('account_category_type')->insert( [ 'name' => 'Cost of sales' ] );
        // DB::table('account_category_type')->insert( [ 'name' => 'Expenses' ] );
        // DB::table('account_category_type')->insert( [ 'name' => 'Other expense' ] );
    
        DB::table( 'account_category_type' )->insert( [ 'name' => 'Asset' ] );
        DB::table( 'account_category_type' )->insert( [ 'name' => 'Liability' ] );
        DB::table( 'account_category_type' )->insert( [ 'name' => 'Owner\'s Equity' ] );
        DB::table( 'account_category_type' )->insert( [ 'name' => 'Operating Revenue' ] );
        DB::table( 'account_category_type' )->insert( [ 'name' => 'Operating Expense' ] );
        DB::table( 'account_category_type' )->insert( [ 'name' => 'Non-operating Revenues and Expenses, Gains and Losses' ] );
    }
}
