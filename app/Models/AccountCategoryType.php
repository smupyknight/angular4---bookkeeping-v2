<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AccountCategoryType extends Model
{
    protected $table = 'account_category_type';

    protected $fillable = [
    	'name'
    ];

    public function detailTypes() {
    	return $this->hasMany('App\Models\AccountDetailType', 'account_category_type_id');
    }
}
