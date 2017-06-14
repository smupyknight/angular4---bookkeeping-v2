<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductService extends Model
{
    protected $table = 'product_service';

    protected $fillable = [
    	'name', 'sku', 'price', 'product_category_id', 'active'
    ];
}
