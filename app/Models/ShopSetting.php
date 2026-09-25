<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShopSetting extends Model
{
        protected $fillable = [
        'shop_name',
        'business_start',
        'business_end',
        'slot_minutes',
    ];
}
