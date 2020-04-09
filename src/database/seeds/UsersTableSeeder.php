<?php

use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $defaultCoupon = [1];
        $users = [
            ['name' => 'kurenaif',
            'email' => 'kurenaif@example.com',
            'password' => 'password',
            'balance' => 1000000000,
            'admin' => True,
            'icon' => '14tzV3b9uiZis4OTbP7V3icC4LI0TDlf8xcNqPY9.png',
            'coupons' => serialize($defaultCoupon)],
            ['name' => 'fwarashi',
            'email' => 'fwarashi@example.com',
            'password' => '123456',
            'balance' => 100000,
            'admin' => False,
            'icon' => 'ZYAJ2BRHh2RDvgW2mksk1GtKcpTTZFumrakRFkqB.png',
            'coupons' => serialize($defaultCoupon)],
            ['name' => 'fkurenai',
            'email' => 'fkurenai@example.com',
            'password' => 'pass123',
            'balance' => 100000,
            'admin' => False,
            'coupons' => serialize($defaultCoupon)],
        ];
        foreach($users as $user) {
            $user['password'] = md5($user['password']);
            \App\User::create($user);
        }
    }
}
