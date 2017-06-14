<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attachment extends Model
{
    protected $table = 'attachment';

    protected $fillable = [
    	'transaction_type', 'transaction_id', 'attachment_name', 'attachment_link'
    ];
}
