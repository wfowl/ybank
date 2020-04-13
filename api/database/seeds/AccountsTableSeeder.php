<?php

use Illuminate\Database\Seeder;

class AccountsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('accounts')->insert([
            'name' => 'John from USA',
            'balance' => 980,
            'country_id' => 1
        ]);

        DB::table('accounts')->insert([
            'name' => 'Sally from USA',
            'balance' => 2000,
            'country_id' => 1
        ]);

        DB::table('accounts')->insert([
            'name' => 'Adam from Lithuania',
            'balance' => 1000,
            'country_id' => 2
        ]);

        DB::table('accounts')->insert([
            'name' => 'Sharon from USA',
            'balance' => 2000,
            'country_id' => 2
        ]);
    }
}
