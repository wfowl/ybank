<?php

use Illuminate\Database\Seeder;

class CountryTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('countries')->insert([
            'name' => 'United States',
            'currency_id' => 1
        ]);

        DB::table('countries')->insert([
            'name' => 'Lithuania',
            'currency_id' => 2
        ]);
    }
}
