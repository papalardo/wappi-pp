<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Customer extends Model
{
    use Notifiable;

    protected $fillable = [
        'name',
        'last_name',
        'email',
        'phone',
    ];

    public function dialog_config() {
        return $this->hasOne(CustomerDialogConfig::class);
    }
}
