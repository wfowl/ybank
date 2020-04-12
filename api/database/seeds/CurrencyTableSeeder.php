<?php

use Illuminate\Database\Seeder;

class CurrencyTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('currencies')->insert([
            'name' => 'USD',
            'symbol' => '$'
        ]);

        DB::table('currencies')->insert([
            'name' => 'EUR',
            'symbol' => '€'
        ]);
    }
}
