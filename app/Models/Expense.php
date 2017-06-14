<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    protected $table = 'expense';

    protected $fillable = [
    	'date', 'transaction_type', 'payee_id', 'payee_type', 'account_id', 'total', 'statement_memo'
    ];
}
