<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(UsersTableSeeder::class);
        $this->call(GoodsTableSeeder::class);
        $this->call(CouponsTableSeeder::class);
        $this->call(ReviewsTableSeeder::class);
    }
}
