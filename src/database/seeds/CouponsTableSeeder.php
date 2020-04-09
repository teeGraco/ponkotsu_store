<?php

use Illuminate\Database\Seeder;

class CouponsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $defaultCoupon = [1];
        $coupons = [
            ['discount' => 10, 'description' => 'リリース記念10%Off!'],
        ];
        foreach($coupons as $coupon) {
            \App\Coupon::create($coupon);
        }
    }
}
