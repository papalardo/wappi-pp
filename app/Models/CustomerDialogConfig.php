<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomerDialogConfig extends Model
{
    protected $fillable = [
        'type',
        'customer_id'
    ];
}
