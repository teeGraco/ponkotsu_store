<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Coupon extends Model
{
    protected $table = 'coupons';

    public function hasCoupon($discount) {
        return DB::table($this->table)->where('discount', $discount)->exists();
    }
}
