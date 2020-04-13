<?php

use Illuminate\Database\Seeder;

class TransactionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('transactions')->insert([
            'from' => 1,
            'to' => 2,
            'details' => 'sample transaction',
            'money_from' => 10,
            'money_to' => 10,
            'to_currency_id' => '1',
            'from_currency_id' => '1'
        ]);

        DB::table('transactions')->insert([
            'from' => 1,
            'to' => 3,
            'details' => 'sample transaction 2',
            'money_from' => 10,
            'money_to' => 9.15,
            'to_currency_id' => '2',
            'from_currency_id' => '1'
        ]);
    }
}
